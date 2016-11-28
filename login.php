<?php
if(isset($_COOKIE['SpkSession'])){
	require 'cnf.php';
	require 'class/SPK.php';
	error_reporting(_DEBUGLVL);
	$conn = SPK\GlobalFunc::getMysqlConn(_HOST,_USER,_PASS,_DDBB);
	
	$sql = "SELECT web_session.id_mc_player
	FROM web_session
	WHERE web_session.session = ?;";
	$stmt = $conn->prepare($sql);
	if($stmt === false){die($conn->error);}
	$stmt->bind_param('s',
		$_COOKIE['SpkSession']
	);
	$stmt->execute();
	if($stmt->error){die($stmt->error);}
	$datos_user = $stmt->get_result()->fetch_assoc();
	$stmt->close();
	
	if($datos_user != NULL){
		$sql = "UPDATE web_session SET expire = DATE_ADD(NOW(),INTERVAL "._SESSTIMEOUT.") WHERE id_mc_player = ?;";
		$stmt = $conn->prepare($sql);
		if($stmt === false){die($conn->error);}
		$stmt->bind_param('i',
			$datos_user['id_mc_player']
		);
		$stmt->execute();
		if($stmt->error){die($stmt->error);}
		$stmt->close();
		unset($sesscad);
		if(isset($_SERVER['HTTP_REFERER'])){
			header("Location: ".parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH)."?s=".$_COOKIE['SpkSession']);
		} else {
			header("Location: /index.php?s=".$_COOKIE['SpkSession']);
		}
		die();
	} else {
		setcookie ("SpkSession", "", 0);
	}
} else {
	setcookie ("SpkSession", "", 0);
}
?><!DOCTYPE html>
<html>
<!--
_____/\\\\\\\\\\\__________/\\\\\\\\\\\\\__________/\\\________/\\\_______        
 ___/\\\/////////\\\_______\/\\\/////////\\\_______\/\\\_____/\\\//________       
  __\//\\\______\///________\/\\\_______\/\\\_______\/\\\__/\\\//___________      
   ___\////\\\_______________\/\\\\\\\\\\\\\/________\/\\\\\\//\\\___________     
    ______\////\\\____________\/\\\/////////__________\/\\\//_\//\\\__________    
     _________\////\\\_________\/\\\___________________\/\\\____\//\\\_________   
      __/\\\______\//\\\________\/\\\___________________\/\\\_____\//\\\________  
       _\///\\\\\\\\\\\/____/\\\_\/\\\______________/\\\_\/\\\______\//\\\__/\\\_ 
        ___\///////////_____\///__\///______________\///__\///________\///__\///__
-->
    <head>
        <meta charset="utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="initial-scale=1,user-scalable=no,width=device-width"/>
        <meta name="format-detection" content="telephone=no"/>
        <meta name="msapplication-tap-highlight" content="no"/>
		<meta name="msapplication-config" content="none"/>
		<meta name="description" content="S.P.K. Es un servidor minecraft no-premium y premium con modalidades conocidas y otras propias">
		<meta name="author" content="Katsuro Kurosaki">
		<meta name="theme-color" content="#000000">
		<meta name="msapplication-navbutton-color" content="#000000">
		<meta name="apple-mobile-web-app-status-bar-style" content="#000000">
		
		<!--[if IE]><link rel="shortcut icon" href="favicon.ico"/><![endif]-->
		<link rel="apple-touch-icon-precomposed" href="favicon.png"/>
		<link rel="icon" href="favicon.png"/>
		
		<!--[if lt IE 9]>
			<script type="text/javascript" src="./js/jquery-1.12.4.js" charset="UTF-8"></script>
		<![endif]-->
		<!--[if gte IE 9]><!-->
			<script type="text/javascript" src="./js/jquery-2.2.4.js" charset="UTF-8"></script>
		<!--<![endif]-->
		<!--<script type="text/javascript" src="./js/jquery-3.1.0.js" charset="UTF-8"></script>-->
		<script type="text/javascript" src="./js/bootstrap-3.3.7.js" charset="UTF-8"></script>
		<script type="text/javascript" src="./js/js-cookie-2.1.2.js" charset="UTF-8"></script>
        <script type="text/javascript" src="./js/global-functions.js" charset="UTF-8"></script>
		<script type="text/javascript" src="./js/login.js" charset="UTF-8"></script>
        
		<link rel="stylesheet" type="text/css" href="./css/bootstrap-3.3.7.css"/>
		<link rel="stylesheet" type="text/css" href="./css/bootstrap-theme-3.3.7.css"/>
		<!--<link rel="stylesheet" type="text/css" href="./css/bootstrap-slate.css"/>-->
		<link rel="stylesheet" type="text/css" href="./css/font-awesome-4.6.3.css"/>
		<!--[if lte IE 7]>
			<link rel="stylesheet" type="text/css" href="./css/font-awesome-3.2.1.css"/>
			<link rel="stylesheet" type="text/css" href="./css/font-awesome-ie7-3.2.1.css"/>
		<![endif]-->
		<link rel="stylesheet" type="text/css" href="./css/font-lato.css"/>
		<link rel="stylesheet" type="text/css" href="./css/global-style.css"/>
		<link rel="stylesheet" type="text/css" href="./css/login-style.css"/>
        <title>S.P.K. :: Sobrevive Para Kraftear</title>
    </head>
    <body>
		<div class="container">
			<form class="form-signin">
				<h2 class="form-signin-heading text-center txtcolor-white"><i class="fa fa-sign-in"></i> Iniciar sesión</h2>
				<label for="inputName" class="sr-only">Nombre de minecraft</label>
				<input type="text" id="inputName" class="form-control" placeholder="Nombre de minecraft" required onkeyup="javascript:usernameKeyUp(event);">
				<label for="inputPassword" class="sr-only">Contraseña (Tu /login en SPK)</label>
				<input type="password" id="inputPassword" class="form-control" placeholder="Contraseña (Tu /login en SPK)" required onkeydown="javascript:passwordKeyDownEvent(event);">
				<button type="button" class="btn btn-lg btn-primary btn-block" onclick="javascript:login();"><i class="fa fa-sign-in"></i> Iniciar sesión</button>
				<hr/>
				<button type="button" class="btn btn-block btn-info" onclick="javascript:recoverPass();"><i class="fa fa-unlock-alt"></i> Recuperar mi contraseña</button>
			</form>
		</div>
		<div id="alerts"></div>
    </body>
</html>
