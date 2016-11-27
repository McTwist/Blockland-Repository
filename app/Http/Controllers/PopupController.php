<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class PopupController extends Controller
{
	/**
	 * Gets the view for logging in.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return string HTML containing the view
	 */
	public function getLoginView(Request $request)
	{
		if ($request->ajax())
		{
			return view('auth.loginpopup');
		}
		else
		{
			return view('auth.login');
		}
	}

	/**
	 * Gets the view for uploading a new add-on.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return string HTML containing the view
	 */
	public function getUploadAddonView(Request $request)
	{
		if ($request->ajax())
		{
			return view('resources.addon.upload');
		}
		else
		{
			// TODO: Standalone add-on upload page.
			return view('errors.404');
		}
	}
}

