<?php
$sql = "SELECT playername, rank, UNIX_TIMESTAMP(web_return) AS tsreturn
FROM web_transaction
INNER JOIN mc_players ON mc_players.id = web_transaction.id_mc_player
INNER JOIN mc_ranks_pricing ON mc_ranks_pricing.id = web_transaction.id_rank_pricing
INNER JOIN mc_ranks ON mc_ranks.id = mc_ranks_pricing.id_rank
WHERE `status` = 'COMPLETE'
ORDER BY web_transaction.web_return DESC
LIMIT 20;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->execute();
if($stmt->error){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$out['data'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$out['status'] = "ok";
?>