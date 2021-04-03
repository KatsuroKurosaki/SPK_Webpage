<?php
require 'cnf.php';
require 'class/SPK.php';
error_reporting(_DEBUGLVL);
$conn = SPK\GlobalFunc::getMysqlConn(_HOST,_USER,_PASS,_DDBB);
if(isset($_GET['s'])){
	$datos_user = SPK\GlobalFunc::checkSession($conn, $_GET['s']);
	if(!$datos_user){
		header("Location: ".pathinfo(__FILE__,PATHINFO_BASENAME));
		die();
	}
}
?><!DOCTYPE html>
<html lang="en">
    <?php require 'header.php'; ?>
    <body>
        <?php require 'navbar.php'; ?>
		
		<?php
		$sql = "SELECT map_name, world_name, last_updated, currently_processing
		FROM mc_maps
		WHERE web_display AND id = ?;";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param( 'i',
			$_GET['id']
		);
		$stmt->execute();
		$map = $stmt->get_result()->fetch_assoc();
		$stmt->close();
		
		if($map != NULL){ ?>
			<div class="page-header text-center">
				<h1><small>Mapa</small> <?php echo $map['map_name']; ?></h1>
			</div>
			
			<div class="container-fluid main">
				<div class="text-right"><a href="./maps/<?php echo $map['world_name']; ?>/#/0/0/0/-2/0/0" target="_blank"><i class="fa fa-external-link small" aria-hidden="true"></i> Abrir mapa más grande</a></div>
				<iframe style="width:100%;height:600px;" src="./maps/<?php echo $map['world_name']; ?>/#/0/0/0/-2/0/0"></iframe>
				<div class="text-center well well-sm">Última actualización: <span id="lastUpdate"></span> <?php if($map['currently_processing']){echo '<h1 class="label label-danger blink">Estamos regenerando el mapa</h1>';} ?></div>
			</div>
			
			<script type="text/javascript">
				$("#lastUpdate").text( moment.unix(<?php echo $map['last_updated']; ?>).format("DD/MM/YYYY HH:mm") );
			</script>
			
		<?php } else { ?>
			<div class="jumbotron">
				<div class="container">
					<h1><i class="fa fa-globe" aria-hidden="true"></i> Mapas</h1>
					<p>&nbsp;</p>
				</div>
			</div>
			
			<div class="container-fluid main">
				<p>Haz click en el mapa que te gustaría visitar.</p>
				<div class="row">
					<?php
					$sql = "SELECT id, map_name
					FROM mc_maps
					where web_display
					ORDER BY map_name;";
					$stmt = $conn->prepare($sql);
					$stmt->execute();
					foreach($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $k=>$v){
						echo '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 text-center">
							<a href="maps.php?id='.$v['id']; if(isset($_GET['s'])){echo '&s='.$_GET['s'];} echo '" style="margin-bottom:.5em;" class="btn btn-lg btn-default btn-block"><i class="fa fa-lg fa-map-marker" aria-hidden="true"></i> <span class="fa-lg">'.$v['map_name'].'</span></a>
						</div>';
					}
					$stmt->close();
					?>
				</div>
			</div>
		<?php } ?>
		
		<?php require 'footer.php'; ?>
    </body>
</html>
