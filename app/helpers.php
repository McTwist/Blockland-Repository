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
		$paths = array_filter(func_get_args());
		array_walk($paths, function(&$value)
		{
			$value = rtrim($value, '\\/');
			$value = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $value);
		});
		return implode(DIRECTORY_SEPARATOR, $paths);
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

if (!function_exists('bl_convert_encoding'))
{
	/**
	 * Returns a converted string from Blockland encoding to UTF-8.
	 *
	 * @param  string  $str
	 *
	 * @return string
	 */
	function bl_convert_encoding($str)
	{
		return mb_convert_encoding($str, 'UTF-8', 'Windows-1252');
	}
}
