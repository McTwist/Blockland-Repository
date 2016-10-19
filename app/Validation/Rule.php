<?php

namespace App\Validation;

use Illuminate\Support\MessageBag;

interface Rule
{
	public function validate(MessageBag &$messages);
}
