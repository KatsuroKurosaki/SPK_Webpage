<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';
require '../class/Permission.php';
if(!Permission::checkPermission($datos_user[0]['staff_perms'],Permission::STAFF_RANKS)){
	die("403: Denied");
}

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha entrado a Rangos');";
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
				<h1>Rangos</h1>
				<div class="row">
					<div class="col-md-4"><input type="text" placeholder="Filtrar por jugador de Minecraft" class="form-control" onkeyup="javascript:filtrarJugadores(event);"/></div>
					<div class="col-md-4"><button type="button" class="btn btn-block btn-info" onclick="javascript:addRank();"><i class="fa fa-plus fa-lg" aria-hidden="true"></i> Añadir nuevo rango a jugador</button></div>
					<div class="col-md-4"><button type="button" class="btn btn-block btn-info" onclick="javascript:resyncRanks();"><i class="fa fa-refresh fa-lg" aria-hidden="true"></i> Resincronizar todos los rangos</button></div>
				</div>
				<div>&nbsp;</div>
				<table id="ranksTbl" class="table table-striped table-hover">
					<thead>
						<tr>
							<th>Jugador</th>
							<th>Rango</th>
							<th>Caducidad</th>
							<th>Canal de YouTube</th>
							<?php
							if(Permission::checkPermission($datos_user[0]['staff_perms'],Permission::STAFF_RANGOSEXTRACMDS)){
								echo '<th>Comandos extra</th>';
							} ?>
							<th>Retirar rango</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sql = "SELECT mc_players.id, playername, UNIX_TIMESTAMP(rankuntil) as caducidad, rank, canal_yt
						FROM mc_players
						INNER JOIN mc_ranks ON mc_ranks.id = mc_players.rankid
						WHERE rankupdatable = 'N'
						ORDER BY caducidad ASC,mc_ranks.id ASC, playername;";
						$stmt = $conn->prepare($sql);
						if($stmt===false){
							die( $conn->error );
						}
						$stmt->execute();
						$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
						$stmt->close();
						
						$ahorats = time();
						
						foreach($data as $k=>$v){
							echo '<tr id="player-'.$v['id'].'" playername="'.$v['playername'].'" class="small';
							if(($v['caducidad'] - $ahorats) < 259200) { // 259200 = 3 días
								echo " text-danger";
							} elseif(($v['caducidad'] - $ahorats) < 604800) { // 604800 = 7 días
								echo " text-warning";
							} else {
								echo " text-success";
							}
							
							echo '"><td>'.$v['playername'].'</td><td>'.$v['rank'].'<td><i class="fa fa-calendar fa-lg cursor-pointer" aria-hidden="true" onclick="javascript:spawnRemoteModal(\'ranks_cad.php?s='.$_GET['s'].'\',{id:'.$v['id'].'});"></i> <span class="morphdate">'.$v['caducidad'].'</span></td><td>';
							if($v['canal_yt'] == ""){
								echo "<i>Sin datos</i>";
							} else {
								echo '<a href="'.$v['canal_yt'].'" target="_blank">Ver canal <i class="fa fa-external-link fa-lg" title="'.$v['canal_yt'].'" aria-hidden="true"></i></a>';
							}
							echo '</td>';
							if(Permission::checkPermission($datos_user[0]['staff_perms'],Permission::STAFF_RANGOSEXTRACMDS)){
								echo '<td class="cursor-pointer" onclick="javascript:extraCmds('.$v['id'].');"><i class="fa fa-street-view fa-lg" aria-hidden="true"></i> Ver permisos</td>';
							}
							echo '<td class="cursor-pointer" onclick="javascript:removeRank('.$v['id'].');"><i class="fa fa-ban fa-lg" aria-hidden="true"></i> Retirar</td></tr>';
						}
						?>
					</tbody>
				</table>
			</div>
			
			<script type="text/javascript">
				$(".morphdate").each(function(){
					$(this).text(uts2dt(parseInt($(this).text())));
				});
				
				setTimeout(function(){helpopReq(0);},3000);
				
				function filtrarJugadores(event){
					$("#ranksTbl tbody tr").show();
					if(event.target.value != "") {
						$("#ranksTbl tbody tr").each(function(){
							if ( $(this).attr("playername").toLowerCase().indexOf( event.target.value.toLowerCase() ) == -1 ){
								$(this).hide();
							}
						});
					}
				}
			
				function resyncRanks(){
					$.ajax({
						method: 'POST',
						url: 'api.php?s='+qs("s"),
						data: {
							op:'resyncranks'
						},
						timeout: 10000,
						beforeSend: function(jqXHR, settings) {
							//console.log(settings);
							spawnSpinner();
						},
						success: function (data, textStatus, jqXHR) {
							console.log(data);
							spawnTopAlert(data.msg,data.color);
						},
						error: function(jqXHR, textStatus, errorThrown) {
							//console.log(jqXHR.responseText);
							spawnTopAlert("Error de comunicación. Verifica tu conexión a Internet.","danger");
						},
						complete: function(jqXHR, textStatus) {
							//console.log(textStatus);
							removeSpinner();
						}
					});
				}
				
				function removeRank(id){
					spawnConfirmModal("Retirar rango","¿Retiramos el rango al jugador?",
					function(){
						console.log(id);
						$.ajax({
							method: 'POST',
							url: 'api.php?s='+qs("s"),
							data: {
								op:'removeplrank',
								id:id
							},
							timeout: 10000,
							beforeSend: function(jqXHR, settings) {
								//console.log(settings);
								spawnSpinner();
							},
							success: function (data, textStatus, jqXHR) {
								console.log(data);
								spawnTopAlert(data.msg,data.color);
								if(data.status == "ok"){
									$("#player-"+id).fadeOut("slow",function(){$(this).remove();});
								}
							},
							error: function(jqXHR, textStatus, errorThrown) {
								//console.log(jqXHR.responseText);
								spawnTopAlert("Error de comunicación. Verifica tu conexión a Internet.","danger");
							},
							complete: function(jqXHR, textStatus) {
								//console.log(textStatus);
								removeSpinner();
							}
						});
					});
				}
				
				function addRank(){
					spawnRemoteModal("ranks_add.php?s="+qs("s"));
				}
				
				function extraCmds(id){
					spawnRemoteModal("ranks_xtraperms.php?s="+qs("s"),{id:id});
				}
			</script>
		<?php require 'footer.php'; ?>
    </body>
</html>
