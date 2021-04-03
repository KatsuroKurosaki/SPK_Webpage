<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';
require '../class/PermissionsEX.php';
$mc_modes = PermissionsEx::getMcModes($conn);

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha abierto la ventana para añadir comandos individuales a un jugador');";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title">Permisos adicionales de jugador</h4>
</div>
<div class="modal-body">
	<label>Añadir nuevo permiso:</label>
	<table class="table table-condensed">
		<tr>
			<td>Modalidad: </td>
			<td> <select class="form-control" name="modename" style="width:100%;"><?php foreach ($mc_modes as $k => $v) {
																						echo '<option value="' . $v['id'] . '">' . $v['modename'] . '</option>';
																					} ?></select></td>
		</tr>
		<tr>
			<td>Permiso: </td>
			<td><input type="text" class="form-control" name="nodeperm" /></td>
		</tr>
		<tr>
			<td colspan="2" class="text-right"><button class="btn btn-default" onclick="javascript:addextracmd();">Añadir</button>
		</tr>
	</table>
	<hr />
	<label>Permisos extra actuales</label>
	<table id="extracmdtbl" class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th>Modalidad</th>
				<th>Permiso</th>
				<th>Retirar?</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sql = "SELECT mc_players_extracmds.id, permission_node, modename
			FROM mc_players_extracmds
			INNER JOIN mc_modes ON mc_modes.id = id_mc_mode
			WHERE id_mc_player = ?;";
			$stmt = $conn->prepare($sql);
			if ($stmt === false) {
				die($conn->error);
			}
			$stmt->bind_param(
				'i',
				$_POST['id']
			);
			$stmt->execute();
			$extracmd = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
			$stmt->close();

			foreach ($extracmd as $k => $v) {
				echo '<tr id="extraperm-' . $v['id'] . '"><td>' . $v['modename'] . '</td><td>' . $v['permission_node'] . '</td><td onclick="javascript:delextracmd(' . $v['id'] . ');" class="text-danger"><i class="fa fa-lg fa-trash-o" aria-hidden="true"></i></td></tr>';
			}
			?>
		</tbody>
	</table>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times fa-lg" aria-hidden="true"></i> Cancelar y cerrar</button>
	<button type="button" class="btn btn-success" data-dismiss="modal" onclick="javascript:resyncRanks();"><i class="fa fa-check fa-lg" aria-hidden="true"></i> Aceptar y resincronizar</button>
</div>
<script type="text/javascript">
	$(".modal-body select[name='modename']").select2({
		minimumResultsForSearch: -1
	});

	function addextracmd() {
		if ($.trim($(".modal-body input[name='nodeperm']").val()) != "") {
			$.ajax({
				method: 'POST',
				url: 'api.php?s=' + qs("s"),
				data: {
					op: 'addxtraperm',
					idp: <?php echo $_POST['id']; ?>,
					mode: $(".modal-body select[name='modename']").val(),
					perm: $(".modal-body input[name='nodeperm']").val()
				},
				timeout: 10000,
				beforeSend: function(jqXHR, settings) {
					//console.log(settings);
					spawnSpinner();
				},
				success: function(data, textStatus, jqXHR) {
					console.log(data);
					//spawnTopAlert(data.msg,data.color);
					$("#extracmdtbl tbody").append('<tr id="extraperm-' + data.idnew + '"><td>' + $(".modal-body select[name='modename'] option:selected").text() + '</td><td>' + $(".modal-body input[name='nodeperm']").val() + '</td><td onclick="javascript:delextracmd(' + data.idnew + ');" class="text-danger"><i class="fa fa-lg fa-trash-o" aria-hidden="true"></i></td></tr>');
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
			spawnAlert("El permiso no puede quedar en blanco", "warning", ".modal-footer");
		}
	}

	function delextracmd(id) {
		$.ajax({
			method: 'POST',
			url: 'api.php?s=' + qs("s"),
			data: {
				op: 'delxtraperm',
				id: id
			},
			timeout: 10000,
			beforeSend: function(jqXHR, settings) {
				//console.log(settings);
				spawnSpinner();
			},
			success: function(data, textStatus, jqXHR) {
				console.log(data);
				$("#extraperm-" + id).fadeOut("slow", function() {
					$(this).remove();
				});
				spawnAlert("Borrado el permiso extra correctamente.", "success", ".modal-footer");
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
	}
</script>