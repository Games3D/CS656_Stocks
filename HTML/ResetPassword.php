<?php
require_once("DBconnect.php");
if(!empty($_POST["reset-password"])) {
	/* Form Required Field Validation */
	foreach($_POST as $key=>$value) {
		if(empty($_POST[$key])) {
		$error_message = "All Fields are required";
		break;
		}
	}
	/* Password Matching Validation */
	if($_POST['password'] != $_POST['confirm_password']){ 
		$error_message = 'Passwords should be same<br>'; 
	}
	
	//to use a hashes on the passwords use this:  md5($_POST["password"])
	

	if(!isset($error_message)) {
		if(isset($_POST["username"])){
			$result = $conn->query("SELECT * FROM SM_Users WHERE Username= '".strtolower($_POST["username"])."'");
		
			$rowcount=mysqli_num_rows($result);
			if ($rowcount <= 0) {
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				$error_message = "Username does not exists. Try Again!";
			}else{

				$query = "UPDATE `SM_Users` SET `Password`= '".strtolower($_POST["password"])."' WHERE `Username` = '".strtolower($_POST["username"])."'";

				$result = $conn->query($query);
				if(!empty($result)) {
					$error_message = "";
					$success_message = "Password Updated Successfully!";	
					unset($_POST);
				} 
			}
		}
	}
}
?>
<html>
<head>
<title>User Registration</title>
<link rel="stylesheet" type="text/css" href="CSS/RegisterUser.css">
</head>
<body>
<header>
	<h1 class="mainheader" align="center"><font color="white">Sign Up with Us!</font></h1>
</header>

<article>
<section>

<form name="RegisterAccount" method="post" action="">
<table border="0" width="500" align="center" class="demo-table">
<?php if(!empty($success_message)) { ?>	
<div class="success-message"><?php if(isset($success_message)) echo $success_message; ?></div>
<?php } ?>
<?php if(!empty($error_message)) { ?>	
<div class="error-message"><?php if(isset($error_message)) echo $error_message; ?></div>
<?php } ?>
<tr>
<td>User Name</td>
<td><input type="text" class="demoInputBox" name="username" value="<?php if(isset($_POST['userName'])) echo $_POST['userName']; ?>"></td>
</tr>

<tr>
<td>New Password</td>
<td><input type="password" class="demoInputBox" name="password" value=""></td>
</tr>

<tr>
<td>Confirm Password</td>
<td><input type="password" class="demoInputBox" name="confirm_password" value=""></td>
</tr>


<tr>
<td></td>
<td>
<input type="submit" name="reset-password" value="Submit" class="btnResetPassword"></td>
</tr>
</table>
</form>
</section>
</article>
<footer>
	<h2 align="center"><font color="white">Created by Jared, Chris, Saladin, Neeha, Nidhi Studios 2018</font></h2>

</footer>
</body>
</html>