<?php
$sql = "DELETE FROM mc_players_extracmds WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt===false){ die( $conn->error ); }
$stmt->bind_param( 'i',
	$_POST['id']
);
$stmt->execute();
$stmt->close();
	
$out['status'] = "ok";
$out['color'] = "success";
$out['msg'] = "El permiso extra del jugador se ha retirado correctamente.";

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha retirado un permiso adicional del jugador');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>