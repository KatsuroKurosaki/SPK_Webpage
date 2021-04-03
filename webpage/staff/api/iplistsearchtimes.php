<?php
$sql = "SELECT tsconnect, tsdisconnect, tsdisconnect-tsconnect as onlinetime, ipaddress
FROM mc_players_log
WHERE playername = ?
ORDER BY id DESC;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 's',
	$_POST['playername']
);
$stmt->execute();
if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$out['data'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
foreach($out['data'] as $k=>$v){
	$out['data'][$k]['onlinetime'] = sec2hms($v['onlinetime']);
}
$stmt->close();

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha buscado información del jugador ".$conn->escape_string($_POST['playername']).".');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>