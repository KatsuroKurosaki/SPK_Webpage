<?php
//require './class/MinecraftPing.php';
//require './class/MinecraftPingException.php';
require './class/xPaw/MinecraftQuery.php';
require './class/xPaw/MinecraftQueryException.php';

/*$sql = "SELECT ip, port, queryport
FROM mc_modes
WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'i',
	$_POST['srv']
);
$stmt->execute();
if($stmt->error){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
//$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // Get all rows
$data = $stmt->get_result()->fetch_assoc(); // Get 1 row because SQL query returned 1 row and we know it
//$insert_id = $stmt->insert_id; // Get inserted AUTO_INCREMENT
$stmt->close();*/

$out['status'] = "ok";
//if($data != NULL){
/*try {
		$q = new xPaw\MinecraftPing($data['ip'],$data['port']);
		$out['query'] = $q->Query();
		$q->Close();
	} catch( xPaw\MinecraftPingException $e ) {
		$out['status'] = "ko";
		$out['message'] = $e->getMessage();
	}*/

try {
	$q = new xPaw\MinecraftQuery();
	//$q->Connect($data['ip'], $data['queryport']);
	$q->Connect('127.0.0.1', 51100);
	$out['info'] = $q->GetInfo();
	if ($q->GetPlayers() == 0) {
		$out['players'] = array();
	} else {
		$out['players'] = $q->GetPlayers();
		sort($out['players'], SORT_NATURAL | SORT_FLAG_CASE);
	}
} catch (xPaw\MinecraftQueryException $e) {
	$out['status'] = "ko";
	$out['message'] = $e->getMessage();
}
//} else {
//$out['status'] = "ko";
//$out['reason'] = "No data available to show.";
//}
