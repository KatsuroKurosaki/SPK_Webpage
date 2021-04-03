<?php

if($_POST['id'] != 0){
	$sql = "SELECT id, playername, mc_mode, message FROM mc_helpop WHERE id > ? LIMIT 10;";
	$stmt = $conn->prepare($sql);
	if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
	$stmt->bind_param( 'i',
		$_POST['id']
	);
	$stmt->execute();
	if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
	$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	$stmt->close();
	$out['messages']=array();
	if(count($data)>0){
		foreach($data as $k=>$v){
			$out['id'] = $v['id'];
			$out['messages'][] = "'".$v['playername']."' en '".$v['mc_mode']."' dice:".$v['message'];
		}
	} else {
		$out['id'] = $_POST['id'];
	}
} else {
	$sql = "SELECT id FROM mc_helpop ORDER BY id DESC LIMIT 1;";
	$stmt = $conn->prepare($sql);
	if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
	/*$stmt->bind_param( 'i',
		$_POST['server']
	);*/
	$stmt->execute();
	if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
	$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	$stmt->close();
	$out['id'] = $data[0]['id'];
	$out['messages']=array();
}

$out['status'] = "ok";
?>