<?php
/*
 * Blockland Authentication
 * Several ways to authenticate the user
 */

class Auth
{
	// Validates the username through the authentication server
	// This works fine for normal queries, but it fails for the following:
	// * If the user have never logged in or is using an another computer
	//   to authenticate
	// * If there is more than one on the same IP they could lie about
	//   their identity, if not worse, someone got a hold of the IP and
	//   spoof their own with that one
	// Note: This is blocking, so might take several seconds to finish
	static public function CheckAuthServer($username)
	{
		// Prepare data query
		$post = [];
		$post['NAME'] = $username;
		$post['IP'] = $_SERVER['REMOTE_ADDR'];
		$data = http_build_query($post);

		// Prepare http request
		$options = [];
		$options['http'] = [];
		$options['http']['method'] = 'POST';
		$options['http']['header'] = 'Content-Type: application/x-www-form-urlencoded';
		$options['http']['content'] = $data;

		$context = stream_context_create($options);

		// Get data from auth server
		$result = file_get_contents('http://auth.blockland.us/authQuery.php', false, $context);

		// Cannot connect to server
		if ($result === false)
			return null;

		$array = explode(' ', trim($result));
		if ($array[0] === 'YES')
			return (int)$array[1];
		// Invalid authentication
		return false;
	}
}

?>