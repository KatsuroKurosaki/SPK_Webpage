<?php
$sql = "SELECT `name`, `value`, `permission`, `world` FROM pex_permissions_".$_POST['modename']." WHERE `type` = 0;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
/*$stmt->bind_param( 's',
	$_POST['term']
);*/
$stmt->execute();
if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$out['data'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$out['status']="ok";

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha Buscado en PEX acerca de ".$conn->escape_string($_POST['modename']).".');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>