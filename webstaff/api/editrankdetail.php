<?php

$sql = "SELECT playername, rankupdatable, staff_member, `uuid`
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

if($data[0]['rankupdatable'] == 'Y'){
	$out['status'] = "no";
	$out['color'] = "danger";
	$out['msg'] = "El rango del jugador ha caducado y no se permite editar los detalles.";
} elseif($data[0]['staff_member'] == 'Y'){
	$out['status'] = "no";
	$out['color'] = "danger";
	$out['msg'] = "No esta permitido cambiar los detalles del rango a los miembros del Staff.";
} else {
	
	$sql = "UPDATE mc_players SET rankuntil = FROM_UNIXTIME(?), canal_yt = ?, rankupdatable = 'N' WHERE `id` = ?;";
	$stmt = $conn->prepare($sql);
	if($stmt===false){ die( $conn->error ); }
	$stmt->bind_param( 'isi',
		$_POST['expire'],
		$_POST['canal_yt'],
		$_POST['mc_nameid']
	);
	$stmt->execute();
	$stmt->close();
	
	$out['status'] = "ok";
	$out['color'] = "success";
	$out['msg'] = "Se han actualizado detalles del rango correctamente.";
	
	/** TRACKING **/
	$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha actualizado los datos del rango de ".$conn->escape_string($data[0]['playername'])."');";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
	$stmt->execute();
	$stmt->close();
	/** TRACKING **/
}
?>