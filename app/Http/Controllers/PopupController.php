<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class PopupController extends Controller
{
	/**
	 * Gets the view for logging in.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return string HTML containing the view or null if the user is already logged in
	 */
	public function getLoginView(Request $request)
	{
		// If user is logged in return null so the login is not displayed.
		if (auth()->check()) {
			return null;
		} else {
			if ($request->ajax()) {
				return view('auth.popup.login');
			} else {
				return view('auth.login');
			}
		}
	}

	/**
	 * Gets the view for uploading a new add-on.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return string HTML containing the view or null if the user is not logged in
	 */
	public function getUploadFileView(Request $request)
	{
		if (auth()->check()) {
			if ($request->ajax()) {
				return view('resources.file.popup.upload');
			} else {
				return view('resources.file.upload');
			}
		} else {
			// You need to be logged in to upload files.
			return null;
		}
	}
}

