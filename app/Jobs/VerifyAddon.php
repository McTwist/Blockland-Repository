<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Repository\Blockland\Addon\File as AddonFile;

class VerifyAddon extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	private $file;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($file)
	{
		$this->file = $file;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$addon = new AddonFile($this->file);

		// Initial validation
		if ($addon->Validate())
		{
			return;
		}

		// Invidual validations
		if (!$addon->ValidateDescription())
		{
			// TODO: Check what's wrong
		}

		if (!$addon->ValidateNamecheck())
		{
			$addon->GenerateNamecheck(true);
		}

		if (!$addon->ValidateVersion())
		{
			$addon->GenerateVersion(true);

			if (!$addon->ValidateVersion())
			{
				// TODO: Let the user fix it
			}
		}

		if (!$addon->ValidateScripts())
		{
			// TODO: Let the user know that the scripts is invalid(Unable to compile; Dangerous functionality; etc)
		}

		// TODO: Mark as finished
	}
}
