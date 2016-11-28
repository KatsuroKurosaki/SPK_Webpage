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
		
		<div class="container main">
			<h1 class="text-center">Tu dirección IP:</h1>
			<h2 class="text-center"><?php echo $_SERVER['REMOTE_ADDR']; ?></h2>
		</div>
		
		<?php require 'footer.php'; ?>
    </body>
</html>
