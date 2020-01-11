<?php

namespace MySQLDb;

class DbClass
{

	public static function createDataFromBindableParams(\mysqli_stmt $stmt, string $param_type, array $a_bind_params)
	{
		$a_params = array();
		$a_params[] = &$param_type;

		$n = count($a_bind_params);
		for ($i = 0; $i < $n; $i++) {
			$a_params[] = &$a_bind_params[$i];
		}
		call_user_func_array(array(
			$stmt,
			'bind_param',
		), $a_params);
	}

	public static function executeSql(\Mysqli $conn, String $sql, $param_type = "", array $a_bind_params = [])
	{
		$result = new DbResult();

		$stmt = $conn->prepare($sql);
		if (!$stmt) {
			throw new DbErrorConnection($conn, $sql);
		}
		if ($param_type != "") {
			self::createDataFromBindableParams($stmt, $param_type, $a_bind_params);
		}

		if (!$stmt->execute()) {
			throw new DbErrorStatement($stmt, $sql);
		}
		$stmt_result = $stmt->get_result();

		if ($stmt_result) {
			$result->checkData($stmt_result);
			$stmt_result->free_result();
		} else {
			$result->checkInsertId($stmt);
		}

		$stmt->close();
		return $result;
	}

	public static function executeSqlBulk(\Mysqli $conn, String $sql, $param_type = "", array $list = [])
	{
		$stmt = $conn->prepare($sql);
		if (!$stmt) {
			print_r($conn);
			throw new DbErrorConnection($conn, $sql);
		}

		foreach ($list as $value) {
			if ($param_type != "") {
				self::createDataFromBindableParams($stmt, $param_type, $value);
			}
			if (!$stmt->execute()) {
				throw new DbErrorStatement($stmt, $sql);
			}
		}

		$stmt->close();
		return true;
	}

	public static function executeSqlLongdata(\Mysqli $conn, String $sql, $param_type = "", array $a_bind_params = [], String $file_path)
	{
		$result = new DbResult();

		$stmt = $conn->prepare($sql);
		if (!$stmt) {
			throw new DbErrorConnection($conn, $sql);
		}
		if ($param_type != "") {
			self::createDataFromBindableParams($stmt, $param_type, $a_bind_params);
		}

		$fp = fopen($file_path, "r");
		while (!feof($fp)) {
			$stmt->send_long_data(strpos($param_type, 'b'), fread($fp, 8192));
		}
		fclose($fp);

		if (!$stmt->execute()) {
			throw new DbErrorStatement($stmt, $sql);
		}
		$stmt_result = $stmt->get_result();

		if ($stmt_result) {
			$result->checkData($stmt_result);
			$stmt_result->free_result();
		} else {
			$result->checkInsertId($stmt);
		}

		$stmt->close();
		return $result;
	}
}
