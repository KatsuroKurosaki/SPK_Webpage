<?php
$sql = "INSERT INTO mc_players_extracmds (id_mc_player, id_mc_mode, permission_node) VALUES (?,?,?);";
$stmt = $conn->prepare($sql);
if($stmt===false){ die( $conn->error ); }
$stmt->bind_param( 'iis',
	$_POST['idp'],
	$_POST['mode'],
	$_POST['perm']
);
$stmt->execute();
if($stmt->error){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$out['idnew'] = $stmt->insert_id;
$stmt->close();
	
$out['status'] = "ok";
$out['color'] = "success";
$out['msg'] = "El permiso extra del jugador se ha añadido correctamente.";

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha añadido un nuevo permiso al id_jugador ".$conn->escape_string($_POST['idp'])." a la id_modalidad ".$conn->escape_string($_POST['mode']).": ".$conn->escape_string($_POST['perm'])."');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>