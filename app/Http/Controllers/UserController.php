<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
	
	/*public function show(Request $request)
	{
		return $this->show($request->user());
	}*/

	public function show($user = null)
	{
		if (!is_object($user))
		{
			if (auth()->check())
			{
				$user = auth()->user();
			}
			else
			{
				return view('errors.403');
			}
		}
		return view('auth.show', compact('user'));
	}

	public function edit()
	{
		$user = auth()->user();
		return view('auth.edit', compact('user'));
	}

	public function update(Request $request)
	{
		$this->validate($request, [
			'username' => 'required|max:32|unique:users',
			'email' => 'email|max:254|unique:users',
			'bl_id' => 'integer|min:0|max:999999' // Yes, Badspot may register if he wants to
		]);

		$data = [];

		$user = $request->user();

		if ($request->has('username'))
		{
			$user->username = $request->input('username');
		}

		if ($request->has('email'))
		{
			$user->email = $request->input('email');
		}

		if ($request->has('bl_id'))
		{
			// Note: Fix this
		}

		$user->save();

		return redirect()->intended(route('user.edit'));
	}
}

