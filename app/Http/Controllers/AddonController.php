<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Addon;
use App\Models\Channel;
use App\Models\Category;
use App\Models\File as FileModel;
use App\Models\VersionCache;
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
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
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
			'file' => 'required|mimes:zip|max:50000' // 50MB
		]);

		// Remove previous one
		if ($request->session()->has('upload'))
		{
			Storage::disk('temp')->remove($request->session()->get('upload')['path']);
			// Clear from session while we're at it
			$request->session()->forget('upload');
		}

		$file = $request->file('file');

		// Move to a better storage
		$path = $file->store('', 'temp');

		// Validate addon
		// TODO: Put into a job instead
		// Note: Maybe not needed as it will just complicate it for the user
		$validator = AddonValidator::make(temp_path($path), $file->getClientOriginalName());

		// Don't throw an exception. Instead, pass it to the user and try to assist by fixing it ourselves.
		if ($validator->fails())
		{
			// Check for special messages
			$critical_keys = ['required_files', 'scripts_invalid', 'type_missing'];
			$messages = $validator->messages();
			if ($messages->hasAny($critical_keys))
			{
				// Remove invalid file
				unlink(temp_path($path));
				// Get specific errors and display them
				$errors = [];
				foreach ($critical_keys as $key)
					if ($messages->has($key))
						$errors[] = $messages->get($key);
				if ($request->ajax())
				{
					return response()->json(['error' => $errors], 422);
				}
				else
				{
					$request->session()->flash('error', $messages);
					return redirect()->intended(route('addon.upload'));
				}
			}
			
			$request->session()->flash('error', $messages);
		}

		// Flash the data for next request
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
		$error = $request->session()->get('error', null);
		$request->session()->keep('upload');

		$temp_file = temp_path($data['path']);
		$original = $data['original'];

		// Ensure its existence
		$addon_file = new AddonFile($original);
		if (!$addon_file->Open($temp_file))
		{
			return redirect()->intended(route('pages.home'));
		}

		// TODO: Do checks, validations and generations and notify the user

		$categories = Category::listSelect();
		$category = Category::getByType($addon_file->Type());

		// TODO: Use the addon directly instead of values, making this easier to change
		$title = $addon_file->Title();
		$summary = $addon_file->Description();
		$authors = $addon_file->Authors('');
		$description = '';
		$channel = $addon_file->Channel();
		$version = $addon_file->Version();
		// Show the Create Page for Addon
		return view('resources.addon.create', compact('title', 'summary', 'authors', 'description', 'categories', 'category', 'channel', 'version', 'error'));
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
			'authors' => 'required',
			'description' => 'required',
			'category' => 'integer|exists:categories,id'
		]);

		$data = $request->session()->get('upload');

		// Get all inputs
		// TODO: Make it possible to send this into the addon directly
		$category = $request->input('category');
		$title = $request->input('title');
		$summary = $request->input('summary');
		$authors = $request->input('authors');
		$description = $request->input('description');
		$channel = $request->input('channel');
		$version = $request->input('version');

		// TODO: Verify the values

		// TODO: Generate a valid slug
		// Note: A valid slug is depending on the status on the add-on. Private is a string id instead
		$slug = str_slug($data['attributes']['display_name'], '_');

		// Create the Resource
		$addon = Addon::create([
			'name' => $title,
			'slug' => $slug,
			'description' => $description
		]);
		// Link them together
		Category::find($category)->addons()->save($addon);

		// Attach to user
		$addon->owners()->save($request->user());

		// Update channel with newer data
		$channel_obj = $addon->channel;
		if (!empty($channel))
			$channel_obj->name = $channel;
		$channel_obj->slug = $slug;
		$channel_obj->save();

		// Update version with newer data
		$version_obj = $channel_obj->version;
		if (!empty($version))
			$version_obj->name = $version;
		$version_obj->save();

		// Add to cache
		$cache = new VersionCache;
		$cache->version()->associate($version_obj);
		$cache->summary = $summary;
		$cache->authors = $authors;
		$cache->crc = \App\Models\Blacklist\AddonCrcBlacklist::convertTo32(crc32(file_get_contents(temp_path($data['path']))));
		$cache->save();

		// Make the file model
		$file_obj = FileModel::import($data['path'], $data['attributes']);

		// Associate with user
		$file_obj->uploader()->associate($request->user());

		// Save file with the Addon
		$addon->version->file()->save($file_obj);

		// Flush data to file
		$addon->flush();

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

		// Show the Edit Page for Addon
		return view('resources.addon.edit', compact('addon', 'categories'));
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
				'authors' => 'required'
				'description' => 'required'
			]);

			// Update the Addon
			$addon->name = $request->input('title');
			$addon->description = $request->input('description');
			$addon->summary = $request->input('summary');
			$addon->authors = $request->input('authors');

			// Save the Addon
			$addon->push();
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
			// Remove all owners
			$addon->owners()->detach();

			// Delete the Addon
			$addon->delete();
		}

		// Redirect to the Index Page
		return redirect()->intended(route('categories.show', $category));
	}
}
