<?php
require 'cnf.php';
require 'class/SPK.php';
error_reporting(_DEBUGLVL);
$conn = SPK\GlobalFunc::getMysqlConn(_HOST,_USER,_PASS,_DDBB);
?><!DOCTYPE html>
<html lang="en">
    <?php require 'header.php'; ?>
    <body>
        <?php require 'navbar.php'; ?>
			<div class="jumbotron">
				<div class="container">
					<h1 class="pay1"><i class="fa fa-cog fa-spin"></i> Procesando la compra...</h1>
					<h1 class="pay2" style="display:none;"><i class="fa fa-check text-success" aria-hidden="true"></i> Rango adquirido correctamente</h1>
					<h1 class="pay3" style="display:none;"><i class="fa fa-times text-danger" aria-hidden="true"></i> Oops, ha ocurrido un error</h1>
					<h1 class="pay4" style="display:none;"><i class="fa fa-times text-danger" aria-hidden="true"></i> Error 404: No encontrado</h1>
				</div>
			</div>
			
			<div class="container main">
					<div class="col-md-push-3 col-md-6 pay1">
						<h3>Un momento...</h3>
						<label>Estamos verificando el pago, por favor, espera...</label>
					</div>
					<div class="col-md-push-3 col-md-6 pay2" style="display:none;">
						<h3>Compra finalizada.</h3>
						<label>¿Quieres activar ya tu rango? Accede a tu perfil y hazlo a un click y sin mas esperas.</label>
						<div class="text-right"><a href="profile.php?s=<?php echo $_GET['s']; ?>&show=rango" class="btn btn-success">¡Vale! <i class="fa fa-chevron-right" aria-hidden="true"></i></a></div>
					</div>
					<div class="col-md-push-3 col-md-6 pay3" style="display:none;">
						<h3>Algo ha ido mal.</h3>
						<label>Se ha producido un error al procesar tu compra, vuelve a intentarlo actualizando esta página o contacta con nosotros.</label>
					</div>
					<div class="col-md-push-3 col-md-6 pay4" style="display:none;">
						<h3>No se ha encontrado la transacción.</h3>
						<label>No hemos podido encontrar tu transacción, contacta con nosotros si crees que es un error.</label>
					</div>
			</div>
			<script type="text/javascript">
			<?php
			$sql = "SELECT provider, pp_payment, status
			FROM web_transaction
			WHERE id_mc_player = ? AND id = ?;";
			$stmt = $conn->prepare($sql);
			if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
			$stmt->bind_param( 'ii',
				$datos_user[0]['id_mc_player'],
				$_GET['txn']
			);
			$stmt->execute();
			if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
			$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
			$stmt->close();
			
			if(count($data)==1){
				if($data[0]['status'] == "COMPLETE"){
					echo '$(".pay1").hide(); $(".pay2").show();';
				} elseif ($data[0]['provider'] == "PAYGOL") {
					$sql = "UPDATE web_transaction SET status = 'COMPLETE', web_return = NOW() WHERE id = ?;";
					$stmt = $conn->prepare($sql);
					if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
					$stmt->bind_param( 'i',
						$_GET['txn']
					);
					$stmt->execute();
					if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
					$stmt->close();
					echo '$(".pay1").hide(); $(".pay2").show();';
				} elseif ($data[0]['provider'] == "PAYPAL") {
					$sql = "UPDATE web_transaction SET status = 'PROCESSING' WHERE id = ?;";
					$stmt = $conn->prepare($sql);
					if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
					$stmt->bind_param( 'i',
						$_GET['txn']
					);
					$stmt->execute();
					if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
					$stmt->close();
					?>
					$.ajax({
						method: 'POST',
						url: 'api.php',
						data: {
							op: 'paypalpay',
							txn: <?php echo $_GET['txn']; ?>,
							paymentId: '<?php echo $_GET['paymentId']; ?>',
							PayerID: '<?php echo $_GET['PayerID']; ?>'
						},
						timeout: 10000,
						beforeSend: function(jqXHR, settings) {
							console.log(settings);
							spawnSpinner();
						},
						success: function (data, textStatus, jqXHR) {
							console.log(data);
							if(data.status == "ok"){
								$(".pay1").hide(); $(".pay2").show();
							} else {
								$(".pay1").hide(); $(".pay3").show();
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(jqXHR.responseText);
							$(".pay1").hide(); $(".pay3").show();
						},
						complete: function(jqXHR, textStatus) {
							console.log(textStatus);
							removeSpinner();
						}
					});
					<?php
				} else {
					echo '$(".pay1").hide(); $(".pay4").show();';
				}
			} else {
				echo '$(".pay1").hide(); $(".pay4").show();';
			}
			?>
			</script>
		<?php require 'footer.php'; ?>
    </body>
</html>
