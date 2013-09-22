<?php 
include 'acore.php';
$app = new acore;
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php A::script('jquery')?>
</head>
<body>
	<h1>ACOF</h1>
	
	<h2>Log</h2>
	<?php A::log($_REQUEST)?>
	<p><?php $app->main->inicio()?></p>
	
	<h2>Variables</h2>
	<p><?php A::log($app->vars)?></p>
	
	<h2>Modulo no encontrado</h2>
	<p><?php $app->main->fin()?></p>
</body>
</html>