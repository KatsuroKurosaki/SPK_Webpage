<?php
$sql = "SELECT playername
FROM mc_players_log
WHERE ipaddress = ? AND playername <> ?
GROUP BY playername";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'ss',
	$_POST['ip'],
	$_POST['playername']
);
$stmt->execute();
if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$out['data'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>