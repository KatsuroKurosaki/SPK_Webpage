
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.php<?php if(isset($_GET['s'])){echo'?s='.$_GET['s'];} ?>" style="color:white;">.: <span style="color:#A00;">S.</span><span style="color:#FA0;">P.</span><span style="color:#0A0;">K.</span> :.</a>
				</div>
				<div class="navbar-collapse collapse navbar-responsive-collapse">
					<ul class="nav navbar-nav small">
						<li class="dropdown">
							<a href="rangos.php<?php if(isset($_GET['s'])){echo'?s='.$_GET['s'];} ?>" class="dropdown-toggle" data-toggle="dropdown">Rangos <i class="caret"></i></a>
							<ul class="dropdown-menu">
								<?php
								$sql = "SELECT id, rank, fa_icon, showdivider
								FROM mc_ranks
								WHERE visible = 'Y'
								ORDER BY orderby ASC, rank DESC;";
								$stmt = $conn->prepare($sql);
								if($stmt===false){
									die( $conn->error );
								}
								$stmt->execute();
								$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
								$stmt->close();
								
								foreach($data as $k=>$v){
									echo '<li><a href="rangos.php?id='.$v['id']; if(isset($_GET['s'])){echo '&s='.$_GET['s'];} echo '"><i class="fa '.$v['fa_icon'].' fa-lg"></i> '.$v['rank'].'</a></li>';
									if($v['showdivider'] == "Y"){
										echo '<li class="divider"></li>';
									}
								}
								?>
							</ul>
						</li>
						<li><a href="normas.php<?php if(isset($_GET['s'])){echo'?s='.$_GET['s'];} ?>">Normas</a></li>
						<li class="dropdown">
							<a href="maps.php<?php if(isset($_GET['s'])){echo'?s='.$_GET['s'];} ?>" class="dropdown-toggle" data-toggle="dropdown">Mapas <i class="caret"></i></a>
							<ul class="dropdown-menu">
								<?php
								$sql = "SELECT id, map_name
								FROM mc_maps
								WHERE web_display
								ORDER BY map_name;";
								$stmt = $conn->prepare($sql);
								if($stmt===false){
									die( $conn->error );
								}
								$stmt->execute();
								$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
								$stmt->close();
								
								foreach($data as $k=>$v){
									echo '<li><a href="maps.php?id='.$v['id']; if(isset($_GET['s'])){echo '&s='.$_GET['s'];} echo '">'.$v['map_name'].'</a></li>';
								}
								?>
							</ul>
						</li>
						<li><a href="stats.php<?php if(isset($_GET['s'])){echo'?s='.$_GET['s'];} ?>">Estadísticas</a></li>
						<li><a href="./foro/">Foros</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right small">
						<li data-toggle="tooltip" data-placement="bottom" title="Danos 'Me gusta' en Facebook.">
							<a href="https://www.facebook.com/Network-SPK-Minecraft-Server-591715610942615/" target="_blank" style="color:#3b5998;"><i class="fa fa-facebook-official fa-lg"></i></a>
						</li>
						<li data-toggle="tooltip" data-placement="bottom" title="Síguenos en Twitter.">
							<a href="https://twitter.com/SPK_MC" target="_blank" style="color:#1da1f2;"><i class="fa fa-twitter fa-lg"></i></a>
						</li>
						<?php
						if(isset($_GET['s'])){
							?>
							<li class="dropdown">
								<a href="profile.php?s=<?php echo $_GET['s']; ?>" class="dropdown-toggle" data-toggle="dropdown"><?php echo $datos_user['playername']; ?> <i class="caret"></i></a>
								<ul class="dropdown-menu">
									<li><a href="profile.php?s=<?php echo $_GET['s']; ?>"><i class="fa fa-user"></i> Mi perfil</a></li>
									<li><a href="javascript:desconexion();"><i class="fa fa-sign-out" aria-hidden="true"></i> Cerrar sesión</a></li>
								</ul>
							</li>
							<?php
						} else {
							?>
							<li><a href="login.php"><i class="fa fa-sign-in"></i> Iniciar sesión</a></li>
							<?php
						}
						?>

					</ul>
				</div>
			</div>
			
			<script type="text/javascript">
				$('.navbar-right [data-toggle="tooltip"]').tooltip();
				<?php if(isset($_GET['s'])){ ?>
				
				function desconexion(){
					spawnConfirmModal(
						"Cerrar sesión",
						"¿Estás seguro que deseas desconectarte de la página web?",
						function(){
							window.location = 'logout.php?s=<?php echo $_GET['s']; ?>';
						}
					);
				}
				<?php } ?>
			</script>
			
		</nav>
