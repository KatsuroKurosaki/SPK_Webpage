<?php
$sql = "SELECT `status`
FROM web_transaction
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

if($data[0]['status'] == 'COMPLETE'){
	$out['status'] = "no";
	$out['color'] = "warning";
	$out['msg'] = "Esta transaccion ya está finalizada.";
} else {
	$sql = "UPDATE web_transaction SET `status` = 'COMPLETE', web_return = NOW() WHERE `id` = ?;";
	$stmt = $conn->prepare($sql);
	if($stmt===false){ die( $conn->error ); }
	$stmt->bind_param( 'i',
		$_POST['id']
	);
	$stmt->execute();
	$stmt->close();
	
	$out['status'] = "ok";
	$out['color'] = "success";
	$out['msg'] = "Se ha finalizado la transacción correctamente.";
	
	/** TRACKING **/
	$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha finalizado la transaccion_id ".$conn->escape_string($_POST['id'])."');";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
	$stmt->execute();
	$stmt->close();
	/** TRACKING **/
}
?>