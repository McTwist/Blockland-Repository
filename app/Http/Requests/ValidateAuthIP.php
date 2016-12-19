<?php

namespace App\Http\Requests;

class ValidateAuthIP extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'id' => 'required|integer|min:0|max:999999',
			'name' => 'required|max:24'
		];
	}
}
