<?php

namespace App\Validation\Addon;

use Symfony\Component\Translation\TranslatorInterface;

class Factory
{
	protected $translator;

	public function __construct(TranslatorInterface $translator)
	{
		$this->translator = $translator;
	}

	public function make($path)
	{
		return new Validator($this->translator, $path);
	}
}
