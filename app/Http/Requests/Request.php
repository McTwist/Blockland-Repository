<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		// Make this default as we're handling it with middleware anyway
		return true;
	}

	/**
	 * Get the validator instance for the request.
	 *
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function getValidatorInstance()
	{
		$request = $this;
		// Add after functions
		return parent::getValidatorInstance()->after(function($validator) use ($request)
		{
			if (method_exists($request, 'after'))
			{
				$request->after($validator);
			}
		});
	}
}
