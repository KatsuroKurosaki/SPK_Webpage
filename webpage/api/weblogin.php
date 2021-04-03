<?php
$sql = "SELECT id, `password`, salt
FROM mc_players
WHERE playername = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 's',
	$_POST['u']
);
$stmt->execute();
if($stmt->error){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if($data != NULL){
	if(hash('whirlpool',$_POST['p'].$data['salt']) == $data['password']){
		$out['status'] = "ok";
		$out['session'] = SPK\GlobalFunc::genSessionId();
		
		$sql = "INSERT INTO web_session (id_mc_player, session, expire, ip_address, user_agent) VALUES (?,?,DATE_ADD(NOW(),INTERVAL "._SESSTIMEOUT."),?,?) ON DUPLICATE KEY UPDATE session=?, expire=DATE_ADD(NOW(),INTERVAL "._SESSTIMEOUT."), ip_address=?, user_agent=?;";
		$stmt = $conn->prepare($sql);
		if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
		$stmt->bind_param( 'issssss',
			$data['id'],
			$out['session'],
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT'],
			$out['session'],
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT']
		);
		$stmt->execute();
		if($stmt->error){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
		$stmt->close();
		
		$sql = "INSERT INTO web_session_log (id_mc_player, ip_address, user_agent) VALUES (?,?,?);";
		$stmt = $conn->prepare($sql);
		if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
		$stmt->bind_param( 'iss',
			$data['id'],
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT']
		);
		$stmt->execute();
		if($stmt->error){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
		$stmt->close();
		setcookie("SpkSession", $out['session'], strtotime(_COOKIETIMEOUT));
		shell_exec('sudo -u '._MC_SYSTEMUSER.' screen -r '._SCREENALERTS.' -X stuff "i '.$_POST['u'].' Has iniciado sesión en la pagina web desde la IP '.$_SERVER['REMOTE_ADDR'].'.\015"');
	} else {
		$out['status'] = "no";
		$out['reason'] = "La contraseña es incorrecta.";
	}
} else {
	$out['status'] = "no";
	$out['reason'] = "No se encuentra el nombre de Minecraft.";
}
?>