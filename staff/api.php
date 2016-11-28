<?php
$start_time = microtime(TRUE);
header('Content-Type: application/json; charset=utf-8');
$out=array();
if(isset($_POST['op'])){
	require '../cnf.php';
	require '../common.php';
	require './check_session.php';
	switch($_POST['op']){
		case 'login':
			require './api/login.php';
		break;
		
		case 'consolecmd':
			require './api/consolecmd.php';
		break;
		
		case 'consoleview':
			require './api/consoleview.php';
		break;
		
		case 'helpop':
			require './api/helpop.php';
		break;
		
		case 'helpoplist':
			require './api/helpoplist.php';
		break;
		
		case 'iplistsearchplayer':
			require './api/iplistsearchplayer.php';
		break;
		
		case 'iplistsearchtimes':
			require './api/iplistsearchtimes.php';
		break;
		
		case 'iplistsearchmultacc':
			require './api/iplistsearchmultacc.php';
		break;
		
		case 'iplistsearchmultip':
			require './api/iplistsearchmultip.php';
		break;
		
		case 'removeplrank':
			require './api/removeplrank.php';
		break;
		
		case 'addplrank':
			require './api/addplrank.php';
		break;
		
		case 'editrankdetail':
			require './api/editrankdetail.php';
		break;
		
		case 'resyncranks':
			require './api/resyncranks.php';
		break;
		
		case 'delxtraperm':
			require './api/delxtraperm.php';
		break;
		
		case 'addxtraperm':
			require './api/addxtraperm.php';
		break;
		
		case 'addewplrank':
			require './api/addewplrank.php';
		break;
		
		case 'transactionlist':
			require './api/transactionlist.php';
		break;
		
		case 'transactionfinish':
			require './api/transactionfinish.php';
		break;
		
		case 'hirehelper':
			require './api/hirehelper.php';
		break;
		
		case 'firehelper':
			require './api/firehelper.php';
		break;
		
		case 'pexsearch':
			require './api/pexsearch.php';
		break;
		
		default:
			$out['msg']="Received operation is invalid.";
			$out['status']="no";
	}
} else {
	$out['status']="ko";
	$out['msg']="NO operation received.";
}
$out['mem']['usage'] = memory_get_usage(false);
$out['mem']['usagereal'] = memory_get_usage(true);
$out['mem']['peakusage'] = memory_get_peak_usage(false);
$out['mem']['peakusagereal'] = memory_get_peak_usage(true);
$out['time'] = round(microtime(TRUE)-$start_time,4);
echo json_encode($out);
?>
