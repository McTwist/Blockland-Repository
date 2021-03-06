<?php

namespace App\Validation\Rules;

use App\Validation\Rule;
use Illuminate\Support\MessageBag;

class NamecheckRule implements Rule
{
	public function validate(MessageBag & $messages)
	{
		if (func_num_args() <= 1)
			return;
		$addon = func_get_arg(1);
		
		if (!$addon->hasNamecheck)
		{
			$messages->add('namecheck_missing', "Does not contain a namecheck.");
		}
		elseif (!$addon->namecheck->Validate())
		{
			$messages->add('namecheck_invalid', "Namecheck is invalid.");
		}
	}
}
