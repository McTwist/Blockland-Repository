<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ViewController extends Controller
{
	public function index()
	{
		return view('index', ['categories' => []]);
	}

	public function category($category)
	{
		return view('category', ['addons' => [\App\Addon::find(1)]]);
	}

	public function addon($addon)
	{
		$add = \App\Addon::find(1);
		return view('addon', ['addon' => $add]);
	}
}
