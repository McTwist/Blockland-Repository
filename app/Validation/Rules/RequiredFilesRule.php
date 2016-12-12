<?php

namespace App\Validation\Rules;

use App\Validation\Rule;
use Illuminate\Support\MessageBag;

class RequiredFilesRule implements Rule
{
	public function validate(MessageBag & $messages)
	{
		if (func_num_args() <= 1)
			return;
		$addon = func_get_arg(1);
		
		if (!$addon->ValidateType())
		{
			$type = $addon->type;
			$messages->add('required_files', "Required files are missing from type '{$type}'.");
		}
	}
}
