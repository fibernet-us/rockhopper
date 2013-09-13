<?php

/**
 * ****************************************************************************
 *
 * Class Passcrypt 
 * 
 * Provide password enscryption and verification routines 
 * (use Blowfish if available, system default otherwise)
 * 
 * @author   Wen Bian
 * @version  1.00
 * @history
 *   09/12/2013: created.
 *                              
 */
class Passcrypt {
	
	// tells crypt to use blowfish for 5 rounds.
	private static $blowfishPre = '$2a$05$';
	private static $blowfishEnd = '$';
	
	// valid characters for salts.
	private static $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
	private static $lenChars = 63;
	private static $lenSalt = 21;
	
	// enscrype a given password with a random salt 
	// and return both the hashed password and the salt
	public static function hashPassword($password) {
		if(! CRYPT_BLOWFISH) {
			return Array(crypt($password), 'default');
		}
		
		$salt = "";
		
		for($i = 0; $i < self::$lenSalt; $i ++) {
			$salt .= self::$validChars[mt_rand(0, self::$lenChars)];
		}
		
		$bcrypt_salt = self::$blowfishPre . $salt . self::$blowfishEnd;
		
		$hashed_password = crypt($password, $bcrypt_salt);
		
		return Array (
			$hashed_password,
			$salt 
		);
	}
	
	// check if a plain password matches its hashed version
	public static function verifyPassword($password, $passhash, $salt) {     
		if(! CRYPT_BLOWFISH) {
			return crypt($password, $passhash) == $passhash;
		}
		
	    $hash = crypt($password, self::$blowfishPre . $salt . self::$blowfishEnd);
	    return  $hash == $passhash;
	}
	
}
