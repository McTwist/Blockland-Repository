<?php

namespace App\Http\Requests;

class StoreAddon extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'title' => 'required|max:64|unique:addons,name',
			'summary' => 'required',
			'authors' => 'required',
			'category' => 'integer|exists:categories,id'
		];
	}

	/**
	 * After validations are done.
	 *
	 * @param \Illuminate\Contracts\Validation\Validator
	 *
	 * @return void
	 */
	public function after($validator)
	{
		// Reflash to avoid the data from being removed
		if ($validator->invalid())
		{
			session()->reflash();
		}
	}
}
