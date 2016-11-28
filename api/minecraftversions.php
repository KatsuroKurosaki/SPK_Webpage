<?php
$ult48h = new DateTime();
$ult48h = $ult48h->modify("-2Day")->format('U');
$sql = "SELECT mcversion
FROM mc_players_log
WHERE tsconnect > ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'i',
	$ult48h
);
$stmt->execute();
if($stmt->error){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$out['mcversions'] = array();
foreach($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $k=>$v){
	if(!array_key_exists(SPK\GlobalFunc::minecraftVersion($v['mcversion']),$out['mcversions'])){
		$out['mcversions'][SPK\GlobalFunc::minecraftVersion($v['mcversion'])] = 0;
	}
	$out['mcversions'][SPK\GlobalFunc::minecraftVersion($v['mcversion'])]++;
}
arsort($out['mcversions']);
$stmt->close();

unset($ult48h);
?>