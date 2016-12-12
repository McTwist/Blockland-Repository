<?php

namespace App\Http\Requests;

use App\Models\Addon;
use Auth;

class UpdateAddon extends Request
{
	/**
	 * The current Add-On we are dealing with.
	 *
	 * @var Addon
	 */
	public $addon = null;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
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
		// TOOD: Move this out somewhere else to not dirty this space
		$this->addon = Addon::fromSlug($this->route()->getParameter('addon'));
		if ($this->addon === null)
			return [];
		return [
			'title' => 'required|max:64|unique:addons,name,'.$this->addon->id,
			'summary' => 'required',
			'authors' => 'required'
		];
	}
}