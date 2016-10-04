<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Addon;
use App\Models\Channel;
use App\Models\Category;
use App\Repository\Blockland\Addon\File as AddonFile;
use App\Jobs\VerifyAddon;

/*
|--------------------------------------------------------------------------
| Addon Controller
|--------------------------------------------------------------------------
|
| The Addon Controller handles the Addon Model as a Resource, and
| utilizes the CRUD process to manage Addons. All Routes that deal
| with Addons are funnelled through this Controller, or an API.
|
*/
class AddonController extends Controller
{
	// File locations
	private static $temp_path = 'app/tmp';
	private static $repo_path = 'app/repo';

	public function __construct()
	{
		// Limiting everything on the addon
		$this->middleware('auth', [
			'except' => 'show'
		]);

		// Make sure repo folder is created
		$path = storage_path(self::$repo_path);
		if (!file_exists($path))
			mkdir($path);
	}

	/**
	 * Upload the Resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 *
	 * @return \Illuminate\Http\Response, \Illuminate\Http\Redirect
	 */
	public function upload(Request $request)
	{
		$this->validate($request, [
			'addon' => 'required|mimes:zip|min:1|max:50000' // 50MB
		]);

		if (!$request->session()->has('upload'))
		{
			$file = $request->file('addon');

			$originalName = $file->getClientOriginalName();
			$tmpName = time().'.temp.'.$originalName;
			$name = time().'.'.$originalName;

			$file->move(storage_path(self::$temp_path), $name);

			// Store data to be used
			$data = [];
			$data['filename'] = $name;
			$data['originalFilename'] = $originalName;

			$request->session()->flash('upload', $data);

			//$this->dispatchFrom(VerifyAddon::class, ['file' => $tmpName]);
		}

		// Decide where to go
		if ($request->ajax())
		{
			return response()->json(['url' => route('addon.create')]);
		}
		else
		{
			return redirect()->intended(route('addon.create'));
		}
		
	}

	/**
	 * Show the Form for Creating a new Resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 *
	 * @return \Illuminate\Http\Response, \Illuminate\Http\Redirect
	 */
	public function create(Request $request)
	{
		// Denied
		if (!$request->session()->has('upload'))
		{
			return redirect()->intended(route('pages.home'));
		}
		$data = $request->session()->get('upload');
		$error = $request->session()->get('error', array());
		$request->session()->reflash();

		// Ensure its existence
		$addon_file = new AddonFile(storage_path(self::$temp_path).'/'.$data['filename']);
		if (!$addon_file->IsOpen())
		{
			return redirect()->intended(route('pages.home'));
		}

		// TODO: Do checks, validations and generations and notify the user

		$categories = Category::listSelect();

		$title = $addon_file->Title();
		$summary = $addon_file->Description();
		$developers = $addon_file->Authors('');
		$description = $addon_file->Description();
		$channel = $addon_file->Channel();
		$version = $addon_file->Version();
		// Show the Create Page for Addon
		return view('resources.addon.create', compact('title', 'summary', 'developers', 'description', 'categories', 'channel', 'version', 'error'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'title' => 'required|max:64|unique:addons,name',
			'summary' => 'required',
			'developers' => 'required',
			'description' => 'required',
			'category' => 'integer|exists:categories,id'
		]);

		$data = $request->session()->get('upload');

		// Get all inputs
		$category = $request->input('category');
		$title = $request->input('title');
		$summary = $request->input('summary');
		$developers = $request->input('developers');
		$description = $request->input('description');
		$channel = $request->input('channel');
		$version = $request->input('version');

		// TODO: Generate a valid slug
		// Note: A valid slug is depending on the status on the add-on. Private is a string id instead
		$slug = str_slug(pathinfo($data['originalFilename'], PATHINFO_FILENAME), '_');
		// Create the Resource
		$addon = Addon::create([
			'name' => $title,
			'slug' => $slug,
			'description' => $description
		]);
		// Link them together
		Category::find($category)->addons()->save($addon);
		$request->user()->addons()->save($addon);
		// Create channel
		$channel_obj = Channel::Create([
			'name' => $channel,
			'slug' => $slug,
			'description' => $description
		]);
		$addon->channels()->save($channel_obj);

		$temp_file = storage_path(self::$temp_path).'/'.$data['filename'];
		$save_file = storage_path(self::$repo_path).'/'.$data['originalFilename'];

		// Update addon data
		$file = new AddonFile($temp_file);
		if ($file->IsOpen())
		{
			if ($file->Title() != $title)
				$file->Title($title);
			if ($file->Authors() != $developers)
				$file->Authors($developers);
			if ($file->Description() != $summary)
				$file->Description($summary);
			if ($file->Channel() != $channel)
				$file->Channel($channel);
			if ($file->Version() != $version)
				$file->Version($version);
			// Save changes!
			$file->GenerateDescription(true);
			$file->GenerateNamecheck(true);
			$file->GenerateVersion(true, true, true);
			$file->Cleanup();
		}
		// Save archive!
		$file->Close();

		// Move to correct place
		@rename($temp_file, $save_file);
		// Note: Removes for now, but fix later
		//unlink($temp_file);

		// Redirect to the addon page
		return redirect()->intended(route('addon.show', $addon->slug));
	}

	/**
	 * Display the specified Resource.
	 *
	 * @param  $addon  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show($addon)
	{
		$addon = Addon::fromSlug($addon);
		// Show the Category Page
		return $addon === null ? view('errors.404') : view('resources.addon.show', compact('addon'));
	}

	/**
	 * Show the Form for Editing the specified Resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  $addon  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $addon)
	{
		$addon = Addon::fromSlug($addon);

		if ($addon === null)
		{
			return view('errors.404');
		}

		if (!$addon->isOwner($request->user()))
		{
			return view('errors.403');
		}

		// Get categories
		$categories = Category::listSelect();

		$summary = $addon->description;
		$developers = $addon->authors();

		// Show the Edit Page for Addon
		return view('resources.addon.edit', compact('addon', 'categories', 'summary', 'developers'));
	}

	/**
	 * Update the specified Resource in Storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  $addon  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $addon)
	{
		$addon = Addon::fromSlug($addon);

		if ($addon === null)
		{
			return redirect()->intended(route('pages.home'));
		}

		if ($addon->isOwner($request->user()))
		{
			$this->validate($request, [
				'title' => 'required|max:64|unique:addons,name,'.$addon->id,
				'summary' => 'required',
				'developers' => 'required',
				'description' => 'required'
			]);

			// Update the Addon
			$addon->name = $request->input('title');
			$addon->description = $request->input('description');

			// Save the Addon
			$addon->save();
		}

		// Redirect to the Index Page
		return redirect()->intended(route('addon.show', $addon->slug));
	}

	/**
	 * Remove the specified Resource from Storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  $addon  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $addon)
	{
		$addon = Addon::fromSlug($addon);

		$category = $addon->category_id;
		
		if ($addon->isOwner($request->user()))
		{
			$addon->owners()->detach();
			// Delete related channels
			$addon->channels()->delete();
			// Delete the Addon
			$addon->delete();
		}

		// Redirect to the Index Page
		return redirect()->intended(route('categories.show', $category));
	}
}
