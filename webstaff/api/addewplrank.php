<?php

$sql = "SELECT rankupdatable, `uuid`
FROM mc_players
WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'i',
	$_POST['mc_nameid']
);
$stmt->execute();
if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if($data[0]['rankupdatable'] == 'N'){
	$out['status'] = "no";
	$out['color'] = "danger";
	$out['msg'] = "No se puede asignar rango a este jugador. Ya tiene uno.";
} else {
	
	require '../class/PermissionsEX.php';
	
	$sql = "UPDATE mc_players SET rankid = ?, rankuntil = FROM_UNIXTIME(?), canal_yt = ?, rankupdatable = 'N' WHERE `id` = ?;";
	$stmt = $conn->prepare($sql);
	if($stmt===false){ die( $conn->error ); }
	$stmt->bind_param( 'iisi',
		$_POST['mc_rankid'],
		$_POST['expire'],
		$_POST['canal_yt'],
		$_POST['mc_nameid']
	);
	$stmt->execute();
	$stmt->close();
	
	PermissionsEX::setRank($conn,$data[0]['uuid'],$_POST['mc_name'],$_POST['mc_rank']);
	
	shell_exec('sudo -u '._MC_SYSTEMUSER.' screen -r '._SCREENALERTS.' -X stuff "send '.$_POST['mc_name'].' prelobby\015"');
	sleep(1);
	shell_exec('sudo -u '._MC_SYSTEMUSER.' screen -r '._SCREENALERTS.' -X stuff "i '.$_POST['mc_name'].' Se te ha asignado un rango '.$_POST['mc_rank'].'. Usa /lobby para volver a entrar.\015"');
	
	$out['status'] = "ok";
	$out['color'] = "success";
	$out['msg'] = "El rango del jugador se ha asignado correctamente.";
	
	/** TRACKING **/
	$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha asignado un nuevo rango ".$conn->escape_string($_POST['mc_rank'])." al jugador ".$conn->escape_string($_POST['mc_name']).".');";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
	$stmt->execute();
	$stmt->close();
	/** TRACKING **/
}
?>