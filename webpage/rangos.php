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
		$sql = "SELECT rank, fa_icon, details, display_getrankbtn, howtoget, commands, benefits
		FROM mc_ranks
		WHERE visible = 'Y' AND id = ?;";
		$stmt = $conn->prepare($sql);
		if($stmt===false){
			die( $conn->error );
		}
		$stmt->bind_param( 'i',
			$_GET['id']
		);
		$stmt->execute();
		$ranks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		$stmt->close();
		
		if(count($ranks)==1){ ?>
			<div class="jumbotron">
				<div class="container">
					<h1><i class="fa <?php echo $ranks[0]['fa_icon']; ?>"></i> <?php echo $ranks[0]['rank']; ?></h1>
					<p><?php echo $ranks[0]['details']; ?></p>
				</div>
			</div>
			
			<div class="container-fluid main">
				<div class="col-md-4">
					<h3>¿Cómo se obtiene?</h3>
					<div><?php echo $ranks[0]['howtoget']; ?></div>
					<?php if(!isset($_GET['s'])){
						if($_GET['id'] != _BASERANKID) { ?>
							<label>Es obligatorio iniciar sesión en la web, con tu nombre de Minecraft y contraseña del server, para solicitar este rango.</label>
							<a class="btn btn-warning btn-block btn-lg" href="login.php">Iniciar sesión <i class="fa fa-chevron-right fa-lg"></i></a>
						<?php }
					} else { ?>
						<?php if($ranks[0]['display_getrankbtn'] == 'Y'){
							$sql = "SELECT id, days, price
							FROM mc_ranks_pricing
							WHERE id_rank = ?;";
							$stmt = $conn->prepare($sql);
							if($stmt===false){
								die( $conn->error );
							}
							$stmt->bind_param( 'i',
								$_GET['id']
							);
							$stmt->execute();
							$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
							$stmt->close();
							
							foreach($data as $k=>$v){
								echo '<button class="btn btn-info btn-block btn-lg" onclick="javascript:spawnRemoteModal(\'transaction.php\',{id:'.$v['id'].'});"><i class="fa fa-calendar" aria-hidden="true"></i> '.$v['days'].' Días - Precio '.$v['price'].' <i class="fa fa-eur" aria-hidden="true"></i><br>Comprar <i class="fa fa-shopping-cart" aria-hidden="true"></i></button>';
							}
						}
					} ?>
				</div>
				<?php if($ranks[0]['benefits'] != ""){ ?>
				<div class="col-md-4">
					<h3>¿Qué ventajas trae?</h3>
					<div><?php echo $ranks[0]['benefits']; ?></div>
				</div>
				<?php } ?>
				<?php if($ranks[0]['commands'] != ""){ ?>
				<div class="col-md-4">
					<h3>¿Qué comandos tiene?</h3>
					<div><?php echo $ranks[0]['commands']; ?></div>
				</div>
				<?php } ?>
			</div>
			<?php
		} else {
			?>
			<div class="jumbotron">
				<div class="container">
					<h1><i class="fa fa-cubes"></i> Rangos</h1>
					<p>&nbsp;</p>
				</div>
			</div>
			
			<div class="container-fluid main">
				<p>Haz click en el rango que te gustaría obtener mas información.</p>
				<div class="row">
					<?php
					$sql = "SELECT id, rank, fa_icon
					FROM mc_ranks
					WHERE visible = 'Y'
					ORDER BY orderby ASC, rank DESC;";
					$stmt = $conn->prepare($sql);
					$stmt->execute();
					foreach($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $k=>$v){
						echo '<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 text-center">
							<a href="rangos.php?id='.$v['id']; if(isset($_GET['s'])){echo '&s='.$_GET['s'];} echo '" style="margin-bottom:.5em;" class="btn btn-default btn-block"><i class="fa fa-lg '.$v['fa_icon'].'"></i> <span class="fa-lg">'.$v['rank'].'</span></a>
						</div>';
					}
					$stmt->close();
					?>
				</div>
			</div>
			<?php
		}
		?>
		<script type="text/javascript">
		if($(".main>div").length == 2){
			$(".main>div").removeClass("col-md-4").addClass("col-md-6");
		} else if ($(".main>div").length == 1){
			$(".main>div").removeClass("col-md-4").addClass("col-md-12");
		}
		</script>
		<?php require 'footer.php'; ?>
    </body>
</html>
