<?php
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

$curl=array();
$curl['curl'] = curl_init();
curl_setopt_array($curl['curl'], array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FAILONERROR => true,
	CURLOPT_URL => _PPURL."/payments/payment/".$_POST['paymentId']."/execute",
	CURLOPT_HEADER => true,
	CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json',
		'Authorization: '.$PP_tokentype.' '.$PP_token
	),
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => json_encode(
		array(
			'payer_id'=> $_POST['PayerID']
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
$curldata = json_encode($curl);

$sql = "UPDATE web_transaction SET web_return=NOW(), status = 'COMPLETE', pp_execute = ? WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'si',
	$curldata,
	$_POST['txn']
);
$stmt->execute();
if($stmt === false){$out['status']="ko"; $out['msg']=$stmt->error; die(json_encode($out));}
$stmt->close();

$out['status']="ok";
?>