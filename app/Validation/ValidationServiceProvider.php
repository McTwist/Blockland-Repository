<?php

namespace App\Validation;

use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->registerAddonValidationFactory();
	}

	protected function registerAddonValidationFactory()
	{
		$this->app->singleton('app.validator.addon', function()
		{
			return new Addon\Factory($this->app['translator']);
		});
	}
}
