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
	$out['msg'] = "No puedes contratar este jugador como Helper. Ha de perder su rango actual.";
} else {
	
	require '../class/PermissionsEX.php';
	
	require '../class/GoogleAuthenticator.php';
	$ga = new PHPGangsta_GoogleAuthenticator();
	$secret = $ga->createSecret();
	
	$sql = "UPDATE mc_players SET rankid = 9, rankuntil = '"._MAXTS."', staff_member='Y', rankupdatable = 'N', gauthcode = ? WHERE `id` = ?;";
	$stmt = $conn->prepare($sql);
	if($stmt===false){ die( $conn->error ); }
	$stmt->bind_param( 'si',
		$secret,
		$_POST['mc_nameid']
	);
	$stmt->execute();
	$stmt->close();
	
	$sql = "UPDATE mc_players SET staff_member_helper = ? WHERE `id` = ?;";
	$stmt = $conn->prepare($sql);
	if($stmt===false){ die( $conn->error ); }
	$stmt->bind_param( 'ii',
		$_POST['mc_nameid'],
		$datos_user[0]['id_mc_player']
	);
	$stmt->execute();
	$stmt->close();
	
	PermissionsEX::setRank($conn,$data[0]['uuid'],$_POST['mc_name'],"HELPER");
	
	shell_exec('sudo -u '._MC_SYSTEMUSER.' screen -r '._SCREENALERTS.' -X stuff "send '.$_POST['mc_name'].' prelobby\015"');
	sleep(1);
	shell_exec('sudo -u '._MC_SYSTEMUSER.' screen -r '._SCREENALERTS.' -X stuff "i '.$_POST['mc_name'].' Se te ha asignado un rango HELPER. Usa /lobby para volver a entrar.\015"');
	
	$out['status'] = "ok";
	$out['color'] = "success";
	$out['msg'] = "El rango del jugador se ha asignado correctamente.";
	
	/** TRACKING **/
	$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha contratado a un helper ".$conn->escape_string($_POST['mc_name'])."');";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
	$stmt->execute();
	$stmt->close();
	/** TRACKING **/
}
?>