<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';
require '../class/Permission.php';
if (!Permission::checkPermission($datos_user[0]['staff_perms'], Permission::STAFF_HELPOP)) {
	die("403: Denied");
}

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha entrado a HelpOP');";
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
		<h1>HelpOP</h1>
		<label>Listado de los 25 HelpOP mas recientes.</label>
		<button type="button" class="btn btn-default btn-sm" onclick="javascript:buscarHelpop();"><i class="fa fa-refresh" aria-hidden="true"></i> Actualizar</button>

		<table id="helpopTbl" class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th>Jugador</th>
					<th>Modalidad</th>
					<th>Cuándo</th>
					<th>Mensaje</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>

	<script type="text/javascript">
		/*$(".morphdate").each(function(){
					$(this).text(uts2dt(parseInt($(this).text())));
				});*/
		buscarHelpop();

		setTimeout(function() {
			helpopReq(0);
		}, 3000);

		function buscarHelpop() {
			$.ajax({
				method: 'POST',
				url: 'api.php?s=' + qs("s"),
				data: {
					op: 'helpoplist'
				},
				timeout: 10000,
				beforeSend: function(jqXHR, settings) {
					//console.log(settings);
					spawnSpinner();
				},
				success: function(data, textStatus, jqXHR) {
					console.log(data);
					$("#helpopTbl tbody").html("");
					for (var i in data.data) {
						$("#helpopTbl tbody").append('<tr class="small"><td>' + data.data[i].playername + '</td><td>' + data.data[i].mc_mode + '</td><td>' + uts2dt(data.data[i].fecha) + '</span></td><td>' + data.data[i].message + '</td></tr>');
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					//console.log(jqXHR.responseText);
					spawnTopAlert("Se ha producido un error de comunicación. Revisa tu conexión a Internet.", "danger");
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