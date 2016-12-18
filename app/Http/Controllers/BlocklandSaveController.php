<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

use App\Models\Repository;
use App\Models\Channel;
use App\Models\Version;
use App\Models\Category;
use App\Models\RepositoryType;
use App\Models\File as FileModel;
use App\Models\VersionCache;
use App\Http\Requests\UploadFile;
use App\Http\Requests\StoreSave;
use App\Http\Requests\UpdateSave;

use Composer\Semver\Comparator;

class BlocklandSaveController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// Limiting everything on the controller
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

		// TODO: Do some validation

		// Flash the data for next request
		$data = [
			'path' => $path,
			'original' => $orig_name,
			'attributes' => [
				'display_name' => basename($orig_name, '.'.$ext),
				'size' => $file->getClientSize(),
				'extension' => $ext,
				'mime' => $file->getClientMimeType()
			]
		];
		$request->session()->flash('upload', $data);

		// Decide where to go
		if ($request->ajax())
		{
			return response()->json(['url' => route('save.create')]);
		}
		else
		{
			return redirect()->intended(route('save.create'));
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

		// TODO: Do checks, validations and generations and notify the user

		$categories = Category::listSelect('save');
		$category = Category::getIdByAddonGroup('saves');

		// Get values
		$title = basename($original, '.bls');

		// Show the Create Page for Save
		return view('resources.save.create', compact('title', 'categories', 'category', 'error'));
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  StoreSave  $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(StoreSave $request)
	{
		$data = $request->session()->get('upload');

		// Get all inputs
		$category = $request->input('category');
		$title = $request->input('title');
		$summary = $request->input('summary');
		$authors = $request->input('authors');
		$description = $request->input('description', '');
		$channel = $request->input('channel');
		$version = $request->input('version');

		// TODO: Verify the values

		// TODO: Generate a valid slug
		// Note: A valid slug is depending on the status on the save. Private is a string id instead
		$slug = str_slug($data['attributes']['display_name'], '_');

		// Create the Resource
		$save = Repository::create([
			'name' => $title,
			'slug' => $slug,
			'description' => $description
		]);

		// Attach to type
		$save->type()->associate(RepositoryType::where('name', 'save')->first());

		// Link them together
		Category::find($category)->repositories()->save($save);

		// Attach to user
		$save->owners()->save($request->user());

		// Update channel with newer data
		$channel_obj = $save->channel;
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
		$cache->crc = 0;
		$cache->save();

		// Make the file model
		$file_obj = FileModel::import($data['path'], $data['attributes']);

		// Associate with user
		$file_obj->uploader()->associate($request->user());

		// Save file with the Save
		$save->version->file()->save($file_obj);

		// Flush data to file
		$save->flush();

		// Redirect to the save page
		return redirect()->intended(route('save.show', $save->slug));
	}

	/**
	 * Display the specified Resource.
	 *
	 * @param  $save  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show($save)
	{
		$save = Repository::fromSlug($save);
		// Show the Category Page
		return $save === null ? view('errors.404') : view('resources.save.show', compact('save'));
	}

	/**
	 * Show the Form for Editing the specified Resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  $save  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $save)
	{
		$save = Repository::fromSlug($save);

		if ($save === null)
		{
			return abort(404);
		}

		if (!$save->isOwner($request->user()))
		{
			return abort(403);
		}

		$title = $save->name;
		$summary = $save->summary;
		$authors = $save->authors;
		$channel = $save->channel->name;
		$version = $save->version->name;

		// Update file
		if ($request->session()->has('upload'))
		{
			$data = $request->session()->get('upload');

			$temp_file = temp_path($data['path']);
			$original = $data['original'];

			// TODO: Verify file

			// Get values
			$title = basename($original, '.bls');
		}

		// Get categories
		$categories = Category::listSelect('save');
		$error = $request->session()->get('error', null);

		// Keep everything for now
		$request->session()->reflash();

		// Show the Edit Page for Save
		if ($request->session()->has('upload'))
		{
			return view('resources.save.update', compact('save', 'title', 'summary', 'authors', 'channel', 'version', 'error', 'categories'));
		}
		else
		{
			return view('resources.save.edit', compact('save', 'title', 'summary', 'authors', 'channel', 'version', 'error', 'categories'));
		}
	}

	/**
	 * Update the specified Resource in Storage.
	 *
	 * @param  UpdateSave  $request
	 * @param  $save  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(UpdateSave $request, $save)
	{
		$save = $request->save;

		$title = $request->input('title');
		$summary = $request->input('summary');
		$authors = $request->input('authors');
		$description = $request->input('description', '');

		// Update the file with a new version
		if ($request->session()->has('upload'))
		{
			$data = $request->session()->get('upload');

			$channel_name = $request->input('channel');
			$version_name = $request->input('version');

			$channel_obj = $save->channels()->where('name', $channel_name)->first();
			// Create new channel
			if (!$channel_obj)
			{
				$channel_obj = $save->createChannel($channel_name);

				// Update the version
				$version_obj = $channel_obj->version;
				if (!empty($version_name))
					$version_obj->name = $version_name;
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
			$cache = new VersionCache;
			$cache->version()->associate($version_obj);
			$cache->summary = $summary;
			$cache->authors = $authors;
			$cache->crc = 0;
			$cache->save();

			// Make the file model
			$file_obj = FileModel::import($data['path'], $data['attributes']);

			// Associate with user
			$file_obj->uploader()->associate($request->user());

			// Save file with the Addon
			$save->version->file()->save($file_obj);
		}

		// Update the Addon
		$save->name = $title;
		$save->description = $description;
		$save->summary = $summary;
		$save->authors = $authors;

		// Save the Addon
		$save->push();

		// Redirect to the Index Page
		return redirect()->intended(route('addon.show', $addon->slug));
	}

	/**
	 * Remove the specified Resource from Storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  $save  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $save)
	{
		$save = Repository::fromSlug($save);

		$category = $save->category_id;
		
		if ($save->isOwner($request->user()))
		{
			// Remove all owners
			$save->owners()->detach();

			// Delete the save
			$save->delete();
		}

		// Redirect to the Index Page
		return redirect()->intended(route('categories.show', $category));
	}
}
