<?php
require 'cnf.php';
require 'class/SPK.php';
error_reporting(_DEBUGLVL);
$conn = SPK\GlobalFunc::getMysqlConn(_HOST,_USER,_PASS,_DDBB);

$get = json_encode(array('fecha'=>date("Y-m-d H:i:s"),'get'=>$_GET,'server'=>$_SERVER));
$sql = "UPDATE web_transaction SET status = 'COMPLETE', web_return = NOW(), pg_get = ? WHERE id = ?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'si',
	$get,
	$_GET['custom']);
$stmt->execute();
$stmt->close();	
$conn->close();
echo "OK";
?>