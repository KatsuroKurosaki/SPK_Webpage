<?php
$start_time = microtime(TRUE);
header('Content-Type: application/json; charset=utf-8');
$out = array();
if (isset($_POST['op'])) {
	require 'cnf.php';
	require 'class/SPK.php';
	error_reporting(_DEBUGLVL);
	$conn = SPK\GlobalFunc::getMysqlConn(_HOST, _USER, _PASS, _DDBB);

	switch ($_POST['op']) {
		case 'onlineusers':
			require './api/onlineusers.php';
			break;

		case 'lastranks':
			require './api/lastranks.php';
			break;

		case 'toponline':
			require './api/toponline.php';
			break;

		case 'onlinedatagraph':
			require './api/onlinedatagraph.php';
			break;

		case 'minecraftversions':
			require './api/minecraftversions.php';
			break;

		case 'checkconnectivity':
			require './api/checkconnectivity.php';
			break;

		case 'weblogin':
			require './api/weblogin.php';
			break;

			////////////////////////////////////////////////////////////////






		case 'getrank':
			require './api/getrank.php';
			break;

		case 'paypalpay':
			require './api/paypalpay.php';
			break;

		case 'enablerank':
			require './api/enablerank.php';
			break;

		case 'histranks':
			require './api/histranks.php';
			break;

		default:
			$out['status'] = "ko";
			$out['errormsg'] = "OP inválido.";
	}
	$conn->close();
} else {
	$out['status'] = "ko";
	$out['errormsg'] = "No se ha recibido OP.";
}
/*$out['mem']['usage'] = memory_get_usage(false);
$out['mem']['usagereal'] = memory_get_usage(true);
$out['mem']['peakusage'] = memory_get_peak_usage(false);
$out['mem']['peakusagereal'] = memory_get_peak_usage(true);*/
$out['mem'] = memory_get_usage(false);
$out['time'] = round(microtime(TRUE) - $start_time, 4);
echo json_encode($out);
