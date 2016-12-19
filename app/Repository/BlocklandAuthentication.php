<?php

namespace App\Repository;

/*
 * Blockland Authentication
 * Several ways to authenticate the user
 */

class BlocklandAuthentication
{
	// Validates the username through the authentication server
	// This works fine for normal queries, but it fails for the following:
	// * If the user have never logged in or is using an another computer
	//   to authenticate
	// * If there is more than one on the same IP they could lie about
	//   their identity, if not worse, someone got a hold of the IP and
	//   spoof their own with that one
	// Note: This is blocking, so might take several seconds to finish
	static public function CheckAuthServer($username, $user_agent = true)
	{
		// Prepare username
		$username = mb_convert_encoding(urldecode($username), 'ISO-8859-1');
		// Due to how Blockland auth server works, it is required to do just some of it manually
		$username = str_replace('%', '%25', $username);
		$encodeChars = array(' ', '@', '$', '&', '?', '=', '+', ':', ',', '/');
		$encodeValues = array('%20', '%40', '%24', '%26', '%3F', '%3D', '%2B', '3A','%2C', '%2F');
		$username = str_replace($encodeChars, $encodeValues, $username);

		// Prepare data query
		// Note: If this IP is invalid, it will check the connected IP
		$data = "NAME={$username}&IP={$_SERVER['REMOTE_ADDR']}";
		$length = strlen($data);

		// Get user agent
		$agent = '';
		if ($user_agent)
		{
			$revision = self::GetBlocklandRevision();
			if ($revision !== false)
			{
				$agent = "User-Agent: Blockland {$revision}\r\n";
			}
		}

		// Prepare http request
		$options = [];
		$options['http'] = [];
		$options['http']['method'] = 'POST';
		$options['http']['header'] = "Connection: close\r\n{$agent}Content-Type: application/x-www-form-urlencoded\r\nContent-Length: {$length}\r\n";
		$options['http']['content'] = $data;

		$context = stream_context_create($options);

		// Get data from auth server
		$result = file_get_contents('http://auth.blockland.us/authQuery.php', false, $context);

		// Cannot connect to server
		if ($result === false)
			return null;

		$array = explode(' ', trim($result));
		// Invalid authentication
		if ($array[0] === 'NO')
		{
			return false;
		}
		// This could happen too and is considered invalid
		elseif (!is_numeric($array[1]))
		{
			return false;
		}
		return (int)$array[1];
	}

	// Validates through the forum
	// A user account exist on the forum that sends an authentication
	// request to the user. The user then approves it and reply to the
	// authentication account for it to store its credentials.
	// This might be tedious for the user, but it is valid as a two-way
	// communication is being done.
	static public function CheckForum($username)
	{
		// TODO: Create this
	}

	// Get the current revision released
	// Connects to the development board and looks for threads with
	// revision number in them. Then gets the newest one of them.
	static protected function GetBlocklandRevision()
	{
		// Prepare http request
		$options = [];
		$options['http'] = [];
		$options['http']['method'] = 'GET';
		$options['http']['header'] = "Connection: close\r\n";

		$context = stream_context_create($options);

		// Get content from Development board
		$content = file_get_contents('http://forum.blockland.us/index.php?board=41.0;wap', false, $context);

		// Locate revision numbers
		preg_match_all("/[^0-9a-zA-Z](r[0-9]{4})[^0-9a-zA-Z]/", $content, $result);

		// Avoid possible errors that could arise
		if (count($result) != 2 || count($result[1]) == 0)
			return false;

		// Get result and sort it
		$result = $result[1];
		$result = rsort($result);

		return $result[0];
	}
}

?>
