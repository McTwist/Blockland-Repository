<?php

namespace App\Validation\Rules;

use App\Validation\Rule;
use Illuminate\Support\MessageBag;

class TypeRule implements Rule
{
	public function validate(MessageBag & $messages)
	{
		if (func_num_args() <= 1)
			return;
		$addon = func_get_arg(1);

		$type = $addon->Type();
		if (empty($type))
		{
			$messages->add('type_missing', "Type is missing.");
		}
	}
}
