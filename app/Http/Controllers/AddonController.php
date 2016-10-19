<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Addon;
use App\Models\Channel;
use App\Models\Category;
use App\Models\File as FileModel;
use App\Repository\Blockland\Addon\File as AddonFile;
use App\Jobs\VerifyAddon;
use Storage;
use AddonValidator;

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
	public function __construct()
	{
		// Limiting everything on the addon
		$this->middleware('auth', [
			'except' => 'show'
		]);
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

			/*// Verify the addon
			$addon_file = new AddonFile(storage_path(self::$temp_path).'/'.$name);

			$errors = array();

			// Invidual validations
			if (!$addon_file->ValidateDescription())
			{
				// TODO: Check what's wrong
				$errors[] = array('code' => 'INVALID_DESC', 'message' => 'Description is not valid');
			}

			if (!$addon_file->ValidateNamecheck())
			{
				// Create the file
				$addon_file->GenerateNamecheck(true);
			}

			if (!$addon_file->ValidateVersion())
			{
				$addon_file->GenerateVersion(true);

				if (!$addon_file->ValidateVersion())
				{
					// TODO: Let the user fix it
					$errors[] = array('code' => 'FIX_VERSION', 'message' => 'Was not able to fix the version file');
				}
			}

			if (!$addon_file->ValidateScripts())
			{
				// TODO: Let the user know that the scripts is invalid(Unable to compile; Dangerous functionality; etc)
				$errors[] = array('code' => 'INVALID_SCRIPTS', 'message' => 'Scripts are not valid');
			}

			if (!$addon_file->HasRequiredFiles())
			{
				// TODO: Let the user know that files are missing
				$errors[] = array('code' => 'INVALID_ADDON', 'message' => $addon_file->Type().' type is in need of more files to be valid');
			}

			$addon_file->Close();

			if (count($errors))
				$request->session()->flash('error', $errors);*/

			// Move to a better storage
			$path = $file->store('', 'temp');

			// Validate addon
			// TODO: Put into a job instead
			// Note: Maybe not needed as it will just complicate it for the user
			$validator = AddonValidator::make(Storage::disk('temp')->getDriver()->getAdapter()->getPathPrefix().$path);

			if ($validator->fails())
				$this->throwValidatorException($request, $validator);

			$data = [
				'path' => $path,
				'original' => $file->getClientOriginalName(),
				'attributes' => [
					'display_name' => basename($file->getClientOriginalName(), '.zip'),
					'size' => $file->getClientSize(),
					'extension' => $file->guessClientExtension(),
					'mime' => $file->getClientMimeType()
				]
			];
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

		$temp_file = storage_path('app\\temp\\').$data['path'];
		$original = $data['original'];

		// Ensure its existence
		$addon_file = new AddonFile($temp_file, $original);
		if (!$addon_file->IsOpen())
		{
			return redirect()->intended(route('pages.home'));
		}

		// TODO: Do checks, validations and generations and notify the user

		$categories = Category::listSelect();

		// TODO: Use the addon directly instead of values, making this easier to change
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
		// TODO: Make it possible to send this into the addon directly
		$category = $request->input('category');
		$title = $request->input('title');
		$summary = $request->input('summary');
		$developers = $request->input('developers');
		$description = $request->input('description');
		$channel = $request->input('channel');
		$version = $request->input('version');

		// Make the file model
		$file_obj = FileModel::import($data['path'], $data['attributes']);

		// TODO: Generate a valid slug
		// Note: A valid slug is depending on the status on the add-on. Private is a string id instead
		$slug = str_slug($file_obj->display_name, '_');
		// Create the Resource
		$addon = Addon::create([
			'name' => $title,
			'slug' => $slug,
			'description' => $description
		]);
		// Link them together
		Category::find($category)->addons()->save($addon);

		// Save file with the Addon
		$addon->version()->file()->save($file_obj);

		// Attach to user
		$addon->owners()->save($request->user());
		//$request->user()->addons()->save($addon);

		// Update channel with newer data
		$channel_obj = $addon->channel();
		if (!empty($channel))
			$channel_obj->name = $channel;
		$channel_obj->slug = $slug;
		$channel_obj->save();

		// Update version with newer data
		$version_obj = $channel_obj->version();
		if (!empty($version))
			$version_obj->name = $version;
		$version_obj->save();

		$temp_file = storage_path('app\\uploads\\').$file_obj->path;

		// Update addon data
		$file = new AddonFile($temp_file, $file_obj->download_name);
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
			// Delete the Addon
			$addon->delete();
		}

		// Redirect to the Index Page
		return redirect()->intended(route('categories.show', $category));
	}
}
