<?php
if(!isset($_GET['s'])){
	header("Location: login.php");
	die();
}
require '../cnf.php';
require '../common.php';
require './check_session.php';
require '../class/Permission.php';

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha entrado a Inicio');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?><!DOCTYPE html>
<html lang="en">
    <?php require 'header.php'; ?>
    <body>
        <?php define("_FILE",basename(__FILE__, '.php')); require 'navbar.php'; ?>
		
		<div class="container main">
			<h1>Inicio</h1>
			<div class="row">
				<div class="col-md-12">
					<h3>Resumen de miembros del Staff</h3>
					<table class="table table-striped table-hover table-condensed">
						<thead>
							<!--<tr>
								<th colspan="2">&nbsp;</th>
								<th>Núm conexiones</th>
								<th>Total tiempo online</th>
								<th>Promedio tiempo online</th>
								<th>&nbsp;</th>
							</tr>-->
							<tr>
								<th>
									Nombre Staff
								</th>
								<th>
									Última conexión
									<i class="fa fa-sort-amount-desc fa-lg" aria-hidden="true" title="Orden descendente" onclick="javscript:ordenarColumna('#tblResumenMods','lastlogin',0);"></i>
									<i class="fa fa-sort-amount-asc fa-lg" aria-hidden="true" title="Orden ascendente" onclick="javscript:ordenarColumna('#tblResumenMods','lastlogin',1);"></i></th>
								<th>
									Núm conexiones
									<i class="fa fa-sort-amount-desc fa-lg" aria-hidden="true" title="Orden descendente" onclick="javscript:ordenarColumna('#tblResumenMods','connections',0);"></i>
									<i class="fa fa-sort-amount-asc fa-lg" aria-hidden="true" title="Orden ascendente" onclick="javscript:ordenarColumna('#tblResumenMods','connections',1);"></i></th>
								</th>
								<th>
									Total online
									<i class="fa fa-sort-amount-desc fa-lg" aria-hidden="true" title="Orden descendente" onclick="javscript:ordenarColumna('#tblResumenMods','sumonline',0);"></i>
									<i class="fa fa-sort-amount-asc fa-lg" aria-hidden="true" title="Orden ascendente" onclick="javscript:ordenarColumna('#tblResumenMods','sumonline',1);"></i></th>
								</th>
								<th>
									Promedio online
									<i class="fa fa-sort-amount-desc fa-lg" aria-hidden="true" title="Orden descendente" onclick="javscript:ordenarColumna('#tblResumenMods','avgonline',0);"></i>
									<i class="fa fa-sort-amount-asc fa-lg" aria-hidden="true" title="Orden ascendente" onclick="javscript:ordenarColumna('#tblResumenMods','avgonline',1);"></i></th>
								</th>
								<th>
									Notas
								</th>
							</tr>
						</thead>
						<tbody id="tblResumenMods">
							<?php
							$sql = "SELECT playername, UNIX_TIMESTAMP(lastlogin) AS lastlogin, connections, staff_inactivo
							FROM mc_players
							WHERE staff_member = 'Y'
							ORDER BY lastlogin DESC;";
							$stmt = $conn->prepare($sql);
							if($stmt===false){
								die( $conn->error );
							}
							$stmt->execute();
							$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							
							foreach($data as $k=>$v){
								$sql = "SELECT SUM(tsdisconnect-tsconnect) AS sumonline, ROUND(AVG(tsdisconnect-tsconnect),0) AS avgonline
								FROM mc_players_log
								WHERE playername = '".$v['playername']."';";
								$stmt = $conn->prepare($sql);
								if($stmt===false){
									die( $conn->error );
								}
								$stmt->execute();
								$online = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
								$stmt->close();
								echo '<tr class="small" lastlogin="'.$v['lastlogin'].'" connections="'.$v['connections'].'" sumonline="'.$online[0]['sumonline'].'" avgonline="'.$online[0]['avgonline'].'">
									<td>'.$v['playername'].'</td>
									<td><span class="morphdate">'.$v['lastlogin'].'</span><br>'.timeHace($v['lastlogin']).'</td>
									<td>'.$v['connections'].'</td>
									<td>'.sec2dhms($online[0]['sumonline']).'</td>
									<td>'.sec2dhms($online[0]['avgonline']).'</td>
									<td>'.$v['staff_inactivo'].'</td>
								</tr>';
							}
							?>
						</thead>
					</table>
				</div>
				
			</div>
		</div>
		
		<script type="text/javascript">
		setTimeout(function(){helpopReq(0);},3000);
		
		$(".morphdate").each(function(){
			$(this).text(uts2dt(parseInt($(this).text())));
		});
		
		if (!("Notification" in window)) {
			spawnModal("Sin servicio de notificaciones.","No se puede activar el servicio de notificaciones en tu navegador web.","Cerrar");
		} else if (Notification.permission !== 'denied') {
			Notification.requestPermission(function (permission) {
				// If the user accepts, let's create a notification
				if (permission === "granted" && qs("welcome") == "true") {
					var options = {
						body: "<?php echo $datos_user[0]['playername']; ?>",
						icon: '../favicon.png',
					}
					var n = new Notification("¡Bienvenid@ de nuevo!",options);
					setTimeout(n.close.bind(n), 10000);
				}
			});
		}
		
		function ordenarColumna(table,atributo,tipo){
			//table: referencia al tbody
			//atributo: por que campo leer los datos a ordenar
			//tipo: 0=descendiente, 1=ascendiente
			datos = [];
			$(table+" tr").each(function(a,b){
				datos.push( {valor: parseInt($(b).attr(atributo)), html:b} ); });
				datos.sort(function(a,b){
					if(a.valor>b.valor){
						if(tipo){
							return 1;
						} else {
							return -1;
						}
					} else if (a.valor<b.valor) {
						if(tipo){
							return -1;
						} else {
							return 1;
						}
					} else {
						return 0;
					}
				});
				$(table).html("");
				for (i=0; i<datos.length; i++) {
					$(datos[i].html).appendTo(table);
				}
			delete datos;
		}
		</script>
		
		<?php require 'footer.php'; ?>
    </body>
</html>
