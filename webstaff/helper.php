<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';
require '../class/Permission.php';
if (!Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_MIHELPER)) {
	die("403: Denied");
}

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha entrado a Mi helper');";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/

$sql = "SELECT staff_member_helper
FROM mc_players
WHERE id = ?;";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
	$out['status'] = "ko";
	$out['msg'] = $conn->error;
	die(json_encode($out));
}
$stmt->bind_param(
	'i',
	$datos_user[0]['id_mc_player']
);
$stmt->execute();
if ($stmt === false) {
	$out['status'] = "ko";
	$out['msg'] = $stmt->error;
	die(json_encode($out));
}
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<?php require 'header.php'; ?>

<body>
	<?php define("_FILE", basename(__FILE__, '.php'));
	require 'navbar.php'; ?>
	<div class="container main">
		<h1>Mi helper</h1>
		<hr />
		<?php
		if ($data[0]['staff_member_helper'] == "") {
		?>
			<label>No tienes asignado un helper personal. ¿Quieres contratar a alguien?</label>
			<div>
				<select name="mc_name" class="form-control" style="width:300px;"></select>
				<button type="button" class="btn btn-success btn-sm" onclick="javascript:contratarHelper();">Contratar <i class="fa fa-fire" aria-hidden="true"></i></button>
			</div>
			<script type="text/javascript">
				$("select[name='mc_name']").select2({
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

				function contratarHelper() {
					if ($("select[name='mc_name']").val() != undefined) {
						spawnConfirmModal("Contratar a helper", "¿Estás seguro que quieres contratar a " + $("select[name='mc_name'] option:selected").text() + " como tu helper?",
							function() {
								$.ajax({
									method: 'POST',
									url: 'api.php?s=' + qs("s"),
									data: {
										op: 'hirehelper',
										mc_name: $("select[name='mc_name'] option:selected").text(),
										mc_nameid: $("select[name='mc_name']").val()
									},
									timeout: 10000,
									beforeSend: function(jqXHR, settings) {
										//console.log(settings);
										spawnSpinner();
									},
									success: function(data, textStatus, jqXHR) {
										console.log(data);
										spawnTopAlert(data.msg, data.color);
										if (data.status == "ok") {
											location.reload(true);
										}
									},
									error: function(jqXHR, textStatus, errorThrown) {
										//console.log(jqXHR.responseText);
										spawnTopAlert("Error de comunicación. Verifica tu conexión a Internet.", "danger");
									},
									complete: function(jqXHR, textStatus) {
										//console.log(textStatus);
										removeSpinner();
									}
								});
							});
					} else {
						spawnModal("Error", "No has buscado un nombre de Minecraft para contratarlo como helper.", "Cerrar")
					}
				}
			</script>
		<?php
		} else {
			$sql = "SELECT playername, gauthcode
					FROM mc_players
					WHERE id = ?;";
			$stmt = $conn->prepare($sql);
			if ($stmt === false) {
				$out['status'] = "ko";
				$out['msg'] = $conn->error;
				die(json_encode($out));
			}
			$stmt->bind_param(
				'i',
				$data[0]['staff_member_helper']
			);
			$stmt->execute();
			if ($stmt === false) {
				$out['status'] = "ko";
				$out['msg'] = $stmt->error;
				die(json_encode($out));
			}
			$helper = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
			$stmt->close();
		?>
			<div>
				<label>Este es tu helper personal: <?php echo $helper[0]['playername']; ?>.</label>
				<button type="button" class="btn btn-danger btn-sm" onclick="javascript:despedirHelper();">Despedir <i class="fa fa-sign-out" aria-hidden="true"></i></button>
			</div>
			<hr />
			<div class="row">
				<div class="col-md-6">
					<h4>Tiempos de conexión</h4>
					<table id="tblConn" class="table table-striped table-hover table-condensed">
						<thead>
							<tr>
								<th>Hora conexión</th>
								<th>Hora desconexión</th>
								<th>Tiempo online</th>
								<th>Dirección IP</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="col-md-6">
					<h4>Datos de acceso a la web de Staff</h4>
					<label>Descargar la APP Google Autenticator para uno de estos dispositivos</label>
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-android fa-lg" aria-hidden="true"></i></div>
						<input type="text" class="form-control" value="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" readonly>
					</div>
					<div>&nbsp;</div>
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-apple fa-lg" aria-hidden="true"></i></div>
						<input type="text" class="form-control" value="https://itunes.apple.com/en/app/google-authenticator/id388497605" readonly>
					</div>
					<div>&nbsp;</div>
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-windows fa-lg" aria-hidden="true"></i></div>
						<input type="text" class="form-control" value="https://www.microsoft.com/en-us/store/p/authenticator/9nblggh08h54" readonly>
					</div>
					<div>&nbsp;</div>
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-chrome fa-lg" aria-hidden="true"></i></div>
						<input type="text" class="form-control" value="https://chrome.google.com/webstore/detail/authenticator/bhghoamapcdpbohphigoooaddinpkbai" readonly>
					</div>
					<div>&nbsp;</div>
					<label>Facilitar este <i>SECRET</i> a tu helper:</label>
					<div class="input-group">
						<div class="input-group-addon"><i class="fa fa-user-secret fa-lg" aria-hidden="true"></i></div>
						<input type="text" class="form-control" value="<?php echo $helper[0]['gauthcode']; ?>" readonly>
					</div>
					<h6 class="text-danger">Si el secret está en blanco, debes despedir y contratar de nuevo a tu helper para que se genere uno.</h6>
					<h6>En caso de problemas con el acceso a la web de staff, contacta con un OWNER para revisarlo.</h6>
				</div>
			</div>

			<script type="text/javascript">
				$.ajax({
					method: 'POST',
					url: 'api.php',
					data: {
						s: qs("s"),
						op: 'iplistsearchtimes',
						playername: '<?php echo $helper[0]['playername']; ?>'
					},
					timeout: 10000,
					beforeSend: function(jqXHR, settings) {
						//console.log(settings);
						spawnSpinner();
					},
					success: function(data, textStatus, jqXHR) {
						console.log(data);
						$("#tblConn tbody").html("");
						for (var i in data.data) {
							$("#tblConn tbody").append('<tr class="small"><td>' + uts2dt(data.data[i].tsconnect) + '</td><td>' + uts2dt(data.data[i].tsdisconnect) + '</td><td>' + data.data[i].onlinetime + '</td><td>' + data.data[i].ipaddress + '</td></tr>');
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						//console.log(jqXHR.responseText);
						spawnTopAlert("Se ha producido un error de comunicación.", "danger");
					},
					complete: function(jqXHR, textStatus) {
						//console.log(textStatus);
						removeSpinner();
					}
				});

				function despedirHelper() {
					spawnConfirmModal("Despedir a helper", "¿Estás seguro que quieres despedir a tu helper?",
						function() {
							$.ajax({
								method: 'POST',
								url: 'api.php?s=' + qs("s"),
								data: {
									op: 'firehelper',
									id: <?php echo $data[0]['staff_member_helper']; ?>
								},
								timeout: 10000,
								beforeSend: function(jqXHR, settings) {
									//console.log(settings);
									spawnSpinner();
								},
								success: function(data, textStatus, jqXHR) {
									console.log(data);
									spawnTopAlert(data.msg, data.color);
									if (data.status == "ok") {
										location.reload(true);
									}
								},
								error: function(jqXHR, textStatus, errorThrown) {
									//console.log(jqXHR.responseText);
									spawnTopAlert("Error de comunicación. Verifica tu conexión a Internet.", "danger");
								},
								complete: function(jqXHR, textStatus) {
									//console.log(textStatus);
									removeSpinner();
								}
							});
						});
				}
			</script>
		<?php
		}
		?>
	</div>

	<script type="text/javascript">
		setTimeout(function() {
			helpopReq(0);
		}, 3000);
	</script>
	<?php require 'footer.php'; ?>
</body>

</html>