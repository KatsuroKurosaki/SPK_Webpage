<?php

namespace MySQLDb;

class DbErrorStatement extends \Exception
{

	private $stmt = null;
	private $sql = "";

	public function __construct(\mysqli_stmt $stmt, string $sql)
	{
		$this->stmt = $stmt;
		$this->sql = $sql;
		parent::__construct($stmt->error, $stmt->errno);
	}

	public function __desctruct()
	{
		unset($this->stmt);
		unset($this->sql);
	}

	public function getStatement()
	{
		return $this->stmt;
	}

	public function getSql()
	{
		return $this->sql;
	}

	public function __toString()
	{
		return get_class($this) . "\nMessage: " . $this->message . "\nFile: " . $this->file . "::" . $this->line . "\n\n" . $this->getTraceAsString();
	}
}
