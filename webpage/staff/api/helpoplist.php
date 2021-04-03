<?php
$sql = "SELECT playername, mc_mode, UNIX_TIMESTAMP(fecha) AS fecha, message
FROM mc_helpop
ORDER BY id DESC
LIMIT 25;";
$stmt = $conn->prepare($sql);
if($stmt===false){
	die( $conn->error );
}
$stmt->execute();
$out['data'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha comprobado los HelpOP mas recientes.');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>