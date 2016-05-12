<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class RepositoryApiController extends Controller
{
	// TODO: Implement each and all of the methods (Need database for that)
	// Default page with all add-ons for default repository
	public function home(Request $request)
	{
		if ($request->has('mods'))
		{
			return $this->mods($request->get('mods'));
		}
		elseif ($request->has('mod'))
		{
			return $this->mod($request->get('mod'));
		}
		elseif ($request->has('repo'))
		{
			return $this->repository($request->get('repo'));
		}
		else
		{
			return self::json(
				(object)[
					"addons"=>
					[
						(object)[
							"name"=>"Stu//ff",
						]
					]
				], isset($request->pretty));
		}
	}

	// Display all mods in a list
	public function mods($mods)
	{
		return self::json((object)[$mods]);
	}

	// Display only one mod
	public function mod($mod)
	{
		return self::json((object)[$mod]);
	}

	// Display a certain repository and all its add-ons
	public function repository($repo)
	{
		return self::json((object)[$repo]);
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
