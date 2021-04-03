<?php
$sql = "SELECT staff_member, `uuid`, playername
FROM mc_players
WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'i',
	$_POST['id']
);
$stmt->execute();
if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if($data[0]['staff_member'] == 'Y'){
	$out['status'] = "no";
	$out['color'] = "danger";
	$out['msg'] = "No puedes retirar el rango de un miembro del Staff.";
} else {
	
	require '../class/PermissionsEX.php';
	
	$sql = "UPDATE mc_players SET rankid = "._BASERANKID.", rankuntil = '"._MAXTS."', rankupdatable = 'Y' WHERE `id` = ?;";
	$stmt = $conn->prepare($sql);
	if($stmt===false){ die( $conn->error ); }
	$stmt->bind_param( 'i',
		$_POST['id']
	);
	$stmt->execute();
	$stmt->close();
	
	$sql = "DELETE FROM mc_players_extracmds WHERE id_mc_player = ?;";
	$stmt = $conn->prepare($sql);
	if($stmt===false){ die( $conn->error ); }
	$stmt->bind_param( 'i',
		$_POST['id']
	);
	$stmt->execute();
	$stmt->close();
	
	PermissionsEX::delRank($conn,$data[0]['uuid']);
	
	shell_exec('sudo -u '._MC_SYSTEMUSER.' screen -r '._SCREENALERTS.' -X stuff "send '.$data[0]['playername'].' prelobby\015"');
	sleep(1);
	shell_exec('sudo -u '._MC_SYSTEMUSER.' screen -r '._SCREENALERTS.' -X stuff "i '.$data[0]['playername'].' Se te ha retirado tu rango. Usa /lobby para volver a entrar.\015"');
	
	$out['status'] = "ok";
	$out['color'] = "success";
	$out['msg'] = "El rango del jugador se ha retirado correctamente.";
	
	/** TRACKING **/
	$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha retirado el rango del jugador ".$data[0]['playername']."');";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
	$stmt->execute();
	$stmt->close();
	/** TRACKING **/
}

?>