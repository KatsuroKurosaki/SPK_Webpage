<?php
$start_time = microtime(true);
ini_set('display_errors', true);
error_reporting(-1);

set_error_handler(function ($code, $string, $file, $line) {
	throw new \ErrorException($string, null, $code, $file, $line);
});
register_shutdown_function(function () {
	$error = error_get_last();
	if ($error !== null) {
		echo json_encode(\GlobalFunctions::returnOut([
			"status" => "ko",
			"msg" => $error['message'],
			"file" => $error['file'],
			"line" => $error['line'],
			"color" => "danger",
			"code" => -1,
		]));
	}
});

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/class/autoload.php';
$_in = (empty($_POST) && empty($_FILES)) ? json_decode(file_get_contents("php://input"), true) : $_POST;
$_out = [];

if ($_in !== null) {
	if (isset($_in['op'])) {
		switch ($_in['op']) {
			case 'HELLO':
				$_out = \GlobalFunctions::returnOut([
					"status" => "ok",
					"msg" => "Hello World!",
					"color" => "success",
					"code" => 0,
				]);
				break;

			case 'UPLOAD_FILE':
				if (!empty($_FILES)) {
					move_uploaded_file($_FILES['file']['tmp_name'], './upload/' . $_FILES['file']['name']);
					$_out = \GlobalFunctions::returnOut([
						"msg" => "File uploaded.",
					]);
				} else {
					$_out = \GlobalFunctions::returnOut([
						"status" => "ko",
						"msg" => "No file received.",
						"color" => "warning",
					]);
				}
				break;

			default:
				$_out = \GlobalFunctions::returnOut([
					"status" => "no",
					"msg" => "Received operation is invalid.",
					"color" => "warning",
					"code" => -1,
				]);
		}
	} else {
		$_out = \GlobalFunctions::returnOut([
			"status" => "ko",
			"msg" => "NO operation received.",
			"color" => "danger",
			"code" => -1,
		]);
	}
} else {
	$_out = \GlobalFunctions::returnOut([
		"status" => "ko",
		"msg" => "Input JSON is invalid.",
		"color" => "warning",
		"code" => -1,
	]);
}

if (\GlobalConf::API_DEBUG) {
	$_out['mem']['engine_curr'] = memory_get_usage(false);
	$_out['mem']['system_curr'] = memory_get_usage(true);
	$_out['mem']['engine_peak'] = memory_get_peak_usage(false);
	$_out['mem']['system_peak'] = memory_get_peak_usage(true);
	$_out['time'] = round(microtime(true) - $start_time, 6);
}
echo json_encode($_out);
