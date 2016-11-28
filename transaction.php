<?php
require 'cnf.php';
require 'class/SPK.php';
error_reporting(_DEBUGLVL);
$conn = SPK\GlobalFunc::getMysqlConn(_HOST,_USER,_PASS,_DDBB);

$sql = "SELECT rank, days, price, paygol, paypal
FROM mc_ranks_pricing
INNER JOIN mc_ranks ON mc_ranks.id = mc_ranks_pricing.id_rank
WHERE mc_ranks_pricing.id = ?;";
$stmt = $conn->prepare($sql);
if($stmt===false){
	die( $conn->error );
}
$stmt->bind_param( 'i',
	$_POST['id']
);
$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if(count($data)==1){ ?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">×</span></button>
		<h4 class="modal-title"><?php echo "Rango ".$data[0]['rank'].", ".$data[0]['days']." días, precio ".$data[0]['price']." &euro;."; ?></h4>
	</div>
	<div class="modal-body">
		<label>Asegurate de seleccionar el rango correcto, no se harán devoluciones por error.</label>
		<div class="text-center"><a class="btn btn-info" href="https://www.google.com/finance/converter?a=<?php echo $data[0]['price']; ?>&from=EUR&to=USD" target="_blank">Convertir de Euros a otras monedas</a></div>
	</div>
	<div class="modal-footer">
		<div class="text-center">
			<?php if($data[0]['paygol'] == "Y") { ?>
				<button class="btn btn-default" onclick="javascript:getRank('PAYGOL');" data-toggle="tooltip" data-placement="top" title="Pago por SMS no disponible en todos los paises."><i class="fa fa-envelope-o" aria-hidden="true"></i> PayGol (SMS, Paysafecard)</button>
			<?php } ?>
			<?php if ($data[0]['paypal'] == "Y"){ ?>
				<button class="btn btn-default" onclick="javascript:getRank('PAYPAL');"><i class="fa fa-paypal" aria-hidden="true"></i> PayPal (VISA, MasterCard)</button>
			<?php } ?>
		</div>
	</div>
	<script type="text/javascript">
		$('.modal-footer [data-toggle="tooltip"]').tooltip();
		
		function getRank(mode){
			$.ajax({
				method: 'POST',
				url: 'api.php',
				data: {
					op:'getrank',
					mode:mode,
					s:qs("s"),
					id:<?php echo $_POST['id']; ?>
				},
				timeout: 10000,
				beforeSend: function(jqXHR, settings) {
					console.log(settings);
					spawnSpinner();
				},
				success: function (data, textStatus, jqXHR) {
					console.log(data);
					if(data.status == "ok"){
						$("body").append(data.payment_url);
					} else {
						spawnAlert(data.msg,"warning",".modal-footer");
						removeSpinner();
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR.responseText);
					spawnAlert("Error de comunicación. Verifica la correcta conexión a Internet.","danger",".modal-footer")
					removeSpinner();
				},
				complete: function(jqXHR, textStatus) {
					console.log(textStatus);
				}
			});
		}
	</script>
<?php } else { ?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">×</span></button>
		<h4 class="modal-title">Error 404: Rango no encontrado</h4>
	</div>
	<div class="modal-body">
		<p>Los detalles del rango especificado no se han encontrado.</p>
	</div>
	<div class="modal-footer">
		<button class="btn btn-warning" data-dismiss="modal">Cerrar</button>
	</div>
<?php } ?>