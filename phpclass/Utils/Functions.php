<?php

namespace Utils;

class Functions
{
	public static function returnOut(array $options = [])
	{
		$_settings = array_replace_recursive([
			'status' => 'ok',
			'msg' => '',
			'color' => 'info',
		], $options);

		return $_settings;
	}

	public static function trimSeveral(string $str)
	{
		return trim(preg_replace('/(?:\s\s+|\n|\t)/', ' ', $str));
	}

	public static function setCookie(array $options = [])
	{
		$_settings = array_replace_recursive([
			'name' => 'cookie',
			'value' => 'cookie',
			'expires' => time() + 60 * 60 * 24 * 30, // 30 days,
			'path' => '/',
			'domain' => '',
			'secure' => true,
			'httponly' => true,
			'samesite' => 'Strict'
		], $options);

		setcookie(
			$_settings['name'],
			$_settings['value'],
			array(
				'expires' => $_settings['expires'],
				'path' => $_settings['path'],
				'domain' => $_settings['domain'],
				'secure' => $_settings['secure'],
				'httponly' => $_settings['httponly'],
				'samesite' => $_settings['samesite'],
			)
		);
	}
}
