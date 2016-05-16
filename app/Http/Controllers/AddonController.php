<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Http\Requests\Request;

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
	 * Show the Form for Creating a new Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		// Show the Create Page for Addon
		return view('resources.addon.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		// Create the Resource
		$addon = Addon::create($request->all());

		// Redirect to the addon page
		return redirect()->intended(route('addon.show', $addon->id));
	}

	/**
	 * Display the specified Resource.
	 *
	 * @param  Addon  $addon  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show($addon)
	{
		$addon = Addon::find($addon);
		// Show the Category Page
		return view('resources.addon.show', compact('addon'));
	}

	/**
	 * Show the Form for Editing the specified Resource.
	 *
	 * @param  Addon  $addon  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Addon $addon)
	{
		// Show the Edit Page for Addon
		return view('resources.addon.edit', compact('addon'));
	}

	/**
	 * Update the specified Resource in Storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  Addon  $addon  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Addon $addon)
	{
		// Update the Addon
		$addon->fill($request->all());

		// Save the Addon
		$addon->save();

		// Redirect to the Index Page
		return redirect()->intended(route('addon.show', $addon->id));
	}

	/**
	 * Remove the specified Resource from Storage.
	 *
	 * @param  Addon  $addon  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Addon $addon)
	{
		$category = $addon->category_id;
		// Delete the Addon
		$addon->delete();

		// Redirect to the Index Page
		return redirect()->intended(route('categories.show', $category));
	}
}
