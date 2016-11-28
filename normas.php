<?php
header("Location: ./foro/viewtopic.php?f=26&t=3");
die();

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
		
		<div class="jumbotron">
			<div class="container">
				<h1><i class="fa fa-wpforms" aria-hidden="true"></i> Normas</h1>
				<p>&nbsp;</p>
			</div>
		</div>
		
		<div class="container-fluid main">
			<p>Lorem Ipsum...</p>
			
		</div>
		
		<?php require 'footer.php'; ?>
    </body>
</html>
