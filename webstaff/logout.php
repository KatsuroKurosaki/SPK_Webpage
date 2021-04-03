<?php
require '../cnf.php';
require '../common.php';

$sql = "DELETE FROM web_session_staff WHERE session = ?;";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
	die($conn->error);
}
$stmt->bind_param(
	's',
	$_GET['s']
);
$stmt->execute();
if ($stmt === false) {
	die($stmt->error);
}
$stmt->close();
unset($_COOKIE['SpkStaffSession']);
setcookie("SpkStaffSession", "", time() - 3600);
header("Location: login.php");
