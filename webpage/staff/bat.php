<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';
require '../class/Permission.php';
if(!Permission::checkPermission($datos_user[0]['staff_perms'],Permission::STAFF_BANLIST)){
	die("403: Denied");
}

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha entrado a Lista de baneos');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?><!DOCTYPE html>
<html lang="en" style="height:90%;">
    <?php require 'header.php'; ?>
    <body style="height:90%;">
        <?php define("_FILE",basename(__FILE__, '.php')); require 'navbar.php'; ?>
			<iframe src="./bat/" style="width:100%;border:0px;margin:0px;padding:0px;height:100%;"></iframe>
			<script type="text/javascript">
				setTimeout(function(){helpopReq(0);},3000);
			</script>
		<?php require 'footer.php'; ?>
    </body>
</html>
