<?php 	
	require_once 'DBconnect.php';
	session_start();
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "hiiiiiiiiiiiiiii";
		
		if (isset($_POST['CHANGE'])){
			echo "CHANGE";
			
			if($_SESSION['CURPORTFOLIO'] == ""){
				$_SESSION["ERROR"] = 'Please select a portfolio.';
				header('Location: Error.php');
				return;
			}
			

			if ($_POST["name"] == ""){
				$_SESSION["ERROR"] = 'Enter a new portfolio name.';
				header('Location: Error.php');
				return;
			}
			
			if($_SESSION['CURPORTFOLIO'] != "" && $_POST["name"] != ""){
				$conn->query("UPDATE np397.SM_Portfolio set name='".$_POST["name"]."' Where portfolioID= '".$_SESSION['CURPORTFOLIO']."';");
			}
		} elseif (isset($_POST['ADD'])){
			echo "ADD";
			
			if ($_POST["name"] == ""){
				$_SESSION["ERROR"] = 'Enter portfolio name.';
				header('Location: Error.php');
				return;
			}else{
				$conn->query("INSERT into np397.SM_Portfolio (Username, name) values('".$_SESSION["USER"]."', '".$_POST["name"]."');");
			}

		} elseif (isset($_POST['DELETE'])){
			echo "DELETE";
			
			if ($_POST["portfolio"] == ""){
				$_SESSION["ERROR"] = 'Please select a portfolio.';
				header('Location: Error.php');
				return;
			}else{
				$conn->query("DELETE from np397.SM_Portfolio where portfolioID='".$_POST["portfolio"]."';");
			}

		} elseif (isset($_POST['BALANCE_ADD'])){
			echo "BALANCE_ADD";

			if($_SESSION['CURPORTFOLIO'] == ""){
				$_SESSION["ERROR"] = 'Please select a portfolio.';
				header('Location: Error.php');
				return;
			}else{
				$result2 = $conn->query("SELECT Balance FROM np397.SM_Portfolio where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
				if (!$result2) {
					die('Invalid query: ' . mysql_error());
					$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
					header('Location: Error.php');
					return;
				}
				$row2 = $result2->fetch_assoc();
				$BALANCE=$row2['Balance'];
				
				if ($_POST["funds"] < 0){
					$_SESSION["ERROR"] = 'Funds less then $0';
					header('Location: Error.php');
					return;
				}
				
				$conn->query("UPDATE np397.SM_Portfolio set Balance='".($BALANCE+$_POST["funds"])."' where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");	

			}

		} elseif (isset($_POST['BALANCE_SUB'])){
			echo "BALANCE_SUB";
			
			if($_SESSION['CURPORTFOLIO'] == ""){
				$_SESSION["ERROR"] = 'Please select a portfolio.';
				header('Location: Error.php');
				return;
			}else{


				$result2 = $conn->query("SELECT Balance FROM np397.SM_Portfolio where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
				if (!$result2) {
					die('Invalid query: ' . mysql_error());
					$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
					header('Location: Error.php');
					return;
				}
				$row2 = $result2->fetch_assoc();
				$BALANCE=$row2['Balance'];
				
				if ($_POST["funds"] > $BALANCE){
					$_SESSION["ERROR"] = 'Funds less then requested remove amount';
					header('Location: Error.php');
					return;
				}
				
				$conn->query("UPDATE np397.SM_Portfolio set Balance='".($BALANCE-$_POST["funds"])."' where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
			}
		}
}
header("Location: portfolio.php");
?>