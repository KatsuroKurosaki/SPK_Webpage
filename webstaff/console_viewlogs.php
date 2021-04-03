<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';

$sql = "SELECT dirname FROM mc_modes WHERE id = ?;";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
	die($conn->error);
}
$stmt->bind_param(
	'i',
	$_POST['server']
);
$stmt->execute();
$modes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha abierto el listado de registros');";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title">Descarga de registros</h4>
</div>
<div class="modal-body">
	<table class="table table-condensed">
		<thead>
			<tr>
				<th>Archivo</th>
				<th>Tamaño</th>
				<th>Descarga</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$dir = _MC_ROOT . "/" . $modes[0]['dirname'] . "/logs/";
			$scanned_directory = array_diff(scandir($dir, SCANDIR_SORT_DESCENDING), array('..', '.'));

			foreach ($scanned_directory as $k => $v) {
				echo '<tr>
					<td>' . $v . '</td>
					<td>' . human_filesize(filesize($dir . $v)) . '</td>
					<td><a href="console_dwlog.php?s=' . $_GET['s'] . '&server=' . $_POST['server'] . '&log=' . $v . '" target="_blank" class="btn"><i class="fa fa-download" aria-hidden="true"></i></a></td>
				</tr>';
			}
			?>
		</tbody>
	</table>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times fa-lg" aria-hidden="true"></i> Cerrar</button>
</div>