<?php
$sql = "SELECT ip, port, maintenance
FROM mc_modes
WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'i',
	$_POST['mode']
);
$stmt->execute();
if($stmt->error){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if($data != NULL){
	if($data['maintenance']=="Y"){
		$out['status'] = "no";
	} else {
		$fp = @fsockopen($data['ip'], $data['port'], $errno, $errstr, 3);
		if (!$fp) {
			$out['status'] = "ko";
		} else {
			$out['status'] = "ok";
			fclose($fp);
		}
	}
} else {
	$out['status'] = "ko";
}
?>