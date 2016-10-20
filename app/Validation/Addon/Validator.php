<?php
/*
 * Orginally made by Boom
 * Modified by McTwist
 */

namespace App\Validation\Addon;

use Illuminate\Contracts\Validation\Validator as ValidatorBase;
use Illuminate\Support\MessageBag;
use Symfony\Component\Translation\TranslatorInterface;

use App\Validation\Rules\NamecheckRule;
use App\Validation\Rules\DescriptionRule;
use App\Validation\Rules\VersionRule;
use App\Validation\Rules\RequiredFilesRule;
use App\Validation\Rules\ScriptsRule;

use App\Repository\Blockland\Addon\File as AddonFile;

class Validator implements ValidatorBase
{
	//////////////////
	//* Attributes *//
	//////////////////
	/**
	 * The Translator to handle locale information.
	 *
	 * @var TranslatorInterface
	 */
	protected $translator;

	/**
	 * The MessageBag to handle messages.
	 *
	 * @var MessageBag
	 */
	protected $messages;

	/**
	 * The path where the addon is localized.
	 *
	 * @var string
	 */
	protected $path;

	protected $realname = null;

	/**
	 * The rules to be checked.
	 *
	 * @var array
	 */
	protected $rules = [
		NamecheckRule::class,
		DescriptionRule::class,
		VersionRule::class,
		RequiredFilesRule::class,
		ScriptsRule::class,
	];

	/**
	 * The rules that was failed
	 *
	 * @var array
	 */
	protected $failedRules = [];

	public function __construct(TranslatorInterface $translator, $path, $realname=null)
	{
		$this->translator = $translator;
		$this->path = $path;
		$this->realname = $realname;
	}

	public function passes()
	{
		$this->messages = new MessageBag;

		$addon = new AddonFile($this->realname);

		if ($addon->Open($this->path))
		{
			foreach ($this->rules as $rule)
			{
				$rule = app()->make($rule);

				$rule->validate($this->messages, $addon);
			}

			$addon->Abort();
		}
		else
		{
			$this->messages->add('file', "Unable to open archive: {$this->path}");
		}

		return count($this->messages->all()) === 0;
	}

	/**
	 * Determine if the data fails the validation rules.
	 *
	 * @return bool
	 */
	public function fails()
	{
		return !$this->passes();
	}

	/**
	 * Get the failed validation rules.
	 *
	 * @return array
	 */
	public function failed()
	{
		return $this->failedRules;
	}
	
	/**
	 * Add conditions to a given field based on a Closure.
	 *
	 * @param  string  $attribute
	 * @param  string|array  $rules
	 * @param  callable  $callback
	 * @return void
	 */
	public function sometimes($attribute, $rules, callable $callback)
	{
		$payload = new Fluent($this->attributes());

		if (call_user_func($callback, $payload)) {
			foreach ((array) $attribute as $key) {
				if (Str::contains($key, '*')) {
					$this->explodeRules([$key => $rules]);
				} else {
					$this->mergeRules($key, $rules);
				}
			}
		}
	}

	/**
	 * After an after validation callback.
	 *
	 * @param  callable|string  $callback
	 * @return $this
	 */
	public function after($callback)
	{
		$this->after[] = function () use ($callback) {
			return call_user_func_array($callback, [$this]);
		};

		return $this;
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
