<?php

namespace MySQLDb\db_name;

class DbConf
{

	public function __construct()
	{
	}

	const DB_SERVER = 'p:127.0.0.1';
	const DB_USER = 'user';
	const DB_PASS = '';
	const DB_BD = 'db_name';
	const DB_PORT = 3306;
	const DB_TIMEOUT = 5;
	const DB_CHARSET = "utf8mb4";
	const DB_COLLATION = "utf8mb4_unicode_520_ci";
}
