<?php
if(isset($_REQUEST['s'])){
	$sesscad = date('Y-m-d H:i:s');
	$sql = "SELECT web_session_staff.id_mc_player, mc_players.playername, mc_players.uuid, mc_ranks.staff_perms
	FROM web_session_staff
	INNER JOIN mc_players ON mc_players.id = web_session_staff.id_mc_player
	INNER JOIN mc_ranks ON mc_ranks.id = mc_players.rankid
	WHERE web_session_staff.session = ? AND web_session_staff.expire > ?;";
	$stmt = $conn->prepare($sql);
	if($stmt === false){die($conn->error);}
	$stmt->bind_param( 'ss',
		$_REQUEST['s'],
		$sesscad
	);
	$stmt->execute();
	if($stmt === false){die($stmt->error);}
	$datos_user = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
	$stmt->close();
	
	if(count($datos_user) == 1){
		$sesscad = new DateTime();
		$sesscad = $sesscad->modify(_SESSTIMEOUT)->format('Y-m-d H:i:s');
		$sql = "UPDATE web_session_staff SET expire=? WHERE id_mc_player = ?;";
		$stmt = $conn->prepare($sql);
		if($stmt === false){die($conn->error);}
		$stmt->bind_param( 'si',
			$sesscad,
			$datos_user[0]['id_mc_player']
		);
		$stmt->execute();
		if($stmt === false){die($stmt->error);}
		$stmt->close();
	} else {
		//unset($_COOKIE['SpkStaffSession']);
		//setcookie ("SpkStaffSession", "", time() - 3600);
		header("Location: login.php");
		die();
	}
	unset($sesscad);
}
?>