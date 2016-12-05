<?php

namespace App\Models\Traits;

trait FormatSize
{
	/**
	 * Calculates metric bytes prefixes.
	 *
	 * @param  int  $bytes
	 * @param  int  $precision
	 *
	 * @return string
	 */
	protected static function formatBytesMet($bytes, $precision = 2)
	{ 
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1000));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1000, $pow); // Defines metric

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	/**
	 * Calculates binary bytes prefixes.
	 *
	 * @param  int  $bytes
	 * @param  int  $precision
	 *
	 * @return string
	 */
	protected static function formatBytesBin($bytes, $precision = 2)
	{ 
		$units = array('B', 'KiB', 'MiB', 'GiB', 'TiB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= (1 << (10 * $pow)); // Defines binary

		return round($bytes, $precision) . ' ' . $units[$pow];
	}
}
