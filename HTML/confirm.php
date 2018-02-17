<!DOCTYPE html>
<html lang="en">
<head>
    <title>Stock Website</title>
	<link rel="stylesheet" type="text/css" href="CSS/login.css">

	<?php
		require_once("DBconnect.php");
		$conn->query("UPDATE SM_Users SET Confirmed = '1' WHERE Username = '".$_GET["user"]."'");
	?>	
</head>

<body>
  <div>
    <h1>Your account has been confirmed.</h1><br>
	<h3>Click <a href="https://web.njit.edu/~cmb45/index.php">here</a> to log in.</h3>
 </div>
  <script src='http://codepen.io/assets/libs/fullpage/jquery_and_jqueryui.js'></script>
<footer>

</footer>
</body>
</html>