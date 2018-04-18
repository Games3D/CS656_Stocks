<?php 	
	require_once 'DBconnect.php';
	session_start();
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		if (isset($_POST['CHANGE'])){
			//echo "CHANGE";
			
			if (!isset($_SESSION['CURPORTFOLIO'])){
				$_SESSION["ERROR"] = 'Invaild Parameters';
				header('Location: Error.php');
				return;
			}
			
			if (!isset($_POST["name"])){
				$_SESSION["ERROR"] = 'Invaild Parameters';
				header('Location: Error.php');
				return;
			}
			
			$conn->query("UPDATE np397.SM_Portfolio set name='".$_POST["name"]."' Where portfolioID= '".$_SESSION['CURPORTFOLIO']."';");
		} elseif (isset($_POST['ADD'])){
			//echo "ADD";
			
			if (!isset($_POST["name"])){
				$_SESSION["ERROR"] = 'Invaild Parameters';
				header('Location: Error.php');
				return;
			}
			
			$conn->query("INSERT into np397.SM_Portfolio (Username, name) values('".$_SESSION["USER"]."', '".$_POST["name"]."');");
		} elseif (isset($_POST['DELETE'])){
			//echo "DELETE";
					?>
<html>

<head>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


	<meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="CSS/home.css">

	<title>Portfolio Page</title>
</head>

<body>
	you are about to delete	<?PHP echo $SHARES;?> portfolio, is this correct?
	<button onClick='location.href="portfolio.php"'>Back</button>
	<form method = "post" action="portfolioEdit.php">
		<input name="portfolio" type="hidden" value="<?php echo $_POST['portfolio']?>">
		<input type="submit" name="deleteF" value="Delete Portfolio" onclick="deleteF()"/>
	</form>
</body>

</html>
<?php
	return;
			
		}  elseif (isset($_POST['deleteF'])){
			
			if (!isset($_POST["portfolio"])){
				$_SESSION["ERROR"] = 'Invaild Parameters';
				header('Location: Error.php');
				return;
			}
			//get a list of all stocks in this portfolio
			$result = $conn->query("select * from np397.SM_Stocks Where PortfolioID='" . $_POST["portfolio"] . "';" );
			if (!$result) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			while($row2 = $result->fetch_assoc()){
				echo $row2['StockID']."|";
				//sell all stocks one buy one
				$url = 'https://web.njit.edu/~jp834/Buy_Sell.php?StockID='.$row2['StockID'].'&SELL_NUM=-1&sellF=Sell&USER='.$_SESSION["USER"];
				file_get_contents($url);
			}
			
			//move current portfolio money to bank
			$result2 = $conn->query("SELECT BankBalance FROM np397.SM_Users where Username='".$_SESSION["USER"]."';");
			if (!$result2) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			$row2 = $result2->fetch_assoc();
			$BANKBALANCE=$row2['BankBalance'];
			
			$result2 = $conn->query("SELECT Balance FROM np397.SM_Portfolio where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
			if (!$result2) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			$row2 = $result2->fetch_assoc();
			$BALANCE=$row2['Balance'];
			
			$conn->query("UPDATE np397.SM_Portfolio set Balance='0' where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");	
			$conn->query("UPDATE np397.SM_Users set BankBalance='".($BANKBALANCE+$BALANCE)."' where Username='".$_SESSION["USER"]."';");	
			$conn->query("DELETE from np397.SM_Portfolio where portfolioID='".$_POST["portfolio"]."';");
		} elseif (isset($_POST['BALANCE_ADD'])){
			//echo "BALANCE_ADD";
			
			if ($_SESSION['CURPORTFOLIO']==""){
				$_SESSION["ERROR"] = 'No Portfolio selected';
				header('Location: Error.php');
				return;
			}
			
			$result2 = $conn->query("SELECT BankBalance FROM np397.SM_Users where Username='".$_SESSION["USER"]."';");
			if (!$result2) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			$row2 = $result2->fetch_assoc();
			$BANKBALANCE=$row2['BankBalance'];
			
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
			if ($_POST["funds"] > $BANKBALANCE){
				$_SESSION["ERROR"] = 'Not enough funds in the bank to add';
				header('Location: Error.php');
				return;
			}
			
			$conn->query("UPDATE np397.SM_Portfolio set Balance='".($BALANCE+$_POST["funds"])."' where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");	
			$conn->query("UPDATE np397.SM_Users set BankBalance='".($BANKBALANCE-$_POST["funds"])."' where Username='".$_SESSION["USER"]."';");	
		} elseif (isset($_POST['BALANCE_SUB'])){
			//echo "BALANCE_SUB";
			
			if ($_SESSION['CURPORTFOLIO']==""){
				$_SESSION["ERROR"] = 'No Portfolio selected';
				header('Location: Error.php');
				return;
			}
			
			$result2 = $conn->query("SELECT BankBalance FROM np397.SM_Users where Username='".$_SESSION["USER"]."';");
			if (!$result2) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			$row2 = $result2->fetch_assoc();
			$BANKBALANCE=$row2['BankBalance'];
			
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
			$conn->query("UPDATE np397.SM_Users set BankBalance='".($BANKBALANCE+$_POST["funds"])."' where Username='".$_SESSION["USER"]."';");	
		}
}
header("Location: portfolio.php");
?>