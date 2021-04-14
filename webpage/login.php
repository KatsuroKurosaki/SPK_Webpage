<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
	<?php require 'header.php'; ?>
	<?php require 'headercss.php'; ?>
	<link rel="stylesheet" href="css/login_style.min.css?<?= filemtime('css/login_style.min.css') ?>" />
</head>

<body class="h-100 d-flex justify-content-center align-items-center">
	<div class="row no-gutters signin bg-white rounded">
		<div class="col-12 col-md-5">
			<div class="d-flex flex-column h-100">
				<div class="flex-fill w-75 mx-auto p-3 d-flex justify-content-center align-items-center">
					<form class="form-row form-signin" autocomplete="off">
						<div class="col-12 mb-3">
							<p class="h3 font-weight-bolder text-center txtcolor-gray">
								<span class="spktxtred">S.</span>
								<span class="spktxtorange">P.</span>
								<span class="spktxtgreen">K.</span>
							</p>
						</div>
						<div class="col-12 mt-3 mb-3">
							<div class="position-relative form-label-group usericon">
								<input type="text" name="username" class="form-control form-control pl-5 border-secondary border-top-0 border-left-0 border-right-0 rounded-0 shadow-none" placeholder="Usuario" maxlength="64" autofocus autocomplete="new-password" />
								<label class="pl-5">Nombre de minecraft</label>
							</div>
						</div>
						<div class="col-12 mb-3">
							<div class="position-relative form-label-group passicon">
								<input type="password" name="password" class="form-control form-control pl-5 border-secondary border-top-0 border-left-0 border-right-0 rounded-0 shadow-none" placeholder="Contraseña" maxlength="72" autocomplete="new-password" />
								<label class="pl-5">Contraseña</label>
							</div>
						</div>
						<div class="col-12 mb-3 text-center">
							<button type="button" class="btn btn-primary w-50 shadow" onclick="javascript:submitLogin();">
								ENTRAR
							</button>
						</div>
						<input type="hidden" name="op" value="LOGIN" />
					</form>
				</div>
				<div class="text-center p-3">
					<img src="img/logo-login.png" class="img-fluid" alt="SPK logo" />
				</div>
			</div>
		</div>
		<div class="col-7 d-none d-md-block">
			<div class="d-flex justify-content-center align-items-center h-100 rounded-right sidelogo">
				&nbsp;
			</div>
		</div>
	</div>
	<?php require 'footerjs.php'; ?>
	<script src="js/login.min.js?<?= filemtime('js/login.min.js') ?>"></script>
</body>

</html>