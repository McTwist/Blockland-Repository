<?php

namespace App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| Pages Controller
|--------------------------------------------------------------------------
|
| The Pages Controller is responisible for serving Static Pages to the
| User, such as the Home Page. This should server any static content
| that does not already belong to another classification or group.
|
*/

class PagesController extends Controller
{
	/**
	 * Serves the Home Page to the User.
	 *
	 * @return Response
	 */
	public function home()
	{
		// Determine the Categories to be displayed on the Page
		$categories = []; // No Categories yet!

		return view('pages.home', compact('categories'));
	}
}
