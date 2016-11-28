<?php
$sql = "SELECT dirname, logfile FROM mc_modes WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'i',
	$_POST['server']
);
$stmt->execute();
if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
if(count($data)==1){
	$out['consoleout'] = shell_exec("cat "._MC_ROOT."/".$data[0]['dirname'].$data[0]['logfile']." | tail -30");
	$out['status'] = "ok";
} else {
	$out['status'] = "no";
	$out['reason'] = "Error: No se ha encontrado la consola en el server.";
}
?>