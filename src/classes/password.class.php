<?php
// Create and verify passwords
// Made to be compatible with previous hashes
// It handles following compatibiliy issues
// * Functions
// * Hash methods
// * Iterations
// * Salt
// Where the first one defines the rest
// For now it supports the folliwng:
// * hash
// * crypt
// * pbkdf2
// Each function supports internally respective defined hash algorithms

class Password
{
	// Statics, default
	public static $separator = ';';
	public static $salt_size = 24;
	public static $hash_size = 24;
	
	// Enum
	const HASH_TYPE = 0;
	const HASH_ALGORITHM = 1;
	const HASH_ITERATIONS = 2;
	const HASH_SALT = 3;
	const HASH_CRYPT = 4;
	const HASH_COUNT = 5;
	
	// Settings
	public $hash_function = 'pbkdf2';
	public $hash_algorithm = 'sha256';
	public $hash_iterations = 5000;
	
	// Create the password
	public function Create($password)
	{
		$salt = self::CreateSalt(self::$salt_size);
		
		// Put everything together
		return self::CreateCompound(
			$this->hash_function,
			$this->hash_algorithm,
			$this->hash_iterations,
			$salt,
			self::Hash(
				$this->hash_function,
				$this->hash_algorithm,
				$password,
				$salt,
				$this->hash_iterations,
				self::$hash_size
				)
			);
	}
	
	// Validate a password
	public static function Validate($password, $string)
	{
		if (!self::IsValidCompound($string))
			return false;
		
		// Get values
		list($func, $algorithm, $iterations, $salt, $hash) = self::SplitCompound($string);
		
		// Hash the input
		$hash2 = self::Hash($func, $algorithm, $password, $salt, $iterations, self::$hash_size);
		
		// Validate
		return self::ValidateHash($func, $hash, $hash2);
	}

	// Check if password requires rehashing
	public static function NeedsRehash($string, $obj)
	{
		if (!self::IsValidCompound($string))
			return false;
		if (!is_a($obj, 'Password'))
			return false;
		
		// Get values
		list($func, $algorithm, $iterations, $salt, $hash) = self::SplitCompound($string);

		// Check for function, algorithm, iterations and hash size
		return $func !== $obj->hash_function
			|| $algorithm !== $obj->hash_algorithm
			|| $iterations !== $obj->iterations
			|| strlen(base64_decode($hash)) !== self::$hash_size;
	}
	
	// Create hash string for compatibility
	private static function CreateCompound($func, $algorithm, $count, $salt, $hash)
	{
		return self::$separator.$func.self::$separator.$algorithm.self::$separator.$count.self::$separator.$salt.self::$separator.$hash;
	}
	
	// Split compound string to its components
	private static function SplitCompound($compound)
	{
		return explode($compound[0], substr($compound, 1));
	}
	
	// Is a valid compound
	private static function IsValidCompound($hash)
	{
		if (substr_count(substr($hash, 1), $hash[0]) + 1 != self::HASH_COUNT)
			return false;
		return true;
	}
	
	// Get a real random salt
	private static function CreateSalt($max_len = null)
	{
		// mcrypt
		if (extension_loaded('mcrypt'))
			$rand = mcrypt_create_iv(self::$salt_size, MCRYPT_DEV_URANDOM);
		// openssl
		elseif (extension_loaded('openssl'))
		{
			$is_strong = true;
			$rand = openssl_random_pseudo_bytes(self::$salt_size, $is_strong);
			// If it's not strong enough, then issue default one
			if (!$is_strong)
				unset($rand);
		}
		// Default
		if (empty($rand))
			$rand = mt_rand();
		
		$rand = base64_encode($rand);

		return ($max_len === null || $max_len <= 0) ? $rand : substr($rand, 0, $max_len);
	}
	
	// Hash a a password
	// Contains several hashing functions
	private static function Hash($func, $algorithm, $password, $salt, $count, $max_len)
	{
		switch($func)
		{
		// Internal method, told to work but maybe most insecure
		case 'hash':
			return base64_encode(hash($algorithm, $salt.$password, true));
		// Internal method, but could be insecure
		case 'crypt':
			switch(strtolower($algorithm))
			{
				case 'md5': $s = '$1$'.$salt.'$'; break;
				case 'blowfish': $s = '$2y$11$'.$salt.'$'; break;
				default:
				case 'sha256': $s = '$5$rounds='.$count.'$'.$salt.'$'; break;
				case 'sha512': $s = '$6$rounds='.$count.'$'.$salt.'$'; break;
			}
			$str = crypt($password, $s);
			return end(explode('$', $str));
		// Told to be most secure but it's either 3rd party library
		// or you require PHP 5.5 or higher
		case 'pbkdf2':
			return base64_encode(hash_pbkdf2($algorithm, $password, $salt, $count, $max_len, true));
		}
		// As wrong is generated, it's bad to do anything at all
		return null;
	}
	
	// Validate hash if correct
	private static function ValidateHash($func, $a, $b)
	{
		switch($func)
		{
		default:
		case 'crypt':
			return self::SlowEquals($a, $b);
		case 'hash':
		case 'pbkdf2':
			return self::SlowEquals(base64_decode($a), base64_decode($b));
		}
	}
	
	// Slow, length-constant equals
	private static function SlowEquals($a, $b)
	{
		$len1 = strlen($a);
		$len2 = strlen($b);
		$diff = $len1 ^ $len2;
		for ($i = 0; $i < $len1 && $i < $len2; $i++)
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		return $diff === 0;
	}
};
?>