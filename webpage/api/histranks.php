<?php
$sql = "SELECT rank, web_transaction.days, web_transaction.price, usado, UNIX_TIMESTAMP(created) AS created, provider
FROM web_transaction
INNER JOIN mc_ranks_pricing ON mc_ranks_pricing.id = web_transaction.id_rank_pricing
INNER JOIN mc_ranks ON mc_ranks.id = mc_ranks_pricing.id_rank
WHERE id_mc_player = ? AND `status` = 'COMPLETE'
ORDER BY web_transaction.id DESC;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'i',
	$datos_user[0]['id_mc_player']
);
$stmt->execute();
if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$out['data'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$out['status']="ok";
?>