<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
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
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

	<title>Historico jugadores online</title>
</head>

<body>
	<main class="container">
		<?php
		require __DIR__ . '/../class/autoload.php';

		$conn = @new MySQLi("127.0.0.1", "SpkUser", "SpkPassw0rd2K19", "spkdb");
		if ($conn->connect_errno) {
			die($conn->connect_error);
		}

		$stmt = $conn->prepare(
			"SELECT id, server_name
		FROM spkdb.mc_servers;"
		);
		if ($stmt === false) {
			die($conn->error);
		}
		#$stmt->bind_param('s', $_POST['database']);
		$stmt->execute();
		if ($stmt->errno) {
			die($stmt->error);
		}
		$servers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		#$server = $stmt->get_result()->fetch_assoc();
		$stmt->close();

		foreach ($servers as $k => $v) {
			$stmt = $conn->prepare(
				"SELECT UNIX_TIMESTAMP(date_check) AS date_check, ping_data
				FROM spkdb.mc_servers_log
				WHERE id_server = ? AND date_check > DATE_SUB(NOW(),INTERVAL 2 DAY);"
			);
			if ($stmt === false) {
				die($conn->error);
			}
			$stmt->bind_param('i', $v['id']);
			$stmt->execute();
			if ($stmt->errno) {
				die($stmt->error);
			}
			$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
			$stmt->close();

			//$max = array();
			$online = array();
			$date_check = array();
			$maximum = 0;
			$average = 0;
			foreach ($data as $k2 => $v2) {
				$ping_data = json_decode($v2['ping_data'], true);
				if ($ping_data != null) {
					//array_push($max, $ping_data['players']['max']);
					if ($ping_data['players']['online'] > $maximum)
						$maximum = $ping_data['players']['online'];
					$average += $ping_data['players']['online'];
					array_push($online, $ping_data['players']['online']);
					array_push($date_check, $v2['date_check']);
				}
			}
		?>
			<div id="container<?= $v['id']; ?>"></div>
			<label>Últimas 48h - Máximo <?= $maximum ?> - Promedio <?= round($average / count($online), 2) ?></label>
			<script type="text/javascript">
				var _dates = [];
				<?php
				foreach ($date_check as $k2 => $v2) {
					echo "_dates.push(uts2dt(" . $v2 . "));";
				}
				?>
				Highcharts.chart('container<?= $v['id']; ?>', {
					chart: {
						type: 'line',
						zoomType: 'x'
					},
					title: {
						text: 'Modalidad: <?= $v['server_name']; ?>'
					},
					xAxis: {
						categories: _dates,
						labels: {
							enabled: false
						}
					},
					yAxis: {
						title: {
							text: 'Jugadores'
						}
					},
					series: [{
						name: 'Online',
						data: [<?= implode(",", $online) ?>],
						color: 'green'
					}]
				});
			</script>
			<hr />
		<?php
		}

		$conn->close();
		?>
	</main>
</body>

</html>