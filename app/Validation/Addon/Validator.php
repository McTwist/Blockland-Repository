<?php

namespace App\Validation\Addon;

use Illuminate\Support\MessageBag;
use Symfony\Component\Translation\TranslatorInterface;

use App\Validation\Rules\NamecheckRule;

class Validator
{
	protected $translator;

	protected $messages;

	protected $path;

	protected $rules = [
		NamecheckRule::class
	];

	protected $failedRules = [];

	public function __construct(TranslatorInterface $translator, $path)
	{
		$this->translator = $translator;
		$this->path = $path;
	}

	public function passes()
	{
		$this->messages = new MessageBag;

		foreach ($this->rules as $rule)
		{
			$rule = app()->make($rule);

			$rule->validate($this->messages);
		}

		return count($this->messages->all()) === 0;
	}

	public function fails()
	{
		return !$this->passes();
	}

	public function failed()
	{
		return $this->failedRules;
	}

	public function messages()
	{
		if (!$this->messages)
			$this->passes();

		return $this->messages;
	}

	public function errors()
	{
		return $this->messages();
	}

	public function getMessageBag()
	{
		return $this->messages();
	}
}
