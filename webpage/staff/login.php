<?php
if(isset($_COOKIE['SpkStaffSession'])){
	require '../cnf.php';
	require '../common.php';
	$sql = "SELECT web_session_staff.id_mc_player
	FROM web_session_staff
	WHERE web_session_staff.session = ?;";
	$stmt = $conn->prepare($sql);
	if($stmt === false){die($conn->error);}
	$stmt->bind_param( 's',
		$_COOKIE['SpkStaffSession']
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
		unset($sesscad);
		header("Location: index.php?s=".$_COOKIE['SpkStaffSession']."&welcome=true");
		die();
	} else {
		unset($_COOKIE['SpkStaffSession']);
		setcookie ("SpkStaffSession", "", time() - 3600);
	}
} else {
	unset($_COOKIE['SpkStaffSession']);
	setcookie ("SpkStaffSession", "", time() - 3600);
}
?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="initial-scale=1,user-scalable=no,width=device-width"/>
        <meta name="format-detection" content="telephone=no"/>
        <meta name="msapplication-tap-highlight" content="no"/>
		<meta name="msapplication-config" content="none"/>
		<meta name="description" content="Página de control para el Staff"/>
		<meta name="author" content="Katsuro Kurosaki"/>
		
		<!--[if IE]><link rel="shortcut icon" href="favicon.ico"/><![endif]-->
		<link rel="apple-touch-icon-precomposed" href="favicon.png"/>
		<link rel="icon" href="favicon.png"/>
		
		<!--[if lt IE 9]>
			<script type="text/javascript" src="./js/jquery-1.12.3.js" charset="UTF-8"></script>
		<![endif]-->
		<!--[if gte IE 9]><!-->
			<script type="text/javascript" src="./js/jquery-2.2.4.js" charset="UTF-8"></script>
		<!--<![endif]-->
		<script type="text/javascript" src="./js/bootstrap-3.3.6.js" charset="UTF-8"></script>
		<script type="text/javascript" src="./js/js-cookie-2.1.1.js" charset="UTF-8"></script>
        <script type="text/javascript" src="./js/global-functions.js" charset="UTF-8"></script>
		<script type="text/javascript" src="./js/login.js" charset="UTF-8"></script>
        
		<link rel="stylesheet" type="text/css" href="./css/bootstrap-3.3.6.css"/>
		<!--<link rel="stylesheet" type="text/css" href="./css/bootstrap-theme-3.3.6.css"/>-->
		<!--<link rel="stylesheet" type="text/css" href="./css/bootstrap-cerulean.css"/>-->
		<link rel="stylesheet" type="text/css" href="./css/font-awesome-4.6.3.css"/>
		<!--[if lte IE 7]>
			<link rel="stylesheet" type="text/css" href="./css/font-awesome-3.2.1.css"/>
			<link rel="stylesheet" type="text/css" href="./css/font-awesome-ie7-3.2.1.css"/>
		<![endif]-->
		<link rel="stylesheet" type="text/css" href="./css/global-style.css"/>
		<link rel="stylesheet" type="text/css" href="./css/login-style.css"/>
		
        <title>Staff SPK :: Acceso restringido</title>
    </head>
    <body>
		<div id="stafflogo">STAFF</div>
		<div class="container">
			<form class="form-signin">
				<h2 class="form-signin-heading text-center text-muted"><i class="fa fa-lock"></i> Zona restringida</h2>
				<label for="username" class="sr-only">Nombre de Minecraft</label>
				<input type="text" id="username" class="form-control" placeholder="Nombre de Minecraft" required autofocus>
				<label for="password" class="sr-only">Contraseña</label>
				<input type="password" id="password" class="form-control" placeholder="Contraseña" required>
				<label for="secret" class="sr-only">Código de seguridad</label>
				<input type="number" id="secret" class="form-control" placeholder="Código de seguridad" required>
				<button type="button" class="btn btn-lg btn-primary btn-block" onclick="javascript:login();"><i class="fa fa-sign-in fa-lg"></i> Iniciar sesión</button>
			</form>
		</div>
    </body>
</html>
