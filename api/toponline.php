<?php
$ayer = new DateTime();
$ayer->modify("-1Day")->setTime(0,0,0);

$ayerdesde = $ayer->format("U");
$ayerhasta = $ayerdesde+86400;

$sql = "SELECT playername, tsconnect, tsdisconnect
FROM mc_players_log
WHERE tsconnect BETWEEN ? AND ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'ii',
	$ayerdesde,
	$ayerhasta
);
$stmt->execute();
if($stmt->error){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$players=array();
foreach($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $k=>$v){
	if(!array_key_exists($v['playername'],$players)){
		$players[$v['playername']] = 0;
	}
	$players[$v['playername']] += $v['tsdisconnect']-$v['tsconnect'];
}
arsort($players);
$stmt->close();

unset($ayer, $ayerdesde, $ayerhasta);

$out['data'] = array();
$i=0;
foreach($players as $k=>$v){
	$i++;
	$out['data'][$i] = array("name"=>$k,"timeon"=>SPK\GlobalFunc::sec2hms($v));
}

unset($players, $i);

$out['status'] = "ok";
?>