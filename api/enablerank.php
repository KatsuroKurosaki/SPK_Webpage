<?php
$sql = "SELECT web_transaction.days, mc_ranks_pricing.id_rank, mc_players.rankupdatable, mc_players.rankuntil, mc_players.rankid as playercurrank, mc_ranks.rank as rankname, mc_players.staff_member
FROM web_transaction
INNER JOIN mc_ranks_pricing ON mc_ranks_pricing.id = web_transaction.id_rank_pricing
INNER JOIN mc_players ON mc_players.id = web_transaction.id_mc_player
INNER JOIN mc_ranks ON mc_ranks.id = mc_ranks_pricing.id_rank
WHERE web_transaction.id = ? AND web_transaction.id_mc_player = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'ii',
	$_POST['id'],
	$datos_user[0]['id_mc_player']
);
$stmt->execute();
if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
if(count($data)==1){
	if($data[0]['staff_member'] == 'Y'){
		$out['status']="no";
		$out['msg']="Los miembros del Staff no pueden cambiar su rango.";
	}elseif($data[0]['rankupdatable'] == 'Y'){
		$cad = new DateTime();
		$cad->modify("+".$data[0]['days']."Day");
		$cadutc = $cad->format("U");
		$cad = $cad->format("Y-m-d H:i:s");
		$sql = "UPDATE mc_players SET rankupdatable = 'N', rankid = ?, rankuntil =? WHERE id = ?;";
		$stmt = $conn->prepare($sql);
		if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
		$stmt->bind_param( 'isi',
			$data[0]['id_rank'],
			$cad,
			$datos_user[0]['id_mc_player']
		);
		$stmt->execute();
		if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
		$stmt->close();
		
		$sql = "SELECT parent FROM pex_inheritance WHERE child = ?;";
		$stmt = $conn->prepare($sql);
		if($stmt===false){ die( $conn->error ); }
		$stmt->bind_param( 's',
			$datos_user[0]['uuid']
		);
		$stmt->execute();
		$pex = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		$stmt->close();
		
		if (count($pex)==0){
			$sql = "INSERT INTO pex_entity (`name`,`type`,`default`) VALUES (?,1,0);";
			$stmt = $conn->prepare($sql);
			if($stmt===false){ die( $conn->error ); }
			$stmt->bind_param( 's',
				$datos_user[0]['uuid']
			);
			$stmt->execute();
			$stmt->close();
			
			$sql = "INSERT INTO pex_inheritance (`child`,`parent`, `type` ) VALUES (?,?,1);";
			$stmt = $conn->prepare($sql);
			if($stmt===false){ die( $conn->error ); }
			$stmt->bind_param( 'ss',
				$datos_user[0]['uuid'],
				$data[0]['rankname']
			);
			$stmt->execute();
			$stmt->close();
			
			$sql = "INSERT INTO pex_permissions_coliseopvp (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,'name','',?);";
			$stmt = $conn->prepare($sql);
			if($stmt===false){ die( $conn->error ); }
			$stmt->bind_param( 'ss',
				$datos_user[0]['uuid'],
				$datos_user[0]['playername']
			);
			$stmt->execute();
			$stmt->close();
			$sql = "INSERT INTO pex_permissions_creativo (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,'name','',?);";
			$stmt = $conn->prepare($sql);
			if($stmt===false){ die( $conn->error ); }
			$stmt->bind_param( 'ss',
				$datos_user[0]['uuid'],
				$datos_user[0]['playername']
			);
			$stmt->execute();
			$stmt->close();
			$sql = "INSERT INTO pex_permissions_eggwars (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,'name','',?);";
			$stmt = $conn->prepare($sql);
			if($stmt===false){ die( $conn->error ); }
			$stmt->bind_param( 'ss',
				$datos_user[0]['uuid'],
				$datos_user[0]['playername']
			);
			$stmt->execute();
			$stmt->close();
			$sql = "INSERT INTO pex_permissions_factions (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,'name','',?);";
			$stmt = $conn->prepare($sql);
			if($stmt===false){ die( $conn->error ); }
			$stmt->bind_param( 'ss',
				$datos_user[0]['uuid'],
				$datos_user[0]['playername']
			);
			$stmt->execute();
			$stmt->close();
			$sql = "INSERT INTO pex_permissions_lobby (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,'name','',?);";
			$stmt = $conn->prepare($sql);
			if($stmt===false){ die( $conn->error ); }
			$stmt->bind_param( 'ss',
				$datos_user[0]['uuid'],
				$datos_user[0]['playername']
			);
			$stmt->execute();
			$stmt->close();
			$sql = "INSERT INTO pex_permissions_prelobby (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,'name','',?);";
			$stmt = $conn->prepare($sql);
			if($stmt===false){ die( $conn->error ); }
			$stmt->bind_param( 'ss',
				$datos_user[0]['uuid'],
				$datos_user[0]['playername']
			);
			$stmt->execute();
			$stmt->close();
			$sql = "INSERT INTO pex_permissions_skywars (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,'name','',?);";
			$stmt = $conn->prepare($sql);
			if($stmt===false){ die( $conn->error ); }
			$stmt->bind_param( 'ss',
				$datos_user[0]['uuid'],
				$datos_user[0]['playername']
			);
			$stmt->execute();
			$stmt->close();
			$sql = "INSERT INTO pex_permissions_survival (`name`,`type`,`permission`,`world`,`value`) VALUES (?,1,'name','',?);";
			$stmt = $conn->prepare($sql);
			if($stmt===false){ die( $conn->error ); }
			$stmt->bind_param( 'ss',
				$datos_user[0]['uuid'],
				$datos_user[0]['playername']
			);
			$stmt->execute();
			$stmt->close();
		}
		
		$sql = "UPDATE web_transaction SET usado = 'Y' WHERE id = ?;";
		$stmt = $conn->prepare($sql);
		if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
		$stmt->bind_param( 'i',
			$_POST['id']
		);
		$stmt->execute();
		if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
		$stmt->close();
		
		$out['fecha'] = $cad;
		$out['fechautc'] = $cadutc;
		$out['rank'] = $data[0]['rankname'];
		$out['status']="ok";
		$out['msg']="Se ha activado el rango correctamente. Cambia de modalidad para completar la activación en el server.";
	} elseif($data[0]['rankupdatable'] == 'N' && $data[0]['id_rank'] == $data[0]['playercurrank']) {
		
		$cad = DateTime::createFromFormat("Y-m-d H:i:s",$data[0]['rankuntil']);
		$cad->modify("+".$data[0]['days']."Day");
		$cadutc = $cad->format("U");
		$cad = $cad->modify("+".$data[0]['days']."Day")->format("Y-m-d H:i:s");
		$sql = "UPDATE mc_players SET rankuntil =? WHERE id = ?;";
		$stmt = $conn->prepare($sql);
		if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
		$stmt->bind_param( 'si',
			$cad,
			$datos_user[0]['id_mc_player']
		);
		$stmt->execute();
		if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
		$stmt->close();
		
		$sql = "UPDATE web_transaction SET usado = 'Y' WHERE id = ?;";
		$stmt = $conn->prepare($sql);
		if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
		$stmt->bind_param( 'i',
			$_POST['id']
		);
		$stmt->execute();
		if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
		$stmt->close();
		
		$out['fecha'] = $cad;
		$out['fechautc'] = $cadutc;
		$out['rank'] = $data[0]['rankname'];
		$out['status']="ok";
		$out['msg']="Se ha actualizado tu rango añadiendo ".$data[0]['days']." días correctamente.";
	} else {
		$out['status']="no";
		$out['msg']="Actualmente tienes un rango activo y no es compatible con el que intentas activar, has de esperar a que se termine tu rango actual.";
	}
} else {
	$out['status']="no";
	$out['errormsg']="No se han encontrado los datos de activación de rango.";
}
?>