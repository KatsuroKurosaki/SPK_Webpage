<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
ini_set('log_errors', true);
ini_set('error_log', __DIR__ . '/errors.log');
error_reporting(E_ALL);

require __DIR__ . '/../phpclass/autoload.php';

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
	throw new \ErrorException($errstr, $errno, E_ERROR, $errfile, $errline);
});

set_exception_handler(function ($ex) {
	die(json_encode([
		'status' => 'ko',
		'msg' => $ex->getMessage(),
		'data' => [
			'exception' => get_class($ex),
			'code' => $ex->getCode(),
			'file' => $ex->getFile(),
			'line' => $ex->getLine(),
			'trace' => $ex->getTrace(),
		],
		'color' => 'danger',
	]));
});

register_shutdown_function(function () {
	$error = error_get_last();
	if ($error !== null) {
		die(json_encode([
			'status' => 'ko',
			'msg' => $error['message'],
			'data' => [
				'code' => $error['type'],
				'file' => $error['file'],
				'line' => $error['line'],
			],
			'color' => 'danger',
		]));
	}
});
