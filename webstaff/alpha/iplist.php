<?php
require __DIR__ . '/../class/autoload.php';

$conn = @new MySQLi("127.0.0.1", "SpkUser", "SpkPassw0rd2K19", "spkdb");
if ($conn->connect_errno) {
	die($conn->connect_error);
}

$stmt = $conn->prepare(
	"SELECT *
	FROM spkdb.mc_players
	WHERE playername = ?;"
);
if ($stmt === false) {
	die($conn->error);
}
$stmt->bind_param('s', $_POST['playername']);
$stmt->execute();
if ($stmt->errno) {
	die($stmt->error);
}
$player = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare(
	"SELECT *
	FROM spkdb.mc_players_log
	WHERE playername = ?
	ORDER BY id DESC;"
);
if ($stmt === false) {
	die($conn->error);
}
$stmt->bind_param('s', $_POST['playername']);
$stmt->execute();
if ($stmt->errno) {
	die($stmt->error);
}
$connections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function minecraftVersionDetail($intVer)
{
	switch ($intVer) {
		case 575:
			return "1.15.1";
		case 573:
			return "1.15";
		case 498:
			return "1.14.4";
		case 490:
			return "1.14.3";
		case 485:
			return "1.14.2";
		case 480:
			return "1.14.1";
		case 477:
			return "1.14";
		case 404:
			return "1.13.2";
		case 401:
			return "1.13.1";
		case 393:
			return "1.13";
		case 340:
			return "1.12.2";
		case 338:
			return "1.12.1";
		case 335:
			return "1.12";
		case 316:
			return "1.11.1-2";
		case 315:
			return "1.11";
		case 210:
			return "1.10.0-2";
		case 110:
			return "1.9.3-4";
		case 109:
			return "1.9.2";
		case 108:
			return "1.9.1";
		case 107:
			return "1.9";
		case 47:
			return "1.8.0-9";
		case 5:
			return "1.7.6-10";
		case 4:
			return "1.7.2-5";
		case 78:
			return "1.6.4";
		case 77:
			return "1.6.3";
		case 74:
			return "1.6.2";
		case 73:
			return "1.6.1";
		case 61:
			return "1.5.2";
		case 60:
			return "1.5.0-1";
		case 51:
			return "1.4.6-7";
		default:
			return "N/A";
	}
}

?>
<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script type="text/javascript">
		function uts2dt(ts) {
			var _date = new Date(ts * 1000);
			return _date.getFullYear() + "/" +
				(((_date.getMonth() + 1) < 10) ? "0" + (_date.getMonth() + 1) : (_date.getMonth() + 1)) + "/" +
				((_date.getDate() < 10) ? "0" + _date.getDate() : _date.getDate()) + " " +
				((_date.getHours() < 10) ? "0" + _date.getHours() : _date.getHours()) + ":" +
				((_date.getMinutes() < 10) ? "0" + _date.getMinutes() : _date.getMinutes()) + ":" +
				((_date.getSeconds() < 10) ? "0" + _date.getSeconds() : _date.getSeconds());
		}
	</script>
	<title>Historico conexión jugadores</title>
</head>

<body>
	<main class="container">
		<div class="row">
			<div class="col">
				<form action="" method="post">
					<div class="form-row">
						Usuario a buscar:
					</div>
					<div class="form-row pb-2">
						<input type="text" name="playername" class="form-control" value="<?= $_POST['playername'] ?>" />
					</div>
					<div class="form-row">
						<button type="submit" class="btn btn-primary">Buscar</button>
					</div>
				</form>
				<?php if (isset($_POST['playername']) && $player == null) { ?>
					<hr />
					<div class="card">
						<div class="card-body">
							<h5 class="card-title"><?= $_POST['playername'] ?></h5>
							<p class="card-text text-danger">Jugador no encontrado</p>
						</div>
					</div>
				<?php } ?>
				<?php if (isset($_POST['playername'], $player['id'])) { ?>
					<hr />
					<div class="card">
						<div class="card-body">
							<h5 class="card-title"><?= $player['playername'] ?></h5>
							<p class="card-text">UUID: <?= $player['uuid']; ?></p>
							<p class="card-text">E-mail: <?= $player['email']; ?></p>
							<p class="card-text">Fecha registro: <?= $player['registerdate']; ?></p>
							<p class="card-text">IP registro: <?= $player['registerip']; ?></p>
							<p class="card-text">Última conexión: <?= $player['lastlogin']; ?></p>
							<p class="card-text">Última IP: <?= $player['lastip']; ?></p>
							<p class="card-text">Total conexiones: <?= $player['connections']; ?> / <?= count($connections) ?></p>
						</div>
					</div>
				<?php } ?>
			</div>
			<div class="col">
				<?php if (isset($_POST['playername'], $player['id'])) { ?>
					<table class="table table-striped table-hover table-sm">
						<thead>
							<tr>
								<th>Hora conexión</th>
								<th>Hora desconexión</th>
								<th>IP</th>
								<th>Versión MC</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($connections as $k => $v) {
								echo '<tr>
								<td class="uts2dt">' . $v['tsconnect'] . '</td>
								<td class="uts2dt">' . $v['tsdisconnect'] . '</td>
								<td>' . $v['ipaddress'] . '</td>
								<td>' . minecraftVersionDetail($v['mcversion']) . '</td>
							</tr>';
							}
							?>
						</tbody>
					</table>
				<?php } ?>
			</div>
		</div>
		<script type="text/javascript">
			$(".uts2dt").each(function() {
				$(this).text(uts2dt(parseInt($(this).text())));
			})
		</script>
	</main>
</body>

</html>