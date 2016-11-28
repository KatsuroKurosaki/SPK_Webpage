<?php
require '../cnf.php';
require '../common.php';
require 'check_session.php';
require '../class/Permission.php';
if(!Permission::checkPermission($datos_user[0]['staff_perms'],Permission::STAFF_WEBCONSOLE)){
	die("403: Denied");
}

$sql = "SELECT modename, dirname FROM mc_modes WHERE id = ?;";
$stmt = $conn->prepare($sql);
if($stmt === false){$out['status']="ko"; $out['msg']=$conn->error; die(json_encode($out));}
$stmt->bind_param( 'i',
	$_GET['server']
);
$stmt->execute();
if($stmt->error){ die($stmt->error); }
$mc_modes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/** TRACKING **/
$sql = "INSERT INTO web_staff_tracking (id_mc_player,action_done) VALUES (?,'Ha entrado a consola web de ".$mc_modes[0]['modename']."');";
$stmt = $conn->prepare($sql);
$stmt->bind_param( 'i',$datos_user[0]['id_mc_player']);
$stmt->execute();
$stmt->close();
/** TRACKING **/
?><!DOCTYPE html>
<html lang="en">
    <?php require 'header.php'; ?>
    <body>
        <?php define("_FILE",basename(__FILE__, '.php')); require 'navbar.php'; ?>
			<div class="container main">
				<h1>Consola web: <?php echo $mc_modes[0]['modename']; ?></h1>
				<div class="row">
					<div class="col-md-12">
						<button class="btn btn-default" onclick="javascript:viewLogs();"><i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i> Descargar registros</button>
						<button class="btn btn-warning" onclick="javascript:viewLogs();"><i class="fa fa-stop fa-lg" aria-hidden="true"></i> Apagar</button>
						<button class="btn btn-success" onclick="javascript:viewLogs();"><i class="fa fa-play fa-lg" aria-hidden="true"></i> Encender</button>
						<button class="btn btn-info" onclick="javascript:viewLogs();"><i class="fa fa-repeat fa-lg" aria-hidden="true"></i> Reiniciar</button>
						<button class="btn btn-danger" onclick="javascript:viewLogs();"><i class="fa fa-refresh fa-lg" aria-hidden="true"></i> Reestablecer</button>
					</div>
				</div>
				<div>&nbsp;</div>
				<div>
					<pre class="console">Conectando, por favor, espera...</pre>
					<?php if (Permission::checkPermission($datos_user[0]['staff_perms'],Permission::STAFF_WEBCONSOLEACCEPTCMDS)) { ?>
						<input type="text" name="command" class="form-control" placeholder="Esperando comando..." style="font-family:monospace;" onkeydown="javascript:commandKeyDown(event);"/>
					<?php } ?>
				</div>
			</div>
			
			<script type="text/javascript">
				$("input[name='command']").focus();
				
				setTimeout(function(){webconsoleview(); helpopReq(0);},3000);
				
				function commandKeyDown(e){
					keyCode = ('which' in e) ? e.which : e.keyCode;
					if (keyCode==13) webconsolecmd();
				}
				
				function viewLogs(){
					spawnRemoteModal("console_viewlogs.php?s="+qs("s"),{server:qs("server")});
				}
			</script>
		<?php require 'footer.php'; ?>
    </body>
</html>
