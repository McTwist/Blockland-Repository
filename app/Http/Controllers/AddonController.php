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
	public function show($id)
	{
		$addon = \App\Addon::find($id);

		return view('addon', compact('addon'));
	}
}
