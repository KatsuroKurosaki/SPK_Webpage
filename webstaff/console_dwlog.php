<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';

$sql = "SELECT dirname FROM mc_modes WHERE id = ?;";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
	die($conn->error);
}
$stmt->bind_param(
	'i',
	$_GET['server']
);
$stmt->execute();
$modes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$file = _MC_ROOT . "/" . $modes[0]['dirname'] . "/logs/" . $_GET['log'];
if (file_exists($file)) {
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="' . basename($_GET['log']) . '"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	readfile($file);
} else {
	echo "Error 404: Log not found.";
}

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha descargado archivo de log " . $conn->escape_string($file) . "');";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
