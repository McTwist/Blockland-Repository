<?php

namespace App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| Addon Controller
|--------------------------------------------------------------------------
|
| The Addon Controller handles the addon pages
|
*/

class AddonController extends Controller
{
	public function show()
	{
		$addon = \App\Addon::find(1);

		return view('addon', compact('addon'));
	}
}
