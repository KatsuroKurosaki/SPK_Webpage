<?php
$sql = "SELECT `uuid`, playername
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

require '../class/PermissionsEX.php';

$sql = "UPDATE mc_players SET rankid = "._BASERANKID.", rankuntil = '"._MAXTS."', rankupdatable = 'Y', staff_member='N', gauthcode=NULL WHERE `id` = ?;";
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

$sql = "UPDATE mc_players SET staff_member_helper = NULL WHERE `id` = ?;";
$stmt = $conn->prepare($sql);
if($stmt===false){ die( $conn->error ); }
$stmt->bind_param( 'i',
	$datos_user[0]['id_mc_player']
);
$stmt->execute();
$stmt->close();

PermissionsEX::delRank($conn,$data[0]['uuid']);

shell_exec('sudo -u '._MC_SYSTEMUSER.' screen -r '._SCREENALERTS.' -X stuff "send '.$data[0]['playername'].' prelobby\015"');
sleep(1);
shell_exec('sudo -u '._MC_SYSTEMUSER.' screen -r '._SCREENALERTS.' -X stuff "i '.$data[0]['playername'].' Se te ha despedido como HELPER del server. Usa /lobby para volver a entrar.\015"');

$out['status'] = "ok";
$out['color'] = "success";
$out['msg'] = "Se ha despedido a tu helper correctamente.";

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha despedido a su helper ".$data[0]['playername']."');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/

?>