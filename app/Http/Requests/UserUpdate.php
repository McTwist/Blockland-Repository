<?php

namespace App\Http\Requests;

class UserUpdate extends Request
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$user = $this->user();
		return [
			'username' => 'required|max:32|unique:users,username,'.$user->id,
			'displayname' => 'required|max:128|unique:users,displayname,'.$user->id,
			'email' => 'email|max:254|unique:users,email,'.$user->id,
			'id' => 'integer|min:0|max:999999',
			'name' => 'max:24'
		];
	}
}
