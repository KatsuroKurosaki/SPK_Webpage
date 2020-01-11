<?php

namespace MySQLDb\db_name;

class DbConnection extends \MySQLDb\DbClass
{

	static $_instance;
	private $conn;

	public function __construct()
	{
		$this->checkConnection();
	}

	public function __destruct()
	{
	}

	private function __clone()
	{
	}

	public static function getInstance()
	{
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function createConnection()
	{
		mysqli_report(MYSQLI_REPORT_STRICT);

		$this->conn = mysqli_init();
		$this->conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, DbConf::DB_TIMEOUT);
		try {
			$this->conn->real_connect(DbConf::DB_SERVER, DbConf::DB_USER, DbConf::DB_PASS, DbConf::DB_BD, DbConf::DB_PORT);
		} catch (\Error $e) {
			throw new \MySQLDb\DbErrorConnection($this->conn, "");
		} catch (\Exception $e) {
			throw new \MySQLDb\DbErrorConnection($this->conn, "");
		}

		if (!isset($this->conn) or $this->conn == null) {
			throw new \Exception("Error BD ", -10);
		} else {
			$this->conn->query("SET NAMES " . DbConf::DB_CHARSET . ";");
			$this->conn->query("SET collation_connection = " . DbConf::DB_COLLATION . ";");
		}
	}

	private function checkConnection()
	{
		if ($this->conn == null || !mysqli_ping($this->conn)) {
			$this->createConnection();
		}
	}

	public function getConnection()
	{
		return $this->conn;
	}

	public static function beginTransaction()
	{
		self::getInstance()->getConnection()->begin_transaction();
	}

	public static function commit()
	{
		self::getInstance()->getConnection()->commit();
	}

	public static function rollback()
	{
		self::getInstance()->getConnection()->rollback();
	}

	public static function execute(String $sql, string $param_type = "", array $a_bind_params = [])
	{
		return parent::executeSql(self::getInstance()->getConnection(), $sql, $param_type, $a_bind_params);
	}

	public static function executeBulk(String $sql, string $param_type = "", array $list = [])
	{
		return parent::executeSqlBulk(self::getInstance()->getConnection(), $sql, $param_type, $list);
	}

	public static function executeLongdata(String $sql, $param_type = "", array $a_bind_params = [], String $file_path)
	{
		return parent::executeSqlLongdata(self::getInstance()->getConnection(), $sql, $param_type, $a_bind_params, $file_path);
	}
}
