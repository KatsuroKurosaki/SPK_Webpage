<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';
require '../class/PermissionsEX.php';
$mc_modes = PermissionsEx::getMcModes($conn);
$sql = "SELECT id, rank FROM mc_ranks WHERE visible_staffpage ORDER BY orderby;";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
	die($conn->error);
}
$stmt->execute();
$ranks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha abierto la ventana para añadir un rango a jugador');";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title">Añadir rango a jugador</h4>
</div>
<div class="modal-body">
	<table class="table table-condensed">
		<tr>
			<td>Jugador: </td>
			<td><select class="form-control" name="mc_name" style="width:100%;"></select></td>
		</tr>
		<tr>
			<td>Rango: </td>
			<td><select class="form-control" name="mc_rank" style="width:100%;"><?php foreach ($ranks as $k => $v) {
																					echo '<option value="' . $v['id'] . '">' . $v['rank'] . '</option>';
																				} ?></select></td>
		</tr>
		<tr>
			<td>Caducidad: </td>
			<td><input type="text" class="form-control" name="expire" style="width:100%;" readonly="readonly" /></td>
		</tr>
		<tr>
			<td>Link a canal YT: </td>
			<td><input type="text" class="form-control" name="yt_channel" style="width:100%;" placeholder="URL completa: https://www.youtube.com/.../videos" /></td>
		</tr>
	</table>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times fa-lg" aria-hidden="true"></i> Cancelar</button>
	<button type="button" class="btn btn-success" onclick="javascript:addNewRank();"><i class="fa fa-check fa-lg" aria-hidden="true"></i> Aceptar</button>
</div>
<script type="text/javascript">
	$(".modal-body select[name='mc_name']").select2({
		minimumInputLength: 3,
		ajax: {
			url: "api.php?s=" + qs("s"),
			dataType: 'json',
			method: 'POST',
			delay: 500,
			cache: true,
			data: function(params) {
				return {
					term: params.term,
					op: 'iplistsearchplayer',
					s: qs("s")
				};
			},
			processResults: function(data, params) {
				return {
					results: $.map(data.data, function(obj) {
						return {
							id: obj.id,
							text: obj.playername
						};
					})
				}
			}
		}
	});

	$(".modal-body select[name='mc_rank']").select2({
		minimumResultsForSearch: -1
	});

	$(".modal-body input[name='expire']").datetimepicker({
		format: "yyyy-mm-dd HH:ii",
		autoclose: true,
		startDate: new Date(),
		endDate: new Date('2035-12-31 23:59:59'),
		todayHighlight: true,
		language: 'es',
		weekStart: 1
	});

	function addNewRank() {
		if ($.trim($(".modal-body select[name='mc_name']").val()) != "") {
			if ($.trim($(".modal-body input[name='expire']").val()) != "") {
				$.ajax({
					method: 'POST',
					url: 'api.php?s=' + qs("s"),
					data: {
						op: 'addewplrank',
						mc_name: $(".modal-body select[name='mc_name'] option:selected").text(),
						mc_nameid: $(".modal-body select[name='mc_name']").val(),
						mc_rank: $(".modal-body select[name='mc_rank'] option:selected").text(),
						mc_rankid: $(".modal-body select[name='mc_rank']").val(),
						expire: moment($(".modal-body input[name='expire']").val()).unix(),
						canal_yt: $(".modal-body input[name='yt_channel']").val()
					},
					timeout: 10000,
					beforeSend: function(jqXHR, settings) {
						//console.log(settings);
						spawnSpinner();
					},
					success: function(data, textStatus, jqXHR) {
						console.log(data);
						spawnAlert(data.msg, data.color, ".modal-footer");
						if (data.status == "ok") {
							location.reload(true);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						//console.log(jqXHR.responseText);
						spawnAlert("Error de comunicación. Verifica tu conexión a Internet.", "danger", ".modal-footer");
					},
					complete: function(jqXHR, textStatus) {
						//console.log(textStatus);
						removeSpinner();
					}
				});
			} else {
				spawnAlert("No has seleccionado la fecha de caducidad del rango.", "danger", ".modal-footer");
			}
		} else {
			spawnAlert("El nombre de minecraft no puede quedar en blanco.", "danger", ".modal-footer");
		}
	}
</script>