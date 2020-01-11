<?php

namespace Crypt;

class Password
{

	public static function hashPassword($password)
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}

	public static function checkPassword($password, $hash)
	{
		return password_verify($password, $hash);
	}

	public static function passwordRehash($hash)
	{
		return password_needs_rehash($hash, PASSWORD_DEFAULT);
	}
}
