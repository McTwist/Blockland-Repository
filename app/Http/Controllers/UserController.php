<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\BlocklandUser;
use App\Repository\BlocklandAuthentication;

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

	/**
	 * Verify user through IP and name
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return Response
	 */
	public function validateAuthIP(Request $request)
	{
		$this->validate($request, [
			'id' => 'required|min:0|max:999999',
			'name' => 'required|max:24'
			]);

		$data = [];
		if ($request->has('name'))
		{
			$id = $request->input('id');
			$name = $request->input('name');

			$blockland_user = BlocklandUser::where('id', $id)->orderBy('updated_at')->first();

			if (is_object($blockland_user) && $blockland_user->name == $name)
				$bl_id = $id;
			else
				$bl_id = BlocklandAuthentication::CheckAuthServer($name);

			if ($bl_id === null)
			{
				$data['msg'] = 'Unable to authenticate';
				$data['code'] = 'NO_SERVER';
			}
			elseif ($bl_id === false)
			{
				$data['msg'] = 'Invalid ip';
				$data['code'] = 'INVALID';
			}
			else
			{
				if ($id != $bl_id)
				{
					$data['msg'] = 'Invalid name and id';
					$data['code'] = 'INVALID';
				}
				else
				{
					$data['msg'] = 'Verified';
					$data['code'] = 'VERIFIED';

					$id = $bl_id;

					if (!is_object($blockland_user))
						$blockland_user = new BlocklandUser(compact(['id', 'name']));
					elseif ($blockland_user->name != $name)
						$blockland_user->name = $name;

					$blockland_user->save();

					$user = $request->user();
					$user->blockland_id = $id;
					$user->save();
				}
			}
		}
		else
		{
			$data['msg'] = 'Missing required "name" field';
			$data['code'] = 'MISSING_FIELD';
		}


		if ($request->ajax())
		{
			return response()->json((object)$data);
		}
		else
		{
			return response($data['msg']);
		}
	}
}

