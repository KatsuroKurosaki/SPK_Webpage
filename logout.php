<?php
require 'cnf.php';
require 'class/SPK.php';
error_reporting(_DEBUGLVL);
$conn = SPK\GlobalFunc::getMysqlConn(_HOST,_USER,_PASS,_DDBB);

$sql = "DELETE FROM web_session WHERE session = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){die($conn->error);}
$stmt->bind_param('s',
	$_GET['s']
);
$stmt->execute();
if($stmt === false){die($stmt->error);}
$stmt->close();

setcookie ("SpkSession", "", 0);
header("Location: index.php");
?>
