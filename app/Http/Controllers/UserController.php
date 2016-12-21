<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\BlocklandUser;
use App\Repository\BlocklandAuthentication;
use App\Http\Requests\UserUpdate;
use App\Http\Requests\ValidateAuthIP;

class UserController extends Controller
{
	/**
	 * Display the User.
	 *
	 * @param User $user user to be displayed
	 * @return \Illuminate\Http\Response, \Illuminate\Http\Redirect
	 */
	public function show(User $user = null)
	{
		if (!is_object($user) || $user->id == 0)
		{
			if (auth()->check())
			{
				$user = auth()->user();
			}
			else
			{
				return redirect()->intended(route('user.login'));
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
	 * @param  UserUpdate $request
	 * @return \Illuminate\Http\Response, \Illuminate\Http\Redirect
	 */
	public function update(UserUpdate $request)
	{
		$user = $request->user();

		// Update user info
		if ($request->has('username'))
		{
			$user->username = $request->input('username');
		}

		if ($request->has('displayname'))
		{
			$user->displayname = $request->input('displayname');
		}

		if ($request->has('email'))
		{
			$user->email = $request->input('email');
		}

		// Blockland Authentication
		if ($request->has('id') || $request->has('name'))
		{
			$id = $request->input('id');
			$name = $request->input('name');

			// Did user change Blockland name or ID?
			if ($user->blid != $id || $user->blname != $name)
			{
				// Already verified
				if (!$this->verifyBlocklandID($id, $name))
				{
					// Validate again.
					$data = $this->validateBlocklandID($user, $id, $name);

					// If verification failed.
					if ($data['code'] !== 'VERIFIED')
					{
						return redirect()->back()->withErrors([$data['code'] => $data['msg']])->withInput();
					}
				}

				// Get and update
				$blockland_user = BlocklandUser::firstOrNew(['id' => $id]);

				if ($blockland_user->name != $name)
					$blockland_user->name = $name;

				$blockland_user->save();

				$user->blockland_id = $id;
				$user->save();
			}
		}
		// Unlinked account
		else
		{
			$user->blockland_id = null;
		}

		$user->save();

		return redirect()->intended(route('user.show'));
	}

	/**
	 * Verify user through IP and name.
	 *
	 * @param  ValidateAuthIP $request
	 * @return Response
	 */
	public function validateAuthIP(ValidateAuthIP $request)
	{
		$user = $request->user();

		$id = $request->input('id');
		$name = $request->input('name');

		$verified = $this->verifyBlocklandID($id, $name);

		if (!$verified)
		{
			$data = $this->validateBlocklandID($user, $id, $name);
			$verified = $data['code'] == 'VERIFIED';
		}

		if ($verified)
		{
			$this->markBlocklandID($id, $name);
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

	/**
	 * Verify user through IP and name.
	 *
	 * @param  User   $user
	 * @param  int    $id   bl_id
	 * @param  string $name bl_name
	 * @return array
	 */
	protected function validateBlocklandID(User $user, $id, $name)
	{
		$data = [];

		$blockland_user = BlocklandUser::where('id', $id)->orderBy('updated_at', 'desc')->first();

		// Someone has already registered to it
		if ($blockland_user)
		{
			// Someone has already registered on it
			if ($blockland_user->user && $blockland_user->user->id != $user->id)
			{
				$data['msg'] = 'This ID is already verified to an another account.';
				$data['code'] = 'ALREADY_VERIFIED';
				return $data;
			}

			if (strtolower($blockland_user->name) == strtolower($name))
				$bl_id = $id;
		}
		if (!isset($bl_id))
			$bl_id = BlocklandAuthentication::CheckAuthServer($name);

		// We couldn't reach the server
		if ($bl_id === null)
		{
			$data['msg'] = 'Unable to connect to the authentication server.';
			$data['code'] = 'NO_SERVER';
		}
		// The IP does not match
		elseif ($bl_id === false)
		{
			$data['msg'] = 'Wrong IP. Has the game finished authenticating? Your name should be at the bottom in the main menu.';
			$data['code'] = 'INVALID_IP';
		}
		elseif ($id != $bl_id)
		{
			$data['msg'] = 'Wrong Blockland name or ID.';
			$data['code'] = 'INVALID_NAMEID';
		}
		else
		{
			$data['msg'] = 'Verified.';
			$data['code'] = 'VERIFIED';
		}

		return $data;
	}

	/**
	 * Check through cache our current situation.
	 *
	 * @param  int    $id   bl_id
	 * @param  string $name bl_name
	 * @return bool
	 */
	protected function verifyBlocklandID($id, $name)
	{
		// This checks it only once
		$auth = session()->pull('verify_auth', false);

		return $auth && $auth['id'] == $id && $auth['name'] == $name;
	}

	/**
	 * Mark the current situation
	 *
	 * @param  int    $id   bl_id
	 * @param  string $name bl_name
	 * @return bool
	 */
	protected function markBlocklandID($id, $name)
	{
		session('verify_auth', ['id' => $id, 'name' => $name]);
	}
}

