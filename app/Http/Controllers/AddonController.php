<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Storage;
use Illuminate\Support\MessageBag;

use App\Models\Repository;
use App\Models\Channel;
use App\Models\Version;
use App\Models\Author;
use App\Models\Category;
use App\Models\RepositoryType;
use App\Models\Tag;
use App\Models\File as FileModel;
use App\Repository\Blockland\Addon\FileInfo;
use App\Models\AddonCache;
use App\Repository\Blockland\Addon\File as AddonFile;
use App\Http\Requests\UploadFile;
use App\Http\Requests\StoreAddon;
use App\Http\Requests\UpdateAddon;
use App\Jobs\VerifyAddon;
use AddonValidator;

use Composer\Semver\Comparator;

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
	 * @param  UploadFile  $request
	 *
	 * @return \Illuminate\Http\Response, \Illuminate\Http\Redirect
	 */
	public function upload(UploadFile $request)
	{
		// Remove previous one
		if ($request->session()->has('upload'))
		{
			Storage::disk('temp')->delete($request->session()->get('upload')['path']);
			// Clear from session while we're at it
			$request->session()->forget('upload');
		}

		$file = $request->file('file');

		// Move to a better storage
		$path = $file->store('', 'temp');
		$temp_path = temp_path($path);

		$orig_name = $file->getClientOriginalName();
		$ext = $file->getClientOriginalExtension();

		// Validate the existant of the file
		$file_obj = FileModel::fromContent(file_get_contents($temp_path), 'zip');
		if ($file_obj)
		{
			unlink($temp_path);
			$messages = new MessageBag(['file_exists' => 'The file has already been uploaded.']);
			if ($request->ajax())
			{
				return response()->json(['error' => $messages->all()], 422);
			}
			else
			{
				$request->session()->flash('error', $messages);
				return redirect()->intended(route('file.upload'));
			}
		}

		// Validate addon
		// TODO: Put into a job instead
		// Note: Maybe not needed as it will just complicate it for the user
		$validator = AddonValidator::make($temp_path, $orig_name);

		// Don't throw an exception. Instead, pass it to the user and try to assist by fixing it ourselves.
		if ($validator->fails())
		{
			// Check for special messages
			$critical_keys = ['required_files', 'scripts_invalid', 'type_missing'];
			$messages = $validator->messages();
			if ($messages->hasAny($critical_keys))
			{
				// Remove invalid file
				unlink($temp_path);
				// Get specific errors and display them
				if ($request->ajax())
				{
					$errors = [];
					foreach ($critical_keys as $key)
						if ($messages->has($key))
							$errors[] = $messages->get($key);
					return response()->json(['error' => $errors], 422);
				}
				else
				{
					$request->session()->flash('error', $messages);
					return redirect()->intended(route('file.upload'));
				}
			}
			
			$request->session()->flash('error', $messages);
		}

		// Flash the data for next request
		$data = [
			'path' => $path,
			'original' => $orig_name,
			'attributes' => [
				'display_name' => basename($orig_name, '.'.$ext),
				'size' => $file->getClientSize(),
				'extension' => $file->guessClientExtension(),
				'mime' => $file->getClientMimeType()
			]
		];
		$request->session()->flash('upload', $data);

		// Locate already existing Add-On
		$addon = Repository::fromFile($orig_name);
		if ($addon)
		{
			if ($request->ajax())
			{
				return response()->json(['url' => route('addon.edit', $addon->slug)]);
			}
			else
			{
				return redirect()->intended(route('addon.edit', $addon->slug));
			}
		}

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

		// Keep everything for now
		$request->session()->reflash();

		$temp_file = temp_path($data['path']);
		$original = $data['original'];

		// Ensure its existence
		$addon_file = new AddonFile($original);
		if (!$addon_file->Open($temp_file))
		{
			// TODO: Display an error
			return redirect()->intended(route('pages.home'));
		}

		// TODO: Do checks, validations and generations and notify the user

		$categories = Category::listSelect('addon');
		$category = Category::getIdByAddonGroup($addon_file->type);

		// TODO: Use the addon directly instead of values, making this easier to change
		$title = $addon_file->info->title;
		$summary = $addon_file->info->description;
		$authors = $addon_file->info->authorsRaw;
		$description = '';
		$channel = $addon_file->version->channel;
		$version = $addon_file->version->version;
		$addon_file->Abort();
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
	public function store(StoreAddon $request)
	{
		// Denied
		if (!$request->session()->has('upload'))
		{
			return redirect()->intended(route('pages.home'));
		}
		$data = $request->session()->get('upload');

		// Get all inputs
		// TODO: Make it possible to send this into the addon directly
		$category = $request->input('category');
		$title = $request->input('title');
		$summary = $request->input('summary');
		$authors = $request->input('authors');
		$tags = explode(',', $request->input('tags', ''));
		$description = $request->input('description', '');
		$channel = $request->input('channel');
		$version = $request->input('version');

		// TODO: Verify the values

		// TODO: Generate a valid slug
		// Note: A valid slug is depending on the status on the add-on. Private is a string id instead
		$slug = str_slug($data['attributes']['display_name'], '_');

		// Create the Resource
		$addon = Repository::create([
			'name' => $title,
			'slug' => $slug,
			'description' => $description
		]);

		// Attach to type
		$addon->type()->associate(RepositoryType::where('name', 'addon')->first());

		// Link them together
		Category::find($category)->repositories()->save($addon);

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
		if (!empty($summary))
			$version_obj->summary = $summary;
		$version_obj->save();

		// Add to cache
		$cache = new AddonCache;
		$cache->version()->associate($version_obj);
		$cache->crc = \App\Models\Blacklist\AddonCrcBlacklist::convertFileCrcTo32(temp_path($data['path']));
		$cache->save();

		// Locate all tags, and create new one if needed
		$tag_ids = [];
		foreach ($tags as $tag)
		{
			$tag_obj = Tag::firstOrCreate(['name' => strtolower(trim($tag))]);
			$tag_ids[] = $tag_obj->id;
		}
		$addon->tags()->sync($tag_ids);

		// Add authors
		$author_ids = [];
		foreach (FileInfo::str2arr($authors) as $author)
		{
			$author_obj = Author::firstOrCreate(['name' => $author]);
			$author_ids[] = $author_obj->id;
		}
		$version_obj->authors()->sync($author_ids);

		// Make the file model
		$file_obj = FileModel::import($data['path'], $data['attributes']);

		// Associate with user
		$file_obj->uploader()->associate($request->user());

		// Save file with the Addon
		$addon->version->file()->save($file_obj);

		// Flush data to file
		$addon->flush($request->input('item_removals', true),
			($request->has('namecheck_missing') && $request->input('namecheck_missing', false)) ||
			($request->has('namecheck_invalid') && $request->input('namecheck_invalid', true)));

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
		$addon = Repository::fromSlug($addon);
		// Show the Add-On Page
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
		$addon = Repository::fromSlug($addon);

		if ($addon === null)
		{
			return abort(404);
		}

		if (!$addon->isOwner($request->user()))
		{
			return abort(403);
		}

		$title = $addon->name;
		$summary = $addon->summary;
		$authors = $addon->authors->implode('name', ', ');
		$tags = $addon->tags->implode('name', ', ');
		$channel = $addon->channel->name;
		$version = $addon->version->name;

		// Update file
		if ($request->session()->has('upload'))
		{
			$data = $request->session()->get('upload');

			$temp_file = temp_path($data['path']);
			$original = $data['original'];

			$addon_file = new AddonFile($original);
			if (!$addon_file->Open($temp_file))
			{
				// TODO: Display an error
				return redirect()->intended(route('pages.home'));
			}

			$title = $addon_file->info->title;
			$authors = $addon_file->info->authorsRaw;
			$summary = $addon_file->info->description;
			$channel = $addon_file->version->channel;
			$version = $addon_file->version->version;

			$addon_file->Abort();
		}

		// Get categories
		$categories = Category::listSelect();
		$error = $request->session()->get('error', null);

		// Keep everything for now
		$request->session()->reflash();

		// Show the Edit Page for Addon
		if ($request->session()->has('upload'))
		{
			return view('resources.addon.update', compact('addon', 'title', 'summary', 'authors', 'tags', 'channel', 'version', 'error', 'categories'));
		}
		else
		{
			return view('resources.addon.edit', compact('addon', 'title', 'summary', 'authors', 'tags', 'channel', 'version', 'error', 'categories'));
		}
	}

	/**
	 * Update the specified Resource in Storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  $addon  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(UpdateAddon $request, $addon)
	{
		$addon = $request->addon;

		$title = $request->input('title');
		$summary = $request->input('summary');
		$authors = $request->input('authors');
		$tags = explode(',', $request->input('tags', ''));
		$description = $request->input('description', '');

		// Update the file with a new version
		if ($request->session()->has('upload'))
		{
			$data = $request->session()->get('upload');

			$channel_name = $request->input('channel');
			$version_name = $request->input('version');

			$channel_obj = $addon->channels()->where('name', $channel_name)->first();
			// Create new channel
			if (!$channel_obj)
			{
				$channel_obj = $addon->createChannel($channel_name);

				// Update the version
				$version_obj = $channel_obj->version;
				if (!empty($version_name))
					$version_obj->name = $version_name;
				if (!empty($summary))
					$version_obj->summary = $summary;
				$version_obj->save();
			}
			else
			{
				$vname = $channel_obj->versions->keyBy('id')->max()->name;

				// Make sure that this version is higher
				if (Comparator::greaterThanOrEqualTo($vname, $version_name))
				{
					$request->session()->reflash();
					return redirect()->back()->withErrors(['version_lower' => 'Version is lower than previous one: '."{$vname} >= {$version_name}"]);
				}

				// Create a new version
				$version_obj = $channel_obj->createVersion($version_name, true);
			}

			// Add to cache
			$cache = new AddonCache;
			$cache->version()->associate($version_obj);
			$cache->crc = \App\Models\Blacklist\AddonCrcBlacklist::convertFileCrcTo32(temp_path($data['path']));
			$cache->save();

			// Make the file model
			$file_obj = FileModel::import($data['path'], $data['attributes']);

			// Associate with user
			$file_obj->uploader()->associate($request->user());

			// Save file with the Addon
			$addon->version->file()->save($file_obj);

			// Flush data to file
			$addon->flush($request->input('item_removals', true),
				($request->has('namecheck_missing') && $request->input('namecheck_missing', false)) ||
				($request->has('namecheck_invalid') && $request->input('namecheck_invalid', true)));
		}
		else
		{
			$version_obj = $addon->version;
		}

		// Locate all tags, and create new one if needed
		$tag_ids = [];
		foreach ($tags as $tag)
		{
			$tag_obj = Tag::firstOrCreate(['name' => strtolower(trim($tag))]);
			$tag_ids[] = $tag_obj->id;
		}

		// Update the Addon
		$addon->name = $title;
		$addon->tags()->sync($tag_ids);
		$addon->description = $description;
		$version_obj->summary = $summary;

		// Add authors
		$author_ids = [];
		foreach (FileInfo::str2arr($authors) as $author)
		{
			$author_obj = Author::firstOrCreate(['name' => $author]);
			$author_ids[] = $author_obj->id;
		}
		$version_obj->authors()->sync($author_ids);

		// Save the Addon
		$addon->push();
		$addon->flush(false, false);

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
		$addon = Repository::fromSlug($addon);

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
