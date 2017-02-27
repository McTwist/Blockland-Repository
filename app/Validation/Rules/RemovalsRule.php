<?php

namespace App\Validation\Rules;

use App\Validation\Rule;
use Illuminate\Support\MessageBag;

class RemovalsRule implements Rule
{
	public function validate(MessageBag & $messages)
	{
		if (func_num_args() <= 1)
			return;
		$addon = func_get_arg(1);
		
		$items = $addon->GetListOfRemovals();
		if (!empty($items))
		{
			$str = implode(', ', $items);
			$pre = (count($items) > 1) ? 'These items' : 'This item';
			$messages->add('item_removals', "{$pre} will be removed: {$str}");
		}
	}
}
