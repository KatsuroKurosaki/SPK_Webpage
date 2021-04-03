<?php
require 'cnf.php';
require 'class/SPK.php';
error_reporting(_DEBUGLVL);
$conn = SPK\GlobalFunc::getMysqlConn(_HOST,_USER,_PASS,_DDBB);


$sql = "UPDATE web_transaction SET status = 'CANCELLED' WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){ die($conn->error); }
$stmt->bind_param( 'i',
	$_GET['txn']
);
$stmt->execute();
if($stmt === false){ die($stmt->error); }
$stmt->close();
$conn->close();

header("Location: rangos.php?s=".$_GET['s']."&id=".$_GET['id']);
?>