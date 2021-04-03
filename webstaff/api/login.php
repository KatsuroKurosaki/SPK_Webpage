<?php
$sql = "SELECT id, `password`, salt, staff_member, gauthcode
FROM mc_players
WHERE playername = ?;";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
	$out['status'] = "ko";
	$out['msg'] = $conn->error;
	die(json_encode($out));
}
$stmt->bind_param(
	's',
	$_POST['u']
);
$stmt->execute();
if ($stmt === false) {
	$out['status'] = "ko";
	$out['msg'] = $stmt->error;
	die(json_encode($out));
}
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (count($data) == 1) {
	if ($data[0]['staff_member'] == "Y") {
		//if($data[0]['gauthcode'] != ""){
		//require '../class/GoogleAuthenticator.php';
		//$ga = new PHPGangsta_GoogleAuthenticator();
		//if($ga->verifyCode($data[0]['gauthcode'], $_POST['c'], 2)){
		$_POST['p'] = hash('whirlpool', $_POST['p'] . $data[0]['salt']);
		if ($_POST['p'] == $data[0]['password']) {
			$out['status'] = "ok";
			require './class/UUID.php';
			$out['session'] = UUID::v5(UUID::v4(), bin2hex(openssl_random_pseudo_bytes(rand(1, 9))));
			$sesscad = new DateTime();
			$sesscad = $sesscad->modify(_SESSTIMEOUT)->format('Y-m-d H:i:s');
			/*$sql = "INSERT INTO web_session_staff (id_mc_player, session, expire, ip_address, user_agent) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE session=?, expire=?, ip_address=?, user_agent=?;";
			$stmt = $conn->prepare($sql);
			if ($stmt === false) {
				$out['status'] = "ko";
				$out['msg'] = $conn->error;
				die(json_encode($out));
			}
			$stmt->bind_param(
				'issssssss',
				$data[0]['id'],
				$out['session'],
				$sesscad,
				$_SERVER['REMOTE_ADDR'],
				$_SERVER['HTTP_USER_AGENT'],
				$out['session'],
				$sesscad,
				$_SERVER['REMOTE_ADDR'],
				$_SERVER['HTTP_USER_AGENT']
			);
			$stmt->execute();
			if ($stmt === false) {
				$out['status'] = "ko";
				$out['msg'] = $stmt->error;
				die(json_encode($out));
			}
			$stmt->close();

			setcookie("SpkStaffSession", $out['session']);*/
		} else {
			$out['status'] = "no";
			$out['reason'] = "La contraseña no es correcta.";
		}
		/*} else {
				$out['status'] = "no";
				$out['reason'] = "El código de seguridad no es correcto.";
			}*/
		/*} else {
			$out['status'] = "no";
			$out['reason'] = "No tienes configurados los cdódigos de seguridad, contacta con el Admin.";
		}*/
	} else {
		$out['status'] = "no";
		$out['reason'] = "Acceso denegado, no eres un miembro del Staff.";
	}
} else {
	$out['status'] = "no";
	$out['reason'] = "No se encuentra el nombre de Minecraft.";
}
