<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';

$sql = "SELECT rankuntil, canal_yt FROM mc_players WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt===false){ die( $conn->error ); }
$stmt->bind_param( 'i',
	$_POST['id']
);
$stmt->execute();
$player = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha abierto la ventana para editar detalles de rango');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title">Editar detalles de rango</h4>
</div>
<div class="modal-body">
	<table class="table table-condensed">
		<tr><td>Caducidad: </td><td><input type="text" class="form-control" name="expire" style="width:100%;" readonly="readonly" value="<?php echo $player[0]['rankuntil']; ?>"/></td></tr>
		<tr><td>Link a canal YT: </td><td><input type="text" class="form-control" name="yt_channel" style="width:100%;" value="<?php echo $player[0]['canal_yt']; ?>" placeholder="URL completa: https://www.youtube.com/.../videos"/></td></tr>
	</table>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times fa-lg" aria-hidden="true"></i> Cerrar</button>
	<button type="button" class="btn btn-success" onclick="javascript:addNewRank();"><i class="fa fa-check fa-lg" aria-hidden="true"></i> Guardar</button>
</div>
<script type="text/javascript">
	$(".modal-body input[name='expire']").datetimepicker({
		format: "yyyy-mm-dd HH:ii",
		autoclose:true,
		startDate: new Date(),
		endDate : new Date('2035-12-31 23:59:59'),
		todayHighlight: true,
		language:'es',
		weekStart:1
	});
	
	function addNewRank(){
		$.ajax({
			method: 'POST',
			url: 'api.php?s='+qs("s"),
			data: {
				op:'editrankdetail',
				mc_nameid:<?php echo $_POST['id']; ?>,
				expire:moment($(".modal-body input[name='expire']").val()).unix(),
				canal_yt:$(".modal-body input[name='yt_channel']").val()
			},
			timeout: 10000,
			beforeSend: function(jqXHR, settings) {
				//console.log(settings);
				spawnSpinner();
			},
			success: function (data, textStatus, jqXHR) {
				console.log(data);
				spawnAlert(data.msg,data.color,".modal-footer");
				if(data.status=="ok"){
					location.reload(true);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				//console.log(jqXHR.responseText);
				spawnAlert("Error de comunicación. Verifica tu conexión a Internet.","danger",".modal-footer");
			},
			complete: function(jqXHR, textStatus) {
				//console.log(textStatus);
				removeSpinner();
			}
		});
	}
</script>
