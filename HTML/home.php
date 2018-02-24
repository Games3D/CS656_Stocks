<!doctype html>
<html><head>
<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="CSS/home.css">
<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
	?>
	
<?php require_once 'DBconnect.php';
	session_start();
	
	//resets error vars
	unset($_SESSION['ERROR']);
	unset($_SESSION['ERROR_PATH']);
	echo($_SESSION["USER"]);
	
	if ($_SESSION["authenticated"] == "" or (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800))){
		session_unset(); 
		session_destroy(); 
		header("Location: index.php"); /* Redirect browser */
		exit();
	}
	
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
	?>
<title>Stock Home</title>
</head>
<body>
<h1>Stock Home Page</h1>
<hr>
<?php include 'menu.php';?>

</body>
</html>