<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';
require '../class/Permission.php';
if(!Permission::checkPermission($datos_user[0]['staff_perms'],Permission::STAFF_IPLIST)){
	die("403: Denied");
}

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha entrado a Informacion de jugador');";
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
				<h1>Información de jugador</h1>
				<div>
					<label>Escribe el nombre de Minecraft:</label>
					<select name="mc_name" class="form-control" style="width:300px;" onchange="javascript:playerSelect();"></select>
					<button type="button" class="btn btn-default btn-sm" onclick="javascript:playerSelect();"><i class="fa fa-search" aria-hidden="true"></i> Búscar</button>
				</div>
				<hr/>
				<div class="row">
					<div class="col-md-6">
						<h4>Tiempos de conexión</h4>
						<table id="tblConn" class="table table-striped table-hover table-condensed">
							<thead>
								<tr><th>Hora conexión</th><th>Hora desconexión</th><th>Tiempo online</th><th>Dirección IP</th></tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div class="col-md-6">
						<h4>Posibles multicuentas</h4>
						<table id="tblMult" class="table table-striped table-hover table-condensed">
							<thead>
								<tr><th>Dirección IP</th><th>Otros nombres con esta IP</th></tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			
			<script type="text/javascript">
				$("select[name='mc_name']").select2({
					minimumInputLength: 3,
					ajax: {
						url: "api.php?s="+qs("s"),
						dataType: 'json',
						method:'POST',
						delay: 500,
						cache: true,
						data: function (params) {
							return {
								term: params.term,
								op: 'iplistsearchplayer',
								s: qs("s")
							};
						},
						processResults: function (data, params) {
							return {
								results: $.map(data.data,function(obj){
									return {id: obj.playername, text:obj.playername};
								})
							}
						}
					}
				});
				
				setTimeout(function(){helpopReq(0);},3000);
				
				function morphDate(){
					$(".morphdate").each(function(){
						$(this).text(uts2dt(parseInt($(this).text())));
					});
				}
				
				function playerSelect(){
					$.ajax({
						method: 'POST',
						url: 'api.php',
						data: {
							s:qs("s"),
							op:'iplistsearchtimes',
							playername:$("select[name='mc_name']").val()
						},
						timeout: 10000,
						beforeSend: function(jqXHR, settings) {
							//console.log(settings);
							spawnSpinner();
						},
						success: function (data, textStatus, jqXHR) {
							console.log(data);
							$("#tblConn tbody").html("");
							for(var i in data.data){
								$("#tblConn tbody").append('<tr class="small"><td>'+uts2dt(data.data[i].tsconnect)+'</td><td>'+uts2dt(data.data[i].tsdisconnect)+'</td><td>'+data.data[i].onlinetime+'</td><td>'+data.data[i].ipaddress+'</td></tr>');
							}
							playerSelect2();
						},
						error: function(jqXHR, textStatus, errorThrown) {
							//console.log(jqXHR.responseText);
							spawnTopAlert("Se ha producido un error de comunicación.","danger");
							removeSpinner();
						},
						complete: function(jqXHR, textStatus) {
							//console.log(textStatus);
						}
					});
				}
				function playerSelect2(){
					$.ajax({
						method: 'POST',
						url: 'api.php',
						data: {
							s:qs("s"),
							op:'iplistsearchmultacc',
							playername:$("select[name='mc_name']").val()
						},
						timeout: 10000,
						beforeSend: function(jqXHR, settings) {
							//console.log(settings);
						},
						success: function (data, textStatus, jqXHR) {
							//console.log(data);
							$("#tblMult tbody").html("");
							for(var i in data.data){
								$("#tblMult tbody").append('<tr class="small"><td>'+data.data[i].ipaddress+'</td><td id="ip-'+i+'"><i class="fa fa-cog fa-spin fa-lg"></i></td></tr>');
								playerSelect3(data.data[i].ipaddress,i);
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							//console.log(jqXHR.responseText);
							spawnTopAlert("Se ha producido un error de comunicación.","danger");
						},
						complete: function(jqXHR, textStatus) {
							//console.log(textStatus);
							removeSpinner();
						}
					});
				}
				
				function playerSelect3(ip,id){
					console.log(ip);
					$.ajax({
						method: 'POST',
						url: 'api.php',
						data: {
							s:qs("s"),
							op:'iplistsearchmultip',
							ip:ip,
							playername:$("select[name='mc_name']").val()
						},
						timeout: 10000,
						beforeSend: function(jqXHR, settings) {
							//console.log(settings);
						},
						success: function (data, textStatus, jqXHR) {
							if(data.data.length>0){
								$("#ip-"+id).html("");
								for (var i in data.data){
									$("#ip-"+id).append(data.data[i].playername+"<br/>");
								}
							} else {
								$("#ip-"+id).html("<i>Sin multicuentas.</i>");
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							//console.log(jqXHR.responseText);
							$("#ip-"+id).html("Error.");
						},
						complete: function(jqXHR, textStatus) {
							//console.log(textStatus);
						}
					});
				}
			</script>
		<?php require 'footer.php'; ?>
    </body>
</html>
