<?php
$sql = "SELECT dirname FROM mc_modes WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'i',
	$_POST['server']
);
$stmt->execute();
if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
if(count($data)==1){
	shell_exec('sudo -u '._MC_SYSTEMUSER.' screen -r '.$data[0]['dirname'].' -X stuff "'.$_POST['c'].'\015"');
	$out['status'] = "ok";
	
	/** TRACKING **/
	$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha enviado un comando a ".$data[0]['dirname'].": ".$conn->escape_string($_POST['c'])."');";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
	$stmt->execute();
	$stmt->close();
	/** TRACKING **/
} else {
	$out['status'] = "no";
	$out['reason'] = "Error: No se ha encontrado la consola en el server.";
}
?>