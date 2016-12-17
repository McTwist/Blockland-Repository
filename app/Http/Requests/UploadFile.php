<?php

namespace App\Http\Requests;

class UploadFile extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'file' => 'required|max:50000' // 50MB
		];
	}
}
