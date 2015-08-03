<?php

require 'input.class.php';
require 'database.class.php';
require 'uid.class.php';

class Api
{
	private $input;

	public function __construct()
	{
		$this->input = new Input();

		$this->input->Process();

		UID::LoadConfig();
	}

	public function Display()
	{
		// Only allow input from GET requests
		$get = $this->input->Get();

		$pretty = isset($get['pretty']);

		$db = new Database();

		$json = '';

		// Support_Updater
		if (isset($get['mods']))
		{
			// Convert uids to ids
			$mods = array_map('UID::GetNum', explode('-', $get['mods']));

			$repository = $db->GetAddOnList($mods);
			if (isset($repository))
				$json = $repository->PrintJSON($pretty);
			else
				$json = self::Error('No add-on found', $pretty);
		}
		// Add-On
		elseif (isset($get['mod']))
		{
			// Convert uid to id
			$mod = UID::GetNum($get['mod']);
			if (count($mod) == 1)
			{
				$repository = $db->GetAddOnList(array($mod[0]));
				if (isset($repository))
					$json = $repository->PrintJSON($pretty);
			}
			if (empty($json))
				$json = self::Error('No add-on found', $pretty);
		}
		// Repository
		elseif (isset($get['repo']))
		{
			$repository = $db->GetRepository(empty($get['repo']) ? null : $get['repo']);
			if (isset($repository))
				$json = $repository->PrintJSON($pretty);
			else
				$json = self::Error('Unknown repository', $pretty);
		}
		// Default list
		else
		{
			$repository = $db->GetRepository();
			if (is_object($repository))
			{
				$rep = new stdClass();
				// Special for default repository
				if ($repository->Name() !== null)
					$rep->name = $repository->Name();
				$rep->addons = [];
				// Get all addons
				foreach ($repository->GetAddOns() as $addon)
				{
					$add = new stdClass();
					$add->name = $addon->Name();
					$add->id = UID::GetUid($addon->Id());
					$add->channels = [];

					// Get all channels
					foreach ($addon->GetChannels() as $channel)
					{
						$cha = new stdClass();
						$cha->name = $channel->Name();
						$cha->id = UID::GetUid(array($addon->Id(), $channel->Id()));

						array_push($add->channels, $cha);
					}

					array_push($rep->addons, $add);
				}

				// Encode json
				$json = json_encode($rep, ($pretty ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES);
			}
			else
				$json = self::Error('No default repository', $pretty);
		}

		// Set the type used
		header('Content-Type: application/json');

		// Display it
		echo $json;
	}

	private static function Error($msg, $pretty = false)
	{
		$obj = new stdClass();
		$obj->error = $msg;
		return json_encode((object)$obj, ($pretty ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES);
	}
}

?>