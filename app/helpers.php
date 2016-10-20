<?php

if (!function_exists('obfuscate_email'))
{
	/**
	 * Returns the obfuscated email.
	 *
	 * @param  string
	 *
	 * @return string
	 */
	function obfuscate_email($email)
	{
		return preg_replace('/(?<=..).(?=.*@)/u','*', $email);
	}
}

if (!function_exists('path'))
{
	/**
	 * Returns the combined paths.
	 *
	 * @param  string
	 *
	 * @return string
	 */
	function path()
	{
		return implode(DIRECTORY_SEPARATOR, array_filter(func_get_args()));
	}
}

if (!function_exists('temp_path'))
{
	/**
	 * Returns the path to the temp storage folder.
	 *
	 * @param  string  $path
	 *
	 * @return string
	 */
	function temp_path($path = '')
	{
		return path(Storage::disk('temp')->getAdapter()->getPathPrefix(), $path);
	}
}
