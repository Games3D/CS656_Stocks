<?php
require_once("DBconnect.php");
if(!empty($_POST["register-user"])) {
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
	/* Email Validation */
	if(!isset($error_message)) {
		if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		$error_message = "Invalid Email Address";
		}
	}
	
	/* Validation to check if Terms and Conditions are accepted */
	if(!isset($error_message)) {
		if(!isset($_POST["terms"])) {
		$error_message = "Accept Terms and Conditions to Register";
		}
	}
	//to use a hashes on the passwords use this:  md5($_POST["password"])
	
	if(!isset($error_message)) {
		$query = "INSERT INTO SM_Users (username, password, balance, email, phone_num, birth_date, confirmed) VALUES('" . strtolower($_POST["username"]) . "', '" . strtolower($_POST["password"]) . "', '".'0' . "', '". strtolower($_POST["email"]) . "', '" . $_POST["phone_num"] . "', '" . $_POST["birth_date"] . "', '" . '0' . "')";
		$result = $conn->query($query);
		if(!empty($result)) {
			$error_message = "";
			$success_message = "You have registered successfully!";	



			require_once 'PHPMailer/PHPMailerAutoload.php';
    
			$mail = new PHPMailer();

		    $mail->Host = "smtp.gmail.com";

		    $mail->SMTPAuth = true;

		    $mail->Username = "CS673SchoolProject@gmail.com";

		    $mail->Password = "schoolproject";

		    $mail->SMTPSecure = 'ssl';

		    $mail->Port = 465;

		    //Change the email Subject
		    $mail->Subject = 'Stock Market Account Confirmation';

		    //Change the body message REPLACE THE LINK WITH CHRIS' (or whoevers we are going to use for demo) LINK
		    $mail->Body = 'Please confirm your account. Click on the link to confirm your account https://web.njit.edu/~np397/StockMarketTest/confirm.php';

		    $mail->setFrom('CS673SchoolProject@gmail.com', 'CS673Project');

		    //Replace parameters with email and username of user
		    $mail->addAddress(strtolower($_POST["email"]), strtolower($_POST["username"]));

		    if (!$mail->send()) {
		        echo "Mailer Error: " . $mail->ErrorInfo;
		    } else {
		         echo "Message sent";
		       
		    }
			header('Location: index.php');
			unset($_POST);
		} else {
			echo $query;
			$error_message = "Problem in registration. Try Again!";	
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
<td>Password</td>
<td><input type="password" class="demoInputBox" name="password" value=""></td>
</tr>

<tr>
<td>Confirm Password</td>
<td><input type="password" class="demoInputBox" name="confirm_password" value=""></td>
</tr>

<tr>
<td>Email</td>
<td><input type="text" class="demoInputBox" name="email" value="<?php if(isset($_POST['userEmail'])) echo $_POST['userEmail']; ?>"></td>
</tr>

<tr>
<td>Cell Phone Number (xxx-xxx-xxxx)</td>
<td><input type="tel" pattern="^\d{10}$" class="demoInputBox" name="phone_num" value="<?php if(isset($_POST['phone_num'])) echo $_POST['phone_num']; ?>"></td>
</tr>

<tr>
<td>Date of Birth</td>
<td><input type="date" class="demoInputBox" name="birth_date" value="<?php if(isset($_POST['birth_date'])) echo $_POST['birth_date']; ?>"></td>
</tr>

<tr>
<td colspan=2>
<input type="checkbox" name="terms"> I accept Terms and Conditions <input type="submit" name="register-user" value="Register" class="btnRegister"></td>
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