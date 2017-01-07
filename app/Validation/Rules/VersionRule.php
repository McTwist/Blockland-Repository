<?php

namespace App\Validation\Rules;

use App\Validation\Rule;
use Illuminate\Support\MessageBag;

class VersionRule implements Rule
{
	public function validate(MessageBag & $messages)
	{
		if (func_num_args() <= 1)
			return;
		$addon = func_get_arg(1);
		
		if (!$addon->ValidateVersion())
		{
			$messages->add('version_invalid', "Version is invalid and needs to be corrected");
		}
	}
}
