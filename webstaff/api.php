<?php
$start_time = microtime(true);
require 'api_header.php';

$_in = (empty($_POST) && empty($_FILES)) ? json_decode(file_get_contents("php://input"), true) : $_POST;
$_out = \Utils\Functions::returnOut();

if ($_in !== null) {
	if (isset($_in['op'])) {
		switch ($_in['op']) {
			case 'LOGIN':
				$_out = \Utils\Functions::returnOut([
					"status" => "no",
					"msg" => "Nombre de minecraft o contraseña inválidos",
					"color" => "danger",
				]);
				break;

			default:
				$_out = \Utils\Functions::returnOut([
					"status" => "no",
					"msg" => "Received operation not found.",
					"color" => "warning",
					"code" => -1,
				]);
		}
	} else {
		$_out = \Utils\Functions::returnOut([
			"status" => "ko",
			"msg" => "NO operation received.",
			"color" => "danger",
			"code" => -1,
		]);
	}
} else {
	$_out = \Utils\Functions::returnOut([
		"status" => "ko",
		"msg" => "Input JSON is invalid.",
		"color" => "warning",
		"code" => -1,
	]);
}

header('Server-Timing: time;desc="Execution time ms";dur=' . round(microtime(true) - $start_time, 3) * 1000 . ', memalloc;desc="Memory allocated: ' . memory_get_usage() . ' bytes", mempeak;desc="Peak memory: ' . memory_get_peak_usage() . ' bytes"');
echo json_encode($_out);
