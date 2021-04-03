<?php require '../cnf.php';
require '../common.php';
require 'check_session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php require 'header.php'; ?>

<body>
	<?php define("_FILE", basename(__FILE__, '.php'));
	require 'navbar.php'; ?>
	<div class="jumbotron">
		<div class="container">
			<h1>a</h1>
		</div>
	</div>

	<div class="container main">
		<div class="col-md-push-4 col-md-4">
			<h3>a</h3>
			<label>b</label>
		</div>
	</div>
	<?php require 'footer.php'; ?>
</body>

</html>