<?php
$sql = "SELECT web_transaction.id, mc_ranks.rank, web_transaction.provider, web_transaction.price, web_transaction.days, web_transaction.usado, web_transaction.status, UNIX_TIMESTAMP(web_transaction.created) as created, UNIX_TIMESTAMP(web_transaction.web_return) as web_return
FROM web_transaction
INNER JOIN mc_ranks_pricing ON mc_ranks_pricing.id = web_transaction.id_rank_pricing
INNER JOIN mc_ranks ON mc_ranks.id = mc_ranks_pricing.id_rank
WHERE web_transaction.id_mc_player = ?
ORDER BY id DESC;";
$stmt = $conn->prepare($sql);
if($stmt===false){
	die( $conn->error );
}
$stmt->bind_param( 'i',
	$_POST['id']
);
$stmt->execute();
$out['data'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$out['status'] = "ok";

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha buscado datos de transaccion de id_jugador ".$conn->escape_string($_POST['id'])."');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?>