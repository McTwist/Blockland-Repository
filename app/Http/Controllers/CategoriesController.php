<?php

namespace App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| Category Controller
|--------------------------------------------------------------------------
|
| The Category Controller is used to handle requests toward displaying
| a certain category
|
*/

class CategoriesController extends Controller
{
	public function show()
	{
		$addons = [\App\Addon::find(1)];

		return view('category', compact('addons'));
	}
}
