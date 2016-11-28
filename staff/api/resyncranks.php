<?php
require '../class/PermissionsEX.php';
$mc_modes = PermissionsEx::getMcModes($conn);

$conn->query("DELETE FROM pex_entity WHERE id >=20;");
$conn->query("ALTER TABLE pex_entity AUTO_INCREMENT=20;");

$conn->query("DELETE FROM pex_inheritance WHERE id >=20;");
$conn->query("ALTER TABLE pex_inheritance AUTO_INCREMENT=20;");

foreach ($mc_modes as $k => $v){
	$conn->query("DELETE FROM pex_permissions_".$v['dirname']." WHERE id >=2000;");
	$conn->query("ALTER TABLE pex_permissions_".$v['dirname']." AUTO_INCREMENT=2000;");
}

reset($mc_modes);

$sql = "SELECT playername, `uuid`, rank
FROM mc_players
INNER JOIN mc_ranks ON mc_ranks.id = mc_players.rankid
WHERE rankid <> 1
ORDER BY playername;";
$stmt = $conn->prepare($sql);
if($stmt===false){ die( $conn->error ); }
$stmt->execute();
$pex = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

foreach ($pex as $k=>$v){
	$sql = "INSERT INTO pex_entity (`name`,`type`,`default`) VALUES (?,1,0);";
	$stmt = $conn->prepare($sql);
	if($stmt===false){ die( $conn->error ); }
	$stmt->bind_param( 's',
		$v['uuid']
	);
	$stmt->execute();
	$stmt->close();

	$sql = "INSERT INTO pex_inheritance (`child`,`parent`, `type`) VALUES (?,?,1);";
	$stmt = $conn->prepare($sql);
	if($stmt===false){ die( $conn->error ); }
	$stmt->bind_param( 'ss',
		$v['uuid'],
		$v['rank']
	);
	$stmt->execute();
	$stmt->close();

	foreach ($mc_modes as $k2 => $v2){
		$sql = "INSERT INTO pex_permissions_".$v2['dirname']." (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,'name','',?);";
		$stmt = $conn->prepare($sql);
		if($stmt===false){ die( $conn->error ); }
		$stmt->bind_param( 'ss',
			$v['uuid'],
			$v['playername']
		);
		$stmt->execute();
		$stmt->close();
	}
}

$sql = "SELECT mc_players.uuid, mc_modes.dirname, mc_players_extracmds.permission_node
FROM mc_players_extracmds
INNER JOIN mc_players ON mc_players.id = mc_players_extracmds.id_mc_player
INNER JOIN mc_modes ON mc_modes.id = mc_players_extracmds.id_mc_mode;";
$stmt = $conn->prepare($sql);
if($stmt===false){ die( $conn->error ); }
$stmt->execute();
$pex = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

foreach ($pex as $k=>$v){
	$sql = "INSERT INTO pex_permissions_".$v['dirname']." (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,?,'','');";
	$stmt = $conn->prepare($sql);
	if($stmt===false){ die( $conn->error ); }
	$stmt->bind_param( 'ss',
		$v['uuid'],
		$v['permission_node']
	);
	$stmt->execute();
	$stmt->close();
}

$out['status'] = 'ok';
$out['msg'] = "Resincronizados todos los rangos correctamente. Si hay anomalias, reconectarse del server deberia solucionar problemas.";
$out['color'] = 'success';

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha resincronizado rangos');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>