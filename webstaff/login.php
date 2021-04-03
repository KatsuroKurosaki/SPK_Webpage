<!DOCTYPE html>
<html lang="en">

<head>
	<?php require 'header.php'; ?>
	<?php require 'headercss.htm'; ?>
	<link href="css/login-style.min.css?<?= filemtime('css/login-style.min.css') ?>" type="text/css" rel="stylesheet" />
</head>

<body class="d-flex justify-content-center align-items-center">
	<div id="stafflogo">STAFF</div>
	<form class="form-row form-signin text-light px-3 py-4" style="display:none;">
		<div class="col-12 mb-3">
			<label>Nombre de minecraft:</label>
			<input type="text" name="username" class="form-control" />
		</div>
		<div class="col-12 mb-3">
			<label>Contraseña:</label>
			<input type="password" name="password" class="form-control" />
		</div>
		<div class="col-12 mb-3">
			<button type="submit" class="btn btn-lg btn-primary btn-block">
				<i class="fas fa-sign-in-alt"></i> Iniciar sesión
			</button>
		</div>
		<div class="col-12">
			<p class="small text-center">&copy; <?= date('Y') ?> - S.P.K.</p>
		</div>
	</form>
	<?php require 'footerjs.htm'; ?>
	<script src="js/login.min.js?<?= filemtime('js/login.min.js') ?>" type="text/javascript" charset="UTF-8"></script>
	<script type="text/javascript">
		$(window).on("load", function() {
			$("form").css("display", "").on("submit", function(e) {
				e.preventDefault();
				$.api({
					data: {
						op: 'HELLO'
					},
					success: function(data) {
						$.spawnAlert({
							body: data.msg
						});
					}
				});
			}).animateCss({
				effect: "zoomIn",
				end: function() {
					$("form input:first").focus();
				}
			});
		});
	</script>
</body>

</html>