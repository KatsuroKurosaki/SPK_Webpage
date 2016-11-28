<?php
require 'cnf.php';
require 'class/SPK.php';
error_reporting(_DEBUGLVL);
$conn = SPK\GlobalFunc::getMysqlConn(_HOST,_USER,_PASS,_DDBB);

$datos_user = SPK\GlobalFunc::checkSession($conn, $_GET['s']);
if(!$datos_user){
	header("Location: index.php");
	die();
}
?><!DOCTYPE html>
<html lang="en">
    <?php require 'header.php'; ?>
    <body>
        <?php require 'navbar.php'; ?>
			<div class="jumbotron">
				<div class="container">
					<?php
					$sql = "SELECT fa_icon, staff_member
					FROM mc_ranks
					INNER JOIN mc_players ON mc_players.rankid = mc_ranks.id
					WHERE mc_players.id = ?;";
					$stmt = $conn->prepare($sql);
					if($stmt===false){
						die( $conn->error );
					}
					$stmt->bind_param( 'i',
						$datos_user['id_mc_player']
					);
					$stmt->execute();
					$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
					$staff_member = $data[0]['staff_member'];
					$stmt->close();
					?>
					<h1><i class="fa <?php echo $data[0]['fa_icon']; ?>"></i> <?php echo $datos_user['playername']; ?></h1>
					<div id="profileBtns">
						<button class="btn btn-default inicio" onclick="javascript:loadProfile('inicio');">Inicio</button>
						<button class="btn btn-default rango" onclick="javascript:loadProfile('rango');">Mi rango</button>
						<button class="btn btn-default disabled" onclick="javascript:alert('Proximamente');">Estadísticas</button>
						<button class="btn btn-default disabled" onclick="javascript:alert('Proximamente');">HelpOP</button>
						<button class="btn btn-default disabled" onclick="javascript:alert('Proximamente');">YouTube</button>
						<button class="btn btn-default disabled" onclick="javascript:alert('Proximamente');">Skin</button>
						<button class="btn btn-default disabled" onclick="javascript:alert('Proximamente');">Configuración</button>
					</div>
				</div>
			</div>
			
			<div class="perfil inicio container main" style="display:none;">
				<?php
				$sql = "SELECT SUM(tsdisconnect-tsconnect) AS tiempo_online FROM mc_players_log WHERE playername = ?;";
				$stmt = $conn->prepare($sql);
				if($stmt===false){
					die( $conn->error );
				}
				$stmt->bind_param( 's',
					$datos_user['playername']
				);
				$stmt->execute();
				$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				
				$tiempo_online = sec2dhms($data[0]['tiempo_online']);
				
				$sql = "SELECT uuid,email,connections,UNIX_TIMESTAMP(registerdate) as registerdate,UNIX_TIMESTAMP(lastlogin) as lastlogin,lastip
				FROM mc_players
				WHERE id = ?;";
				$stmt = $conn->prepare($sql);
				if($stmt===false){
					die( $conn->error );
				}
				$stmt->bind_param( 'i',
					$datos_user['id_mc_player']
				);
				$stmt->execute();
				$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				?>
				<div class="col-md-12">
					<h3 class="text-center">¡Bienvenido a tu perfil!</h3>
				</div>
				<div class="col-md-4">
					<h4>Núm. de jugador S.P.K.</h4>
					<h5><?php echo $datos_user['id_mc_player']; ?></h5>
				</div>
				<div class="col-md-4">
					<h4>Fecha de registro</h4>
					<h5 class="morphdate"><?php echo $data[0]['registerdate']; ?></h5>
				</div>
				<div class="col-md-4">
					<h4>E-mail registrado</h4>
					<h5><?php echo $data[0]['email']; ?></h5>
				</div>
				<div class="col-md-4">
					<h4>Tiempo total online</h4>
					<h5><?php echo $tiempo_online; ?></h5>
				</div>
				<div class="col-md-4">
					<h4>Fecha última conexión</h4>
					<h5 class="morphdate"><?php echo $data[0]['lastlogin']; ?></h5>
				</div>
				<div class="col-md-4">
					<h4>Conexiones</h4>
					<h5><?php echo $data[0]['connections']; ?></h5>
				</div>
				<div class="col-md-4">
					<h4>Última IP conocida</h4>
					<h5><?php echo $data[0]['lastip']; ?></h5>
				</div>
				<div class="col-md-4">
					<h4>UUID</h4>
					<h5><?php echo $data[0]['uuid']; ?></h5>
				</div>
			</div>
			
			<div class="perfil rango container main" style="display:none;">
				<div class="col-md-12">
					<h3 class="text-center">Gestión de rango</h3>
				</div>
				<?php
				$sql = "SELECT UNIX_TIMESTAMP(rankuntil) AS rankuntil, rank
				FROM mc_players
				INNER JOIN mc_ranks ON mc_ranks.id = mc_players.rankid
				WHERE mc_players.id = ?;";
				$stmt = $conn->prepare($sql);
				if($stmt===false){
					die( $conn->error );
				}
				$stmt->bind_param( 'i',
						$datos_user['id_mc_player']
					);
				$stmt->execute();
				$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
				$stmt->close();
				?>
				<div class="col-md-4">
					<h3>Rango activo:</h3>
					<h4 id="rank" style="height:1em;"><?php echo $data[0]['rank']; ?></h4>
					<hr/>
					<h3>Caducidad:</h3>
					<h4 id="rankuntil" style="height:1em;" class="DigiClock morphdate"><?php echo $data[0]['rankuntil']; ?></h4>
				</div>
				<div class="col-md-4">
					<h3>Rangos comprados</h3>
					<table class="table table-striped table-hover table-condensed">
						<thead>
							<tr><th>Rango</th><th>Días</th><th>&nbsp;</th><th>&nbsp;</th></tr>
						</thead>
						<tbody>
							<?php
							$sql = "SELECT web_transaction.id, rank, web_transaction.days
							FROM web_transaction
							INNER JOIN mc_ranks_pricing ON mc_ranks_pricing.id = web_transaction.id_rank_pricing
							INNER JOIN mc_ranks ON mc_ranks.id = mc_ranks_pricing.id_rank
							WHERE `status` = 'COMPLETE' AND id_mc_player = ? AND usado = 'N'
							ORDER BY web_transaction.id DESC;";
							$stmt = $conn->prepare($sql);
							if($stmt===false){
								die( $conn->error );
							}
							$stmt->bind_param( 'i',
								$datos_user['id_mc_player']
							);
							$stmt->execute();
							$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							
							foreach($data as $k=>$v){
								echo '<tr id="transaction-'.$v['id'].'"><td>'.$v['rank'].'</td><td>'.$v['days'].'</td><td><button class="btn btn-default" onclick="javascript:transfiereRango('.$v['id'].');">Transferir <i class="fa fa-exchange" aria-hidden="true"></i></button></td><td><button class="btn btn-success" onclick="javascript:activaRango('.$v['id'].');">Activar <i class="fa fa-check" aria-hidden="true"></i></button></td></tr>';
							}
							?>
						</thead>
					</table>
					<div class="text-center"><button class="btn btn-info" onclick="javascript:verHistorial();">Ver historial</button></div>
				</div>
				<?php if($staff_member == "N"){ ?>
					<div class="col-md-4">
						<h3>Rangos disponibles:</h3>
						<div class="row">
							<?php
							$sql = "SELECT id, rank, fa_icon
							FROM mc_ranks
							WHERE visible = 'Y'
							ORDER BY orderby ASC, rank DESC;";
							$stmt = $conn->prepare($sql);
							if($stmt===false){
								die( $conn->error );
							}
							$stmt->execute();
							$ranks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							
							foreach($ranks as $k=>$v){
								echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<a href="rangos.php?id='.$v['id']; if(isset($_GET['s'])){echo '&s='.$_GET['s'];} echo '" style="margin-bottom:.5em;" class="btn btn-default btn-block"><i class="fa fa-lg '.$v['fa_icon'].'"></i> <span class="fa-lg">'.$v['rank'].'</span></a>
								</div>';
							}
							?>
						</div>
					</div>
				<?php } ?>
			</div>
			
			<div class="perfil estadisticas container main" style="display:none;">
				<?php
				/*$sql = "SELECT uuid,email,connections,UNIX_TIMESTAMP(registerdate) as registerdate,UNIX_TIMESTAMP(lastlogin) as lastlogin,lastip
				FROM mc_players
				WHERE id = ?;";
				$stmt = $conn->prepare($sql);
				if($stmt===false){
					die( $conn->error );
				}
				$stmt->bind_param( 'i',
					$datos_user['id_mc_player']
				);
				$stmt->execute();
				$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
				$stmt->close();*/
				?>
				<div class="col-md-12">
					<h3 class="text-center">Estadísticas de minijuegos</h3>
				</div>
			</div>
			
			<div class="perfil helpop container main" style="display:none;">
				<div class="col-md-12">
					<h3 class="text-center">HelpOP</h3>
				</div>
			</div>
			
			<div class="perfil youtube container main" style="display:none;">
				<div class="col-md-12">
					<h3 class="text-center">YouTube</h3>
				</div>
			</div>
			
			<div class="perfil skin container main" style="display:none;">
				<div class="col-md-12">
					<h3 class="text-center">Skin</h3>
				</div>
			</div>
			
			<div class="perfil configuracion container main" style="display:none;">
				<div class="col-md-12">
					<h3 class="text-center">Configuración</h3>
				</div>
			</div>
			
			<script type="text/javascript">
				$(".morphdate").each(function(){
					$(this).text(uts2dt(parseInt($(this).text())));
				});
				
				function loadProfile(pagina){
					$("#profileBtns button").removeClass("btn-info");
					$("#profileBtns button."+pagina).addClass("btn-info");
					$(".perfil").hide();
					$("."+pagina).show();
				}
				
				if(qs("show")!=null){
					loadProfile(qs("show"));
				} else {
					loadProfile("inicio");
				}
				
				function verHistorial(){
					$.ajax({
						method: 'POST',
						url: 'api.php',
						data: {
							op:'histranks',
							s:qs("s")
						},
						timeout: 10000,
						beforeSend: function(jqXHR, settings) {
							//console.log(settings);
							spawnSpinner();
						},
						success: function (data, textStatus, jqXHR) {
							console.log(data);
							if(data.status == "ok"){
								tabla = '<table class="table table-striped table-hover table-condensed">';
								tabla += '<thead><tr><th>Rango</th><th>Fecha de compra</th><th>Días</th><th>Precio</th><th>Proveedor</th><th>Usado</th></tr></thead><tbody>';
								for (var index in data.data){
									tabla += '<tr><td>'+data.data[index].rank+'</td><td>'+uts2dt(data.data[index].created)+'</td><td>'+data.data[index].days+'</td><td>'+data.data[index].price+'€</td><td>'+data.data[index].provider+'</td><td>'+data.data[index].usado+'</td></tr>';
								}
								tabla += '</tbody></table>';
								spawnModal("Historial de rangos comprados",tabla,"Cerrar");
							} else {
								spawnTopAlert(data.msg,"warning");
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							//console.log(jqXHR.responseText);
							spawnTopAlert("Error de comunicación al consultar el historial.","danger");
						},
						complete: function(jqXHR, textStatus) {
							//console.log(textStatus);
							removeSpinner();
						}
					});
				}
				
				function transfiereRango(id){
					spawnModal("Transferir rango a otro jugador","Lo sentimos, esta opción aún no está disponible para tu cuenta. Si necesitas transferir un rango a otro jugador, contactanos a <i>soportespk@katsunet.com</i> y nos encargaremos de ello.","Cerrar");
				}
				
				function activaRango(id){
					spawnConfirmModal(
						"Activación de rangos",
						"Procederemos a activarte el rango en el server, ¿Estás seguro que deseas continuar?<br/><br/>Ten en cuenta que, una vez realizada la activación del rango, deberás hacer un cambio de modalidad en el server o desconectarte y conectarte de nuevo para que se active completamente.",
						function(){
							$.ajax({
							method: 'POST',
							url: 'api.php',
							data: {
								op:'enablerank',
								id:id,
								s:qs("s")
							},
							timeout: 10000,
							beforeSend: function(jqXHR, settings) {
								//console.log(settings);
								spawnSpinner();
							},
							success: function (data, textStatus, jqXHR) {
								//console.log(data);
								if(data.status == "ok"){
									letterTypingEffect("#rank",data.rank);
									letterTypingEffect("#rankuntil",uts2dt(data.fechautc),250);
									
									/*$("#rank").slideUp("slow",function(){ $(this).text(data.rank); $(this).slideDown()});
									$("#rankuntil").slideUp("slow",function(){ $(this).text(data.fecha); $(this).slideDown()});*/
									$("#transaction-"+id).fadeOut("slow",function(){ $(this).remove(); });
									
									spawnTopAlert(data.msg,"success");
								} else {
									spawnTopAlert(data.msg,"warning");
								}
							},
							error: function(jqXHR, textStatus, errorThrown) {
								//console.log(jqXHR.responseText);
								spawnTopAlert("Error de Internet al activar el rango solicitado.","danger");
							},
							complete: function(jqXHR, textStatus) {
								//console.log(textStatus);
								removeSpinner();
							}
						});
						}
					);
				}
			</script>
		<?php require 'footer.php'; ?>
    </body>
</html>
