<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';
require '../class/Permission.php';
if(!Permission::checkPermission($datos_user[0]['staff_perms'],Permission::STAFF_TRANSACTION)){
	die("403: Denied");
}

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha entrado a transacciones');";
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
				<h1>Transacciones</h1>
				<div>
					<label>Escribe el nombre de Minecraft:</label>
					<select name="mc_name" class="form-control" style="width:300px;" onchange="javascript:playerSelect(event);"></select>
				</div>
				<hr/>
				
				<table id="transactionTbl" class="table table-striped table-hover table-condensed">
					<thead>
						<tr><th>Id</th><th>Jugador</th><th>Rango</th><th>Proveedor</th><th>Fecha creación</th><th>Fecha compra</th><th>Estado</th><th>Usado?</th><th>Finalizar?</th></tr>
					</thead>
					<tbody></tbody>
				</table>
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
									return {id: obj.id, text:obj.playername};
								})
							}
						}
					}
				});
				
				setTimeout(function(){helpopReq(0);},3000);
				
				function playerSelect(e){
					$.ajax({
						method: 'POST',
						url: 'api.php?s='+qs("s"),
						data: {
							op:'transactionlist',
							id:$("select[name='mc_name']").val()
						},
						timeout: 10000,
						beforeSend: function(jqXHR, settings) {
							//console.log(settings);
							spawnSpinner();
						},
						success: function (data, textStatus, jqXHR) {
							console.log(data);
							$("#transactionTbl tbody").html("");
							for(var i in data.data){
								col = '<tr>'+
									'<td>'+data.data[i].id+'</td>'+
									'<td>'+$("select[name='mc_name'] option:selected").text()+'</td>'+
									'<td>'+data.data[i].rank+'</td>'+
									'<td>'+data.data[i].provider+'</td>'+
									'<td>'+uts2dt(data.data[i].created)+'</td>'+
									'<td id="fechbuy-'+data.data[i].id+'">'+uts2dt(data.data[i].web_return)+'</td>'+
									'<td id="txnst-'+data.data[i].id+'">'+data.data[i].status+'</td>'+
									'<td>'+data.data[i].usado+'</td>'+
									'<td><button type="button" class="btn btn-info btn-xs" onclick="javascript:finishTransaction('+data.data[i].id+');">Finalizar <i class="fa fa-chevron-right" aria-hidden="true"></i></button></td>'+
								'</tr>';
								$("#transactionTbl tbody").append(col);
								delete col;
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							//console.log(jqXHR.responseText);
							spawnTopAlert("Se ha producido un error de comunicación. Comprueba tu conexión a Internet","danger");
						},
						complete: function(jqXHR, textStatus) {
							//console.log(textStatus);
							removeSpinner();
						}
					});
				}
				
				function finishTransaction(id){
					spawnConfirmModal("Finalizar transacción","¿Estás seguro que quieres finalizar la transacción?",
					function (){
						$.ajax({
							method: 'POST',
							url: 'api.php?s='+qs("s"),
							data: {
								op:'transactionfinish',
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
								if(data.status=="ok"){
									$("#fechbuy-"+id).html( moment().format('YYYY/MM/DD HH:mm:ss') );
									$("#txnst-"+id).html('COMPLETE');
								}
							},
							error: function(jqXHR, textStatus, errorThrown) {
								//console.log(jqXHR.responseText);
								spawnTopAlert("Se ha producido un error de comunicación. Comprueba tu conexión a Internet","danger");
							},
							complete: function(jqXHR, textStatus) {
								//console.log(textStatus);
								removeSpinner();
							}
						});
					});
				}
			</script>
		<?php require 'footer.php'; ?>
    </body>
</html>
