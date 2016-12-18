<?php

namespace App\Http\Requests;

use App\Models\Repository;
use Auth;

class UpdateSave extends Request
{
	/**
	 * The current Add-On we are dealing with.
	 *
	 * @var Repository
	 */
	public $save = null;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		if ($this->save === null)
			return false;
		return $this->save->isOwner(Auth::user());
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		// TOOD: Move this out somewhere else to not dirty this space
		$this->save = Repository::fromSlug($this->route()->getParameter('save'));
		if ($this->save === null)
			return [];
		return [
			'title' => 'required|max:64|unique:repositories,name,'.$this->save->id,
			'summary' => 'required',
			'authors' => 'required'
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
