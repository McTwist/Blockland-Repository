<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\UploadFile;

class UploadController extends Controller
{
	/**
	 * Upload the Resource.
	 *
	 * @param  UploadFile  $request
	 *
	 * @return \Illuminate\Http\Response, \Illuminate\Http\Redirect
	 */
	public function upload(UploadFile $request)
	{
		// Note: This is not the best way of doing it, but currently the only way.
		// It's not good that you cannot pick controller depending on file uploaded.
		$file = $request->file('file');
		switch ($file->getClientOriginalExtension())
		{
		case 'zip':
			$controllerName = AddonController::class;
			break;
		case 'bls':
			$controllerName = BlocklandSaveController::class;
			break;
		default:
			// Only way to throw a validation error
			$this->validate($request, [
				'file' => 'mimes:zip,bls'
			]);
			break;
		}
		$controller = app()->make($controllerName);
		return $controller->callAction('upload', [$request]);
	}
}
