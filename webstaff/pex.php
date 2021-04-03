<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';
require '../class/Permission.php';
if (!Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_PERMISSIONSEX)) {
	die("403: Denied");
}

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha entrado a PermissionEx');";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>
<!DOCTYPE html>
<html lang="en">
<?php require 'header.php'; ?>

<body>
	<?php define("_FILE", basename(__FILE__, '.php'));
	require 'navbar.php'; ?>
	<div class="container main">
		<h1>PermissionsEx</h1>
		<label>Ver los permisos de una modalidad:</label>
		<div>
			<select name="mc_mode" class="form-control" style="width:300px;">
				<?php
				$sql = "SELECT dirname, modename
					FROM mc_modes
					WHERE spkmgr
					ORDER BY modename;";
				$stmt = $conn->prepare($sql);
				if ($stmt === false) {
					die($conn->error);
				}
				$stmt->execute();
				if ($stmt === false) {
					die($stmt->error);
				}
				foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $k => $v) {
					echo '<option value="' . $v['dirname'] . '">' . $v['modename'] . '</option>';
				}
				$stmt->close();
				?>
			</select>
			<button type="button" class="btn btn-default btn-sm" onclick="javascript:buscarPermisos();"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
		</div>
		<table id="tblPex" class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th>Rango</th>
					<th>Permiso</th>
					<th>Mundo</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<script type="text/javascript">
		$("select[name='mc_mode']").select2({
			minimumResultsForSearch: -1
		});

		function buscarPermisos() {
			$.ajax({
				method: 'POST',
				url: 'api.php',
				data: {
					s: qs("s"),
					op: 'pexsearch',
					modename: $("select[name='mc_mode']").val()
				},
				timeout: 10000,
				beforeSend: function(jqXHR, settings) {
					//console.log(settings);
					spawnSpinner();
				},
				success: function(data, textStatus, jqXHR) {
					console.log(data);
					$("#tblPex tbody").html("");
					if (data.status == "ok") {
						for (var i in data.data) {
							$("#tblPex tbody").append('<tr class="small"><td>' + data.data[i].name + '</td><td>' + data.data[i].permission + '</td><td>' + data.data[i].world + '</td><td>' + data.data[i].value + '</td></tr>');
						}
					} else {
						spawnTopAlert("Esta modalidad no dispone de rangos ni permisos.", "warning");
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
		}
	</script>
	<?php require 'footer.php'; ?>
</body>

</html>