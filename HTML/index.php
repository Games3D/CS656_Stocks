<!DOCTYPE html>
<html lang="en">
<head>
    <title>Stock Website</title>
	<link rel="stylesheet" type="text/css" href="CSS/login.css">
	
	<?php require_once 'DBconnect.php';
session_start();//removes old session
session_unset(); 
session_destroy();
session_start();//starts new sessionkkkkk
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!empty($_POST["username"]) && !empty($_POST["password"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
		$result = $conn->query("SELECT * FROM SM_Users");//https://www.w3schools.com/php/php_mysql_select.asp

		if (!$result) {
			die('Invalid query: ' . mysql_error());
			$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
			header('Location: Error.php');
		}
		
		while ($row = $result->fetch_assoc()) {

			if ($row['Username'] == $username && $row['Password'] == $password){
				$_SESSION["authenticated"] = 'true';
				$_SESSION["USER"] = $row['username'];
				//$conn->query("UPDATE joshua.users SET lastLogin='".date("m/d/Y: h:i:sa")."' where UserName='".$_SESSION["USER"]."';");
				header('Location: home.php');
				$loggedin="true";
			}
		}
		if (!isset($loggedin)){
			$_SESSION["ERROR"] = 'No user was found or invalid password.';
			header('Location: Error.php');
		}
	}else{
		$_SESSION["ERROR"] = 'Username or password field were empty. Please try again.';
		header('Location: Error.php');
	}
	$conn->close();
} 
?>
</head>

<body>


    <link rel="stylesheet" href="CSS/login.css" media="screen" type="text/css" />

</head>

<body>

  <div class="login-card">
    <h1>Log-in</h1><br>
  <form id="login" method = "post">
    <input type="text" name="username" id="username" placeholder="Username">
    <input type="password" name="password" id="password" placeholder="Password">
    
	  <button type="submit">Log In</button>
	 
  </form>

  <div class="login-help">
    <a href="RegisterAccount.php">Register</a> • <a href="#">Forgot Password</a>
  </div>
  <h1 align="center"><font size="3"> Created by Chris, Saladin, Nidhi, Jared, Neeha</font></h1>
</div>


  <script src='http://codepen.io/assets/libs/fullpage/jquery_and_jqueryui.js'></script>
<footer>

</footer>
</body>
</html>