<?php
require 'cnf.php';
require 'class/SPK.php';
error_reporting(_DEBUGLVL);
$conn = SPK\GlobalFunc::getMysqlConn(_HOST, _USER, _PASS, _DDBB);
if (isset($_GET['s'])) {
	$datos_user = SPK\GlobalFunc::checkSession($conn, $_GET['s']);
	if (!$datos_user) {
		header("Location: " . pathinfo(__FILE__, PATHINFO_BASENAME));
		die();
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require 'header.php'; ?>

<body>
	<?php require 'navbar.php'; ?>

	<div class="jumbotron spklobby">
		<div class="container">
			<h1 class="text-center"><i><span style="color:#A00;">S.</span> <span style="color:#FA0;">P.</span> <span style="color:#0A0;">K.</span></i></h1>
			<p style="color:white;">Bienvenidos al servidor de los Krafteros Supervivientes! Esperamos que disfrutes de nuestro servidor de Minecraft en una comunidad en familia<br />Somos un servidor Premium / No-Premium y necesitas una de estas versiones de Minecraft para jugar: 1.14 hasta 1.8!</p>
			<h2 class="text-center"><span class="label label-info">IP: spk.katsunet.com</span></h2>
		</div>
	</div>

	<div class="container main">
		<div class="row">
			<div class="col-sm-6 col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						¿Quién está conectado? <span id="onlineNum"></span>
					</div>
					<div class="panel-body">
						<div id="onlineUsers" class="text-center" style="height:300px;overflow-y:scroll;">
							<div><i class="fa fa-cog fa-spin fa-lg"></i> Conectando a Minecraft...</div>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					getOnlineUsers();
				</script>
			</div>
			<!--<div class="col-sm-6 col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Últimos rangos adquiridos
					</div>
					<div class="panel-body">
						<div style="height:300px;overflow-y:scroll;">
							<table class="table table-striped table-hover table-condensed">
								<thead>
									<tr class="small">
										<th>Jugador</th>
										<th>Rango</th>
										<th>Cuándo</th>
									</tr>
								</thead>
								<tbody id="lastRanks">
									<tr>
										<td class="text-center" colspan="3"><i class="fa fa-cog fa-spin"></i> Cargando...</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					getLastRanks();
				</script>
			</div>-->
			<div class="col-sm-6 col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Top online día <?php $ayer = new DateTime();
										echo $ayer->modify("-1Day")->format("d/m/Y");
										unset($ayer); ?>
					</div>
					<div class="panel-body">
						<div style="height:300px;overflow-y:scroll;">
							<table class="table table-striped table-hover table-condensed">
								<thead>
									<tr class="small">
										<th></th>
										<th>Jugador</th>
										<th>Tiempo</th>
									</tr>
								</thead>
								<tbody id="topOnline">
									<tr>
										<td class="text-center" colspan="3"><i class="fa fa-cog fa-spin"></i> Cargando...</td>
									</tr>
									</thead>
							</table>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					getTopOnline();
				</script>
			</div>
			<!--<div class="col-sm-6 col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Historico jugadores online
					</div>
					<div class="panel-body">
						<div class="text-center" style="height:300px;">
							<div id="activelast" style="height:92%;">
								<div><i class="fa fa-cog fa-spin"></i> Activando máquina del tiempo...</div>
							</div>
							<p id="activeinfo">&nbsp;</p>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					getGraphHistPlayers()
				</script>
			</div>-->
			<div class="col-sm-6 col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Versiones de Minecraft
					</div>
					<div class="panel-body">
						<div style="height:300px;">
							<div class="text-center" style="height:100%;">
								<div id="minever" class="text-center" style="height:300px;"></div>
							</div>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					getGraphMinecraftVersions();
				</script>
			</div>
			<!--<div class="col-sm-6 col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Estado del servidor <span id="conngeneral"><i class="fa fa-cog fa-spin fa-lg"></i></span>
					</div>
					<div class="panel-body">
						<div style="height:300px;overflow-y:scroll;">
							<table class="table table-striped table-hover table-condensed">
								<thead>
									<tr class="small">
										<th class="text-right" style="width:50%;">Modalidad</th>
										<th>Estado</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$sql = "SELECT id, modename
										FROM mc_modes
										WHERE webdisplay = 'Y'
										ORDER BY modename ASC;";
									$stmt = $conn->prepare($sql);
									$stmt->execute();
									foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $k => $v) {
										echo '<tr><td class="text-right">' . $v['modename'] . '</td><td id="conn-' . $v['id'] . '"><i class="fa fa-cog fa-spin"></i> Comprobando...<script type="text/javascript"> checkConnectivity(' . $v['id'] . ',"#conn-' . $v['id'] . '"); </script></td></tr>';
									}
									$stmt->close();
									?>
									</thead>
							</table>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					checkConnectivity(1, "#conngeneral");
				</script>
			</div>-->
		</div>
		<div class="text-center">
			<a href='https://www.40servidoresmc.es/spk-network' target='_blank'><img style='max-width:160px;' src='https://www.40servidoresmc.es/img/botonvota.png' alt='Servidores de Minecraft 40servidoresM., Vota por SPK NETWORK'></a>
		</div>
		<!--<hr />
		<a href='https://www.40servidoresmc.es/spk-network' target='_blank'><img style='max-width:160px;' src='https://www.40servidoresmc.es/img/botonvota.png' alt='Servidores de Minecraft 40servidoresM., Vota por SPK NETWORK'></a>
		<h2>Actividad social</h2>
		<div class="row">
			<div class="col-sm-6 col-md-4 text-center">
				<h3>Últimos tweets</h3>
				<a class="twitter-timeline" data-dnt="true" href="https://twitter.com/SPK_MC" data-widget-id="646075822013612033">Tweets por @SPK_MC.</a>
				<script type="text/javascript">
					! function(d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0],
							p = /^http:/.test(d.location) ? 'http' : 'https';
						if (!d.getElementById(id)) {
							js = d.createElement(s);
							js.id = id;
							js.src = p + "://platform.twitter.com/widgets.js";
							fjs.parentNode.insertBefore(js, fjs);
						}
					}(document, "script", "twitter-wjs");
				</script>
			</div>
			<div class="col-sm-6 col-md-4 text-center">
				<h3>Actividad de Facebook</h3>
				<div id="fb-root"></div>
				<script type="text/javascript">
					(function(d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) return;
						js = d.createElement(s);
						js.id = id;
						js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.5&appId=1399763723595926";
						fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
				</script>
				<div class="fb-page" data-href="https://www.facebook.com/Network-SPK-Minecraft-Server-591715610942615/" data-tabs="timeline" data-height="600" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"></div>
			</div>
			<div class="col-sm-6 col-md-4 text-center">
				<h3>Vota por nuestro server!</h3>
				<span><a href="http://www.40servidoresmc.es/servidor.php?cod=513" target="_blank"><img src="./img/BOTONVOTA.png" style="margin.5em;"></a></span><br /><br />
				<span><a href="http://minecraft-mp.com/server-s67165" target="_blank"><img src="./img/half-banner-67165.png" style="margin.5em;"></a></span>
			</div>
		</div>-->
	</div>
	<?php require 'footer.php'; ?>
</body>

</html>