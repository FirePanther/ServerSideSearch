<?php
header('content-type: text/html; charset=utf-8');
session_start();

$data = new Main();
if ($data->hasContent()) {
	echo '<!doctype html>
<html>
	<head>
		<title>'.$data->getTitle().'</title>
		<meta charset="utf-8">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
	</head>
	<body>
		<style>'.$style.'</style>
		'.$data->getBody().'
	</body>
</html>
';
}