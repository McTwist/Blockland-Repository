<?php

namespace App\Http\Requests;

class UpdateAddon extends Request
{
	/**
	 * The current Add-On we are dealing with.
	 *
	 * @var Addon
	 */
	public $addon;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$this->addon = Addon::fromSlug($this->route()->getParameter('addon'));
		if ($this->addon === null)
			return false;
		return $this->addon->isOwner(Auth::user());
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'title' => 'required|max:64|unique:addons,name,'.$this->addon->id,
			'summary' => 'required',
			'authors' => 'required'
		];
	}
}
