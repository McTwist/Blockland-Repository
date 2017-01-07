<?php

namespace App\Validation\Rules;

use App\Validation\Rule;
use Illuminate\Support\MessageBag;

class InfoRule implements Rule
{
	public function validate(MessageBag & $messages)
	{
		if (func_num_args() <= 1)
			return;
		$addon = func_get_arg(1);
		
		if ($addon->isSpeedkart)
		{
			if (!$addon->hasInfo)
			{
				$messages->add('info_missing', "Missing credits.txt");
			}
			elseif (!$addon->info->Validate())
			{
				$messages->add('info_invalid', "Invalid credits.txt");
			}
		}
		else
		{
			if (!$addon->hasInfo)
			{
				$messages->add('info_missing', "Missing description.txt");
			}
			elseif (!$addon->info->Validate())
			{
				$messages->add('info_invalid', "Invalid description.txt");
			}
		}
	}
}
