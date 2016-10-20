<?php

namespace App\Validation\Rules;

use App\Validation\Rule;
use Illuminate\Support\MessageBag;

class DescriptionRule implements Rule
{
	public function validate(MessageBag & $messages)
	{
		if (func_num_args() <= 1)
			return;
		$addon = func_get_arg(1);
		
		if (!$addon->ValidateDescription())
		{
			$messages->add('description_invalid', "Invalid description.");
		}
	}
}
