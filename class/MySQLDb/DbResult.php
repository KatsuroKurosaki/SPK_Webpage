<?php

namespace MySQLDb;

class DbResult
{

	private $num_rows = 0;
	private $data = array();
	private $insertId = -1;

	public function setNumRows(int $value)
	{
		$this->num_rows = $value;
	}

	public function setData(array $value)
	{
		$this->data = $value;
	}

	public function getNumRows()
	{
		return $this->num_rows;
	}

	public function getData()
	{
		return $this->data;
	}

	public function getSingleData()
	{
		return ($this->getNumRows() > 0) ? $this->data[0] : null;
	}

	public function getInsertId()
	{
		return $this->insertId;
	}

	public function checkData(\Mysqli_result $value)
	{
		$this->setNumRows($value->num_rows);
		if ($this->getNumRows() > 0) {
			$this->setData($value->fetch_all(MYSQLI_ASSOC));
		}
	}

	public function checkInsertId(\mysqli_stmt $stmt)
	{
		$this->insertId = $stmt->insert_id;
	}
}
