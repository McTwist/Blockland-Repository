<?php

namespace App\Validation\Addon;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade
{
	protected static function getFacadeAccessor()
	{
		return 'app.validator.addon';
	}
}
