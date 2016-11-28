<?php
require_once './class/ZabbixApi.class.php';

$sql = "SELECT zbxitemid
FROM mc_modes
WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'i',
	$_POST['srv']
);
$stmt->execute();
if($stmt->error){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if($data != NULL){
	try {
		$api = new ZabbixApi\ZabbixApi;
		
		//$api->printCommunication(true);
		$api->setVerifyPeer(FALSE);
		$api->setApiUrl(_ZBXURL);
		$api->userLogin(
			array(
				'user' => _ZBXUSER,
				'password' => _ZBXPASS
			)
		);
		
		$fromts = new DateTime();
		$fromts->modify("-2Day");
		$nowts = new DateTime();
		
		$graphs = $api->historyGet(
			array(
				"output"=>"extend",
				"itemids"=>$data['zbxitemid'],
				"time_from"=>$fromts->format("U"),
				"time_till"=>$nowts->format("U"),
				"sortorder"=>"desc"
			)
		);
		
		$out['onlinegraph'] = array();
		foreach($graphs as $graph){
			array_push($out['onlinegraph'],intval($graph->value));
			$out['lastgraph']=intval($graph->value);
		}
		$out['mingraph'] = min($out['onlinegraph']);
		$out['maxgraph'] = max($out['onlinegraph']);
		$out['lastgraph'] = end($out['onlinegraph']);
		
		$out['status'] = "ok";
	} catch(Exception $e) {
		$out['status']="ko";
		$out['errormsg']=$e->getMessage();
	}
} else {
	$out['status']="ko";
	$out['msg']="Server ID Not found";
}
?>