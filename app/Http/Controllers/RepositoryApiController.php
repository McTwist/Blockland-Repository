<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Addon;

class RepositoryApiController extends Controller
{
	// Default page with GET method
	public function home(Request $request)
	{
		$pretty = isset($request->pretty);
		if ($request->has('mods'))
		{
			return $this->mods($request->get('mods'), $pretty);
		}
		elseif ($request->has('mod'))
		{
			return $this->mod($request->get('mod'), $pretty);
		}
		elseif ($request->has('repo'))
		{
			return $this->repository($request->get('repo'), $pretty);
		}
		elseif ($request->has('dl'))
		{
			return $this->download($request->get('dl'));
		}
		else
		{
			return $this->repository('', $pretty);
		}
	}

	// Display all mods in a list
	public function mods($data, $pretty = null)
	{
		$mods = self::ExtractData($data, $data);
		$addon_slugs = explode('-', $mods);
		$addons = Addon::whereIn('slug', $addon_slugs)->get();
		$obj = self::ObjectFromArray($addons);
		return self::json($obj, isset($pretty) || self::IsPretty($data));
	}

	// Display only one mod
	public function mod($data, $pretty = null)
	{
		$mod = self::ExtractData($data, $data);
		$addon = Addon::where('slug', $mod)->first();
		$obj = self::ObjectFromArray([$addon]);
		return self::json($obj, isset($pretty) || self::IsPretty($data));
	}

	// Display a certain repository and all its add-ons
	public function repository($data, $pretty = null)
	{
		$repo = self::ExtractData($data, $data);
		$addons = Addon::all();
		$obj = self::ObjectFromArray($addons);
		return self::json($obj, isset($pretty) || self::IsPretty($data));
	}

	// Download the mod specified
	public function download($data)
	{
		$mod = self::ExtractData($data, $data);
		$addon = Addon::where('slug', $mod)->first();
		return response('Under construction: '.$addon->name);
		//return response()->download($addon->file(), $addon->filename(), 'application/zip');
	}

	// Extract data and attributes from string
	private static function ExtractData($data, &$attributes)
	{
		$attributes = explode('&', $data);
		return array_shift($attributes);
	}

	// Check if data is pretty
	private static function IsPretty(array $data)
	{
		return count($data) > 0 && in_array('pretty', $data);
	}

	// Get object out from array of addons
	static private function ObjectFromArray($addons)
	{
		$obj = new \stdClass;
		$obj->addons = [];
		foreach ($addons as $addon)
		{
			$obj->addons[] = self::ObjectFromAddon($addon);
		}
		return $obj;
	}

	// Get object out from addon
	static private function ObjectFromAddon(Addon $addon)
	{
		$obj = new \stdClass;
		$obj->name = $addon->name;
		$obj->id = $addon->slug;
		$obj->description = $addon->description;
		$obj->channels = [];
		// Channels
		foreach ($addon->channels() as $channel)
		{
			$obj->channels[] = self::ObjectFromChannel($channel);
		}

		return $obj;
	}

	// Get object out from channel
	static private function ObjectFromChannel(Channel $channel)
	{
		$obj = new \stdClass;
		$obj->name = $channel->name;
		$obj->id = $channel->slug;
		$obj->version = '1.0';

		if (!empty($channel->description))
			$obj->description = $channel->description;

		$restart_required = $channel->restart_required();
		if (!empty($restart_required))
			$obj->restart_required = $restart_required;

		$obj->file = '';
		$obj->changelog = '';
		return $obj;
	}

	static private function json($data, $pretty = false)
	{
		return response()->json((object)$data, 200, array(), self::json_flags($pretty));
	}

	static private function json_flags($pretty = false)
	{
		return ($pretty ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES;
	}
}
