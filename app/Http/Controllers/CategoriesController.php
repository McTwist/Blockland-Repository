<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Http\Requests\Request;

/*
|--------------------------------------------------------------------------
| Categories Controller
|--------------------------------------------------------------------------
|
| The Categories Controller handles the Category Model as a Resource, and
| utilizes the CRUD process to manage Categories. All Routes that deal
| with Categories are funnelled through this Controller, or an API.
|
*/
class CategoriesController extends Controller
{
	/**
	 * Display a Listing of the Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// Determine all of the Categories
		$categories = Category::all();

		// Show the Index Page for Categories
		return view('resources.categories.index', compact('categories'));
	}

	/**
	 * Show the Form for Creating a new Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		// Show the Create Page for Categories
		return view('resources.categories.create');
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
		$category = Category::create($request->all());

		// Redirect to the Index Page
		return redirect()->intended(route('categories.index'));
	}

	/**
	 * Display the specified Resource.
	 *
	 * @param  Category  $category  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show(Category $category)
	{
		// Show the Category Page
		return view('resources.categories.show', compact('category'));
	}

	/**
	 * Show the Form for Editing the specified Resource.
	 *
	 * @param  Category  $category  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Category $category)
	{
		// Show the Edit Page for Categories
		return view('resources.categories.edit', compact('category'));
	}

	/**
	 * Update the specified Resource in Storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  Category  $category  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Category $category)
	{
		// Update the Category
		$category->fill($request->all());

		// Save the Category
		$category->save();

		// Redirect to the Index Page
		return redirect()->intended(route('categories.index'));
	}

	/**
	 * Remove the specified Resource from Storage.
	 *
	 * @param  Category  $category  The specified Resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Category $category)
	{
		// Delete the Category
		$category->delete();

		// Redirect to the Index Page
		return redirect()->intended(route('categories.index'));
	}

	/**
	 * Get list of tags depending on search.
	 *
	 * @param  string  $tags  The specified tag to look for.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function tags($tag=null)
	{
		$tag = strtolower($tag);
		$tags = Tag::where('name', 'LIKE', "%{$tag}%")->get();
		return response()->json(['tags' => $tags->pluck('name')->toArray()]);
	}

	/**
	 * Get list of authors depending on search.
	 *
	 * @param  string  $authors  The specified author to look for.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function authors($author=null)
	{
		$author = strtolower($author);
		$authors = Author::where('name', 'LIKE', "%{$author}%")->get();
		return response()->json(['authors' => $tags->pluck('name')->toArray()]);
	}
}
