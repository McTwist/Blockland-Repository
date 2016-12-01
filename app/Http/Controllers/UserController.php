<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\BlocklandUser;
use App\Repository\BlocklandAuthentication;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
	/**
	 * Display the User.
	 *
	 * @param User $user user to be displayed
	 * @return \Illuminate\Http\Response, \Illuminate\Http\Redirect
	 */
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

	/**
	 * Edit the User.
	 *
	 * @return \Illuminate\Http\Response, \Illuminate\Http\Redirect
	 */
	public function edit()
	{
		$user = auth()->user();

		return view('auth.edit', compact('user'));
	}

	/**
	 * Update the User.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response, \Illuminate\Http\Redirect
	 */
	public function update(Request $request)
	{
		$user = $request->user();

		// Ignore this user when checking if username and email are unique.
		$validator = Validator::make($request->all(), [
			'username' => ['required', 'max:32', Rule::unique('users')->ignore($user->id)],
			'email' => ['email', 'max:254', Rule::unique('users')->ignore($user->id)]
		]);

		if ($request->has('username'))
		{
			$user->username = $request->input('username');
		}

		if ($request->has('email'))
		{
			$user->email = $request->input('email');
		}

		if ($request->has('id') || $request->has('name'))
		{
			// Did user change Blockland name or ID?
			if ($user->blid != $request->input('id') || $user->blname != $request->input('name'))
			{
				// TODO: Remove Blockland user link if fields are empty.
				// Validate again.
				$data = $this->validateAuthIP($request, true);

				// If verification failed.
				if ($data['code'] !== 'VERIFIED')
				{
					if ($data['code'] == 'INVALID_IP')
					{
						$data['msg'] = 'Wrong IP. Has the game finished authenticating? Your name should be at the bottom in the main menu.';
					}

					// Add errors messages to validator.
					$validator->getMessageBag()->add('name', $data['msg']);

					return Redirect::back()->withErrors($validator)->withInput();
				}
			}
		}

		$user->save();

		return redirect()->intended(route('user.show'));
	}

	/**
	 * Verify user through IP and name.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return Response
	 */
	public
	function validateAuthIP(Request $request, $arr = false)
	{
		$this->validate($request, [
			'id' => 'required|integer|min:1|max:999999',
			'name' => 'required|max:24'
		]);

		$data = [];
		$id = $request->input('id', 0);
		if ($request->has('name') && $id > 0 && $id <= 999999)
		{
			$name = $request->input('name');

			$blockland_user = BlocklandUser::where('id', $id)->orderBy('updated_at')->first();

			if (is_object($blockland_user) && $blockland_user->name == $name)
				$bl_id = $id;
			else
				$bl_id = BlocklandAuthentication::CheckAuthServer($name);

			if ($bl_id === null)
			{
				$data['msg'] = 'Unable to connect to the authentication server.';
				$data['code'] = 'NO_SERVER';
			}
			elseif ($bl_id === false)
			{
				$data['msg'] = 'Wrong IP.';
				$data['code'] = 'INVALID_IP';
			}
			else
			{
				if ($id != $bl_id)
				{
					$data['msg'] = 'Wrong Blockland name or ID.';
					$data['code'] = 'INVALID_NAMEID';
				}
				else
				{
					$data['msg'] = 'Verified.';
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
			$data['msg'] = 'Missing a required field.';
			$data['code'] = 'MISSING_FIELD';
		}


		if ($request->ajax())
		{
			return response()->json((object)$data);
		}
		else
		{
			if ($arr)
			{
				return $data;
			}

			return response($data['msg']);
		}
	}
}

