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
	
	<h2>Cache</h2>
	<?php A::cache_begin("inicio")?>
	
	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quas a adipisci debitis non dolorum error et nobis eveniet saepe hic culpa soluta inventore unde sunt ullam nemo expedita. Deserunt odio!</p>
	<p><?php $app->main->inicio()?></p>
	
	<?php A::cache_end("inicio")?>
</body>
</html>