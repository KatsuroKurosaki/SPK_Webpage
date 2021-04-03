<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php?s=<?php echo $_GET['s']; ?>"><i class="fa fa-server" aria-hidden="true"></i> SPK STAFF</a>
		</div>
		<div class="navbar-collapse collapse navbar-responsive-collapse">
			<ul class="nav navbar-nav">
				<li<?php if (_FILE == "index") {
						echo ' class="active"';
					} ?>><a href="index.php?s=<?php echo $_GET['s']; ?>">Inicio</a></li>
					<?php if (Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_WEBCONSOLE)) { ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Consola web <i class="caret"></i></a>
							<ul class="dropdown-menu">
								<?php
								$sql = "SELECT id, modename FROM mc_modes ORDER BY modename;";
								$stmt = $conn->prepare($sql);
								if ($stmt === false) {
									die($conn->error);
								}
								$stmt->execute();
								if ($stmt === false) {
									die($stmt->error);
								}
								foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $k => $v) {
									echo '<li><a href="console.php?s=' . $_GET['s'] . '&server=' . $v['id'] . '">' . $v['modename'] . '</a></li>';
								}
								$stmt->close();
								?>
							</ul>
						</li>
					<?php } ?>
					<?php if (Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_BANLIST)) { ?>
						<li<?php if (_FILE == "bat") {
								echo ' class="active"';
							} ?>><a href="bat.php?s=<?php echo $_GET['s']; ?>">Lista de baneos</a></li>
						<?php } ?>
						<?php if (Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_PERMISSIONSEX)) { ?>
							<li<?php if (_FILE == "pex") {
									echo ' class="active"';
								} ?>><a href="pex.php?s=<?php echo $_GET['s']; ?>">PermissionsEX</a></li>
							<?php } ?>
							<?php if (Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_HELPOP)) { ?>
								<li<?php if (_FILE == "helpop") {
										echo ' class="active"';
									} ?>><a href="helpop.php?s=<?php echo $_GET['s']; ?>">HelpOP</a></li>
								<?php } ?>
								<?php if (Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_IPLIST)) { ?>
									<li<?php if (_FILE == "iplist") {
											echo ' class="active"';
										} ?>><a href="iplist.php?s=<?php echo $_GET['s']; ?>">Info de jugador</a></li>
									<?php } ?>
									<?php if (Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_RANKS)) { ?>
										<li<?php if (_FILE == "ranks") {
												echo ' class="active"';
											} ?>><a href="ranks.php?s=<?php echo $_GET['s']; ?>">Rangos</a></li>
										<?php } ?>
										<?php if (Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_TRANSACTION)) { ?>
											<li<?php if (_FILE == "transactions") {
													echo ' class="active"';
												} ?>><a href="transactions.php?s=<?php echo $_GET['s']; ?>">Transacciones</a></li>
											<?php } ?>
											<?php if (Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_MIHELPER)) { ?>
												<li<?php if (_FILE == "helper") {
														echo ' class="active"';
													} ?>><a href="helper.php?s=<?php echo $_GET['s']; ?>">Mi helper</a></li>
												<?php } ?>
												<li><a href="javascript:desconexion();">Desconectar</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<div style="padding-top:8px;" class="text-center small">Hora local del server<br /><span id="servertime">____/__/__ __:__:__</span></div>
			</ul>
		</div>
	</div>
	<script type="text/javascript">
		function showServerTime() {
			$("#servertime").text(moment.utc(moment().unix() * 1000).format('YYYY/MM/DD HH:mm:ss'));
			setTimeout(showServerTime, 1000);
		}

		setTimeout(showServerTime, 1000);

		function desconexion() {
			spawnConfirmModal(
				"Cerrar sesión",
				"¿Estás seguro que deseas cerrar sesión de la página de staff?<br/>Se te volverán a pedir los datos de nuevo (Nombre, Contraseña y código)",
				function() {
					window.location = 'logout.php?s=<?php echo $_GET['s']; ?>';
				}
			);
		}
	</script>
</nav>