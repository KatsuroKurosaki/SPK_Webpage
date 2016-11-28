<?php

$sql = "SELECT mc_ranks.id as idrank, rank, days, price, paygol, paypal
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
$mcrank = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

switch($_POST['mode']){
	case 'PAYPAL':
		if($mcrank[0]['paypal'] == "Y"){
			$sql = "SELECT token, token_type
			FROM paypal_tokens
			WHERE DATE_ADD(`generated`, INTERVAL expires_in SECOND) > NOW()
			ORDER BY id DESC
			LIMIT 1;";
			$stmt = $conn->prepare($sql);
			if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
			$stmt->execute();
			if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
			$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
			$stmt->close();
			if(count($data)==0){
				$curl=array();
				$curl['curl'] = curl_init();
				curl_setopt_array($curl['curl'], array(
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FAILONERROR => true,
					CURLOPT_URL => _PPURL."/oauth2/token",
					CURLOPT_HEADER => true,
					CURLOPT_HTTPHEADER => array(
						'Accept: application/json',
						'Accept-Language: en_US'
					),
					CURLOPT_USERPWD => _PPUSER.":"._PPSECRET,
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => http_build_query(
						array(
							'grant_type'=> 'client_credentials'
						)
					),
					CURLOPT_TIMEOUT => 5
				));
				$curl['result'] = curl_exec($curl['curl']);
				if(curl_errno($curl['curl'])) {
					$out['status']="ko";
					$out['error'] = curl_error($curl['curl']);
					die(json_encode($out));
				} else {
					$curl['info'] = curl_getinfo($curl['curl']);
					$curl['header'] = substr($curl['result'], 0, $curl['info']['header_size']);
					$curl['body'] = substr($curl['result'], $curl['info']['header_size']);
					$curl['body'] = json_decode($curl['body'],true);
				}
				curl_close($curl['curl']);
				unset($curl['curl']);
				$PP_token = $curl['body']['access_token'];
				$PP_tokentype = $curl['body']['token_type'];
				$curldata = json_encode($curl);
				
				$sql = "INSERT INTO paypal_tokens (token, token_type, expires_in, curl) VALUES (?,?,?,?);";
				$stmt = $conn->prepare($sql);
				if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
				$stmt->bind_param( 'ssis',
					$curl['body']['access_token'],
					$curl['body']['token_type'],
					$curl['body']['expires_in'],
					$curldata
				);
				$stmt->execute();
				if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
				$stmt->close();
				unset($curl, $curldata);
			}else {
				$PP_token = $data[0]['token'];
				$PP_tokentype = $data[0]['token_type'];
			}
			unset ($data);
			
			$sql = "INSERT INTO web_transaction (provider, id_rank_pricing, id_mc_player,price, days) VALUES ('PAYPAL',?,?,(SELECT price FROM mc_ranks_pricing WHERE id = ?), (SELECT days FROM mc_ranks_pricing WHERE id = ?));";
			$stmt = $conn->prepare($sql);
			if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
			$stmt->bind_param( 'iiii',
				$_POST['id'],
				$datos_user[0]['id_mc_player'],
				$_POST['id'],
				$_POST['id']
			);
			$stmt->execute();
			$id_txn = $stmt->insert_id;
			if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
			$stmt->close();
			
			$payment = array(
				'intent' => 'sale',
				'payer' => array(
					'payment_method' => 'paypal'
				),
				'transactions' => array (
					array(
						'amount' => array(
							'total' => $mcrank[0]['price'],
							'currency' => 'EUR'
						),
						'description' => "Rango ".$mcrank[0]['rank'].", ".$mcrank[0]['days']." días."
					)
				),
				'redirect_urls' => array (
					'return_url' => _WWWROOT."/paycomplete.php?s=".$_POST['s']."&txn=".$id_txn,
					'cancel_url' => _WWWROOT."/paycancel.php?id=".$mcrank[0]['idrank']."&s=".$_POST['s']."&txn=".$id_txn
				)
			);
			$payment = json_encode($payment,true);
			
			$curl=array();
			$curl['curl'] = curl_init();
			curl_setopt_array($curl['curl'], array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FAILONERROR => true,
				CURLOPT_URL => _PPURL."/payments/payment",
				CURLOPT_HEADER => true,
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Authorization: '.$PP_tokentype.' '.$PP_token
				),
				CURLOPT_USERPWD => _PPUSER.":"._PPSECRET,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $payment,
				CURLOPT_TIMEOUT => 5
			));
			$curl['result'] = curl_exec($curl['curl']);
			if(curl_errno($curl['curl'])) {
				$out['status']="ko";
				$out['error'] = curl_error($curl['curl']);
				die(json_encode($out));
			} else {
				$curl['info'] = curl_getinfo($curl['curl']);
				$curl['header'] = substr($curl['result'], 0, $curl['info']['header_size']);
				$curl['body'] = substr($curl['result'], $curl['info']['header_size']);
				$curl['body'] = json_decode($curl['body'],true);
			}
			curl_close($curl['curl']);
			unset($curl['curl']);
			$curldata = json_encode($curl);
			
			$sql = "UPDATE web_transaction SET pp_payment = ? WHERE id = ?;";
			$stmt = $conn->prepare($sql);
			if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
			$stmt->bind_param( 'si',
				$curldata,
				$id_txn
			);
			$stmt->execute();
			if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
			$stmt->close();
			
			$out['payment_url']="";
			foreach($curl['body']['links'] as $k=>$v){
				if($v['method'] == "REDIRECT"){
					$out['payment_url'] = '<script type="text/javascript"> window.location="'.$v['href'].'"; </script>';
				}
			}
			if($out['payment_url'] != ""){
				$out['status']="ok";
			} else {
				$out['status']="ko";
				$out['msg']="Error de PayPal al procesar el encargo. Vuelve a intentarlo en un rato o contacta con PayPal.";
			}
			
			unset($curl, $curldata);
		} else {
			$out['status'] = "no";
			$out['msg'] = "El metodo de pago está desactivado.";
		}
		
	break;
	
	case 'PAYGOL':
		if($mcrank[0]['paygol'] == "Y"){
			$sql = "INSERT INTO web_transaction (provider, id_rank_pricing, id_mc_player,price, days) VALUES ('PAYGOL',?,?,(SELECT price FROM mc_ranks_pricing WHERE id = ?), (SELECT days FROM mc_ranks_pricing WHERE id = ?));";
			$stmt = $conn->prepare($sql);
			if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
			$stmt->bind_param( 'iiii',
				$_POST['id'],
				$datos_user[0]['id_mc_player'],
				$_POST['id'],
				$_POST['id']
			);
			$stmt->execute();
			$id_txn = $stmt->insert_id;
			if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
			$stmt->close();
			
			$out['payment_url'] = '<form name="pg_frm" method="post" action="https://www.paygol.com/pay">
				<input type="hidden" name="pg_serviceid" value="'._PGSERVICE.'">
				<input type="hidden" name="pg_currency" value="EUR">
				<input type="hidden" name="pg_name" value="Rango '.$mcrank[0]['rank'].', '.$mcrank[0]['days'].' días.">
				<input type="hidden" name="pg_custom" value="'.$id_txn.'">
				<input type="hidden" name="pg_price" value="'.$mcrank[0]['price'].'">
				<input type="hidden" name="pg_return_url" value="'._WWWROOT."/paycomplete.php?s=".$_POST['s']."&txn=".$id_txn.'">
				<input type="hidden" name="pg_cancel_url" value="'._WWWROOT."/paycancel.php?id=".$mcrank[0]['idrank']."&s=".$_POST['s'].'&txn='.$id_txn.'">
				</form>
				<script type="text/javascript"> $("form[name=pg_frm]").submit(); </script>';
				$out['status'] = "ok";
		} else {
			$out['status'] = "no";
			$out['msg'] = "El metodo de pago está desactivado.";
		}
	break;
	
	default:
		$out['status'] = "no";
		$out['msg'] = "El metodo de pago no es compatible.";
}
?>