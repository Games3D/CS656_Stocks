<link rel="stylesheet" type="text/css" href="CSS/home.css">

<?php 	
		require_once 'DBconnect.php';
session_start();


if ($_SERVER['REQUEST_METHOD'] == 'GET') {	
	//echo("SELL!!!".$_GET['BUY']."|".isset($_GET['SELL'])."|");
//*************************************************************************************
//BUY
//*************************************************************************************
	if (isset($_GET['BUYF']) || isset($_GET['AUTO_BUY']) || isset($_GET['buyF'])){
		//echo "BUYYYYYYYYYYY";
		//do a select on stocks to make sure the is added already, if yes then move on, if no then add
		//insert into transactions
		//echo "S:".$_GET['symbol'];
		//echo "A:".$_GET['amount'];
		//echo "P:".$_SESSION['CURPORTFOLIO'];

		if (!isset($_SESSION['CURPORTFOLIO']) || !isset($_GET['symbol']) || !isset($_GET['amount']) || $_GET['amount']<=0){//error checking
			$_SESSION["ERROR"] = 'Buy parameters are invalid: ' . mysql_error();
			header('Location: Error.php');
			return;
		}
		
		//check to make sure you dont have more then 10 stocks already
		$resultSuma = mysqli_query( $conn, "select count(StockID) as aa from np397.SM_Stocks Where PortfolioID='" . $_SESSION['CURPORTFOLIO'] . "';" );
		$rowSuma = mysqli_fetch_assoc( $resultSuma );
//echo "asdfd".$rowSuma['aa']."|";
		if ($rowSuma['aa'] >= 10){
			$_SESSION["ERROR"] = 'You can`t buy more because you already have 10 stocks';
			header('Location: Error.php');
			return;
		}
		
		$sss=$_GET['symbol'];
		$result2 = $conn->query("SELECT * FROM np397.SM_StockList where Symbol='".strtoupper($_GET['symbol'])."';");
		$row22 = $result2->fetch_assoc();
		$NSE_DOW="DOW";
		if ($row22['Market']=="NSE"){
			$NSE_DOW="NSE";
			$sss=$_GET['symbol'].".ns";
		}
					

		
		//Check the 70/30 rule here
		//select count(of .ns stock) from SM_Stocks join SM_StockList where PortfolioID= cur
		//select count(of american stock) from SM_Stocks join SM_StockList where PortfolioID= cur
		//if american is more then 7 dont allow another american
		//if .ns is more then 7 dont allow another .ns
		//if ()
		$result2 = $conn->query("SELECT sum(SM_Transaction.ShareQuantity * SM_Transaction.UnitPrice) as aa FROM np397.SM_Stocks join np397.SM_Transaction on SM_Stocks.StockID = SM_Transaction.StockID join np397.SM_StockList on SM_Stocks.StockSymbol = SM_StockList.Symbol where SM_StockList.Market='DOW' and SM_Stocks.portfolioID='".$_SESSION['CURPORTFOLIO']."';");
		$row22 = $result2->fetch_assoc();
		$DOW=$row22['aa'];
		
		$result2 = $conn->query("SELECT sum(SM_Transaction.ShareQuantity * SM_Transaction.UnitPrice) as aa FROM np397.SM_Stocks join np397.SM_Transaction on SM_Stocks.StockID = SM_Transaction.StockID join np397.SM_StockList on SM_Stocks.StockSymbol = SM_StockList.Symbol where SM_StockList.Market='NSE' and SM_Stocks.portfolioID='".$_SESSION['CURPORTFOLIO']."';");		
		$row22 = $result2->fetch_assoc();
		$NSE=$row22['aa'];
		
		echo "NSE:".$NSE."|DOW:".$DOW."|".$NSE/($NSE+$DOW);//<.30;
		if ((($NSE/($NSE+$DOW))>.35 && ($rowSuma['aa'] >= 7)) && $NSE_DOW=="NSE"){
			$_SESSION["ERROR"] = 'Too much NSE stocks';
			header('Location: Error.php');
			return;
		}
		if ((($DOW/($NSE+$DOW))>.75 && ($rowSuma['aa'] >= 7)) && $NSE_DOW=="DOW"){
			$_SESSION["ERROR"] = 'Too much DOW stocks';
			header('Location: Error.php');
			return;
		}
			
		
	
		//check the 90/10 rule here, in sell, and when adding or removing money
		//sum up all current portfolio's total owned amount and compare to the portfolio balance
				
		
		
		//Check to see if the stock is valid
			$result2 = $conn->query("SELECT count(*) as total FROM np397.SM_StockList where Symbol='".strtoupper($_GET['symbol'])."';");
			if (!$result2) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			$row2 = $result2->fetch_assoc();
			if ($row2['total']<=0){
				$_SESSION["ERROR"] = 'Not a valid stock';
				header('Location: Error.php');
				return;
			}
				
		//get request for stock info
		$urlFirst = 'https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=FIRSTBUY&PARAMS='.$sss;
		$contentsFirst = file_get_contents($urlFirst);

		//If $contents is not a boolean FALSE value.
		if($contentsFirst == false){
			$_SESSION["ERROR"] = 'Get request error';
			header('Location: Error.php');
			return;
		}
		$DATAFIRST = explode("`", $contentsFirst);


		$url = 'https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=GETQUOTE&PARAMS='.$sss;
		$contents = file_get_contents($url);

		//If $contents is not a boolean FALSE value.
		if($contents == false){
			$_SESSION["ERROR"] = 'Get request error';
			header('Location: Error.php');
			return;
		}
		$DATA = explode("`", $contents);

//echo print_r(array_values($DATAFIRST));
		$FirstPrice=$DATAFIRST[13];
		$unitPrice=$DATA[7];
		$StockName=$DATA[2];
		$ListPrice=$DATA[6];
		$MarketCap=$DATA[1];
		$OpenPrice=$DATA[3];
		$ClosePrice=$DATA[5];
		$Currency=$DATA[4];
		//echo "UNIT:".$unitPrice."|";
		if ($unitPrice==0){
			$_SESSION["ERROR"] = 'Bad Ticker';
			header('Location: Error.php');
			return;
		}
			
		//gets the user's balance
		$result2 = $conn->query("SELECT Balance FROM np397.SM_Portfolio where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
			$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
			header('Location: Error.php');
			return;
		}
		$row2 = $result2->fetch_assoc();
		$BALANCE=$row2['Balance'];

		if (($_GET['amount']*$unitPrice) > $BALANCE){
			$_SESSION["ERROR"] = 'not enough funds to buy this stock';
			header('Location: Error.php');
			return;
		}

		//checks to see if the stock is ther already or not
		$result2 = $conn->query("SELECT StockID FROM np397.SM_Stocks where StockSymbol='".$_GET['symbol']."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
			$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
			header('Location: Error.php');
			return;
		}
		$row2 = $result2->fetch_assoc();
		if ($row2['StockID'] != ""){//tests to see if the stock is there already or not
			echo "|trans|";
			$conn->query("INSERT INTO np397.SM_Transaction (StockID, ShareQuantity, UnitPrice, Timestamp) VALUES ('".$row2["StockID"]."', '".$_GET['amount']."', '".$unitPrice."', CURRENT_TIMESTAMP);");
		}else{//new stock
			echo "|stock|";
			$unitPrice=$FirstPrice;//sets the price to the old price 
//echo "UNIT2:".$unitPrice."|";
			$conn->query("INSERT INTO np397.SM_Stocks (PortfolioID, StockSymbol, StockName) VALUES ('".$_SESSION['CURPORTFOLIO']."', '".$_GET['symbol']."', '".$StockName."');");

			$result3 = $conn->query("SELECT StockID FROM np397.SM_Stocks where StockSymbol='".$_GET['symbol']."' and PortfolioID='".$_SESSION['CURPORTFOLIO']."';");
			if (!$result3) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			$row3 = $result3->fetch_assoc();

			$conn->query("INSERT INTO np397.SM_Transaction (StockID, ShareQuantity, UnitPrice, Timestamp) VALUES ('".$row3["StockID"]."', '".$_GET['amount']."', '".$unitPrice."', CURRENT_TIMESTAMP);");
			echo $row3["StockID"]."|".$_GET['symbol']."}";
			
			echo ("INSERT INTO np397.SM_Stocks (PortfolioID, StockSymbol, StockName) VALUES ('".$_SESSION['CURPORTFOLIO']."', '".$_GET['symbol']."', '".$StockName."');");
		}

		//Updates balance
		$result2 = $conn->query("UPDATE np397.SM_Portfolio set Balance='".($BALANCE-($_GET['amount']*$unitPrice))."' where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");

		header("Location: portfolio.php");
//*************************************************************************************
//SELL
//*************************************************************************************
	} elseif (isset($_GET['sellF'])){
		$SHARES=$_GET['SELL_NUM'];	
		
		if (isset($_GET['USER']))
			$_SESSION["USER"]=$_GET['USER'];
		
		if ($SHARES==-1){
			$resultSuma = mysqli_query( $conn, "select sum(ShareQuantity) as aa from np397.SM_Transaction Where StockID='" . $_GET['StockID'] . "';" );
			$SHARES = mysqli_fetch_assoc( $resultSuma )['aa'];
		}

		//get request for stock info
		$result2 = $conn->query("select * from np397.SM_Stocks Where StockID='".$_GET['StockID']."';");
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
			$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
			header('Location: Error.php');
			return;
		}
		$row2 = $result2->fetch_assoc();

		$sss=$row2['StockSymbol'];
		$result2 = $conn->query("SELECT * FROM np397.SM_StockList where Symbol='".strtoupper($row2['StockSymbol'])."';");
		$row22 = $result2->fetch_assoc();
		if ($row22['Market']=="NSE"){
			$sss=$row2['StockSymbol'].".ns";
		}
		
		$url = 'https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=GETQUOTE&PARAMS='.$sss;
		$contents = file_get_contents($url);
		
	//	echo $contents;
		//If $contents is not a boolean FALSE value.
		if($contents == false){
			$_SESSION["ERROR"] = 'Get request error';
			header('Location: Error.php');
			return;
		}
		$DATA = explode("`", $contents);
echo print_r(array_values($DATA));

		$unitPrice=$DATA[7];
		echo $unitPrice;

		//gets the user's balance
		$result2 = $conn->query("SELECT BankBalance FROM np397.SM_Users where Username='".$_SESSION["USER"]."';");
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
			$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
			header('Location: Error.php');
			return;
		}
		$row2 = $result2->fetch_assoc();
		$BALANCE=$row2['BankBalance'];

		//gets the total shares which the current use ownes at the moment
		$result2 = $conn->query("select sum(ShareQuantity) as aa from np397.SM_Transaction Where StockID='".$_GET['StockID']."';");
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
			$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
			header('Location: Error.php');
			return;
		}
		$row2 = $result2->fetch_assoc();

		if ($row2['aa']<$SHARES || ($row2['aa']-$SHARES)<0 || $SHARES<=0){
			$_SESSION["ERROR"] = 'not enough shares to sell';
			header('Location: Error.php');
			return;
		}

		$conn->query("INSERT INTO np397.SM_Transaction (StockID, ShareQuantity, UnitPrice, Timestamp) VALUES ('".$_GET["StockID"]."', '-".$SHARES."', '".$unitPrice."', CURRENT_TIMESTAMP);");

		//Updates balance
		$conn->query("UPDATE np397.SM_Users set BankBalance='".($BALANCE+($SHARES*$unitPrice))."' where Username='".$_SESSION["USER"]."';");

		//tests to see if there are anymore shares left, if not then it removes the stock
		$resultSuma = mysqli_query( $conn, "select sum(ShareQuantity) as aa from np397.SM_Transaction Where StockID='" . $_GET['StockID'] . "';" );
		$SHARES = mysqli_fetch_assoc( $resultSuma )['aa'];
		
		if ($SHARES==0){
			$conn->query("DELETE from np397.SM_Stocks where StockID='".$_GET['StockID']."';");
		}
		
		header("Location: portfolio.php");
//*************************************************************************************
//BUY HTML
//*************************************************************************************
	}		elseif (isset($_GET['BUY'])){
		$sss=$_GET['symbol'];
		$result2 = $conn->query("SELECT * FROM np397.SM_StockList where Symbol='".strtoupper($_GET['symbol'])."';");
		$row22 = $result2->fetch_assoc();
		if ($row22['Market']=="NSE"){
			$sss=$_GET['symbol'].".ns";
		}
		
		$url = 'https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=GETQUOTE&PARAMS='.$sss;
		$contents = file_get_contents($url);
	
		//If $contents is not a boolean FALSE value.
		if($contents == false){
			$_SESSION["ERROR"] = 'Get request error';
			header('Location: Error.php');
			return;
		}
		$DATA = explode("`", $contents);
		$unitPrice=$DATA[7];
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
	you are about to buy
	<?PHP if ($SHARES!=-1)echo $SHARES; else echo "ALL";?> shares of
	<?PHP echo $_GET['symbol']?> stock for <?php echo $unitPrice ?>, is this correct?
	<button onClick='location.href="portfolio.php"'>Back</button>
	<form action="Buy_Sell.php">
		<input name="symbol" type="hidden" value="<?php echo $_GET['symbol']?>">
		<input name="amount" type="hidden" value="<?php echo $_GET['amount']?>">
		<input type="submit" name="buyF" value="Buy Stock" onclick="buyF()"/>
	</form>
</body>

</html>
<?php
//*************************************************************************************
//SELL HTML
//*************************************************************************************
}		elseif (isset($_GET['SELL'])){
 $resultSuma = mysqli_query( $conn, "select count(StockID) as aa from np397.SM_Stocks Where PortfolioID='" . $_SESSION['CURPORTFOLIO'] . "';" );
		$rowSuma = mysqli_fetch_assoc( $resultSuma );
//echo "asdfd".$rowSuma['aa']."|";
		if ($rowSuma['aa'] < 7){
			$_SESSION["ERROR"] = 'You can`t sell because you dont have atleast 7 stocks';
			header('Location: Error.php');
			return;
		}
		
 if($_SESSION['ALLOW']=="NO")
{
$_SESSION["ERROR"] = 'Cannot Sell Stock, You need to withdraw from your portfolio before you can sell.  Too much Cash assests compared to non cash. ';
header('Location: Error.php');
}
		//insert a sell transaction
		//test to see if stock balance is 0, if yes then remove stock

		$SHARES=$_GET['SELL_NUM'];	

		$result2 = $conn->query("SELECT * FROM np397.SM_Stocks where StockID='".$_GET["StockID"]."';");
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
			$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
			header('Location: Error.php');
			return;
		}
		$row2 = $result2->fetch_assoc();
		$STOCKNAME=$row2['StockName']." (".$row2['StockSymbol'].")";
		
		$sss=$_GET['symbol'];
		$result2 = $conn->query("SELECT * FROM np397.SM_StockList where Symbol='".strtoupper($sss)."';");
		$row22 = $result2->fetch_assoc();
		if ($row22['Market']=="NSE"){
			$sss=$_GET['symbol'].".ns";
		}
		
		$url = 'https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=GETQUOTE&PARAMS='.$row2['StockSymbol'];
		$contents = file_get_contents($url);
	
		//If $contents is not a boolean FALSE value.
		if($contents == false){
			$_SESSION["ERROR"] = 'Get request error';
			header('Location: Error.php');
			return;
		}
		$DATA = explode("`", $contents);
		$unitPrice=$DATA[7];
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
	you are about to sell
	<?PHP if ($SHARES!=-1)echo $SHARES; else echo "ALL";?> shares of
	<?PHP echo $STOCKNAME?> stock for <?php echo $unitPrice ?>, is this correct?
	<button onClick='location.href="portfolio.php"'>Back</button>
	<form action="Buy_Sell.php">
		<input name="StockID" type="hidden" value="<?php echo $_GET['StockID']?>">
		<input name="SELL_NUM" type="hidden" value="<?php echo $SHARES?>">
		<input type="submit" name="sellF" value="Sell Stock" onclick="sellF()"/>
	</form>
</body>

</html>
<?php
//*************************************************************************************
//HISTORY
//*************************************************************************************
} elseif ( $_GET[ 'OP' ] == 'HISTORY' ) {
	//write page to display history of transactions for this stock
	$result2 = $conn->query( "SELECT * FROM np397.SM_Stocks where StockID='" . $_GET[ "StockID" ] . "';" );
	if ( !$result2 ) {
		die( 'Invalid query: ' . mysql_error() );
		$_SESSION[ "ERROR" ] = 'Invalid query: ' . mysql_error();
		header( 'Location: Error.php' );
		return;
	}
	$row2 = $result2->fetch_assoc();
	$Stock = $row2[ 'StockName' ] . " (" . $row2[ 'StockSymbol' ] . ")";
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
			<h1>Transaction Page for <?php echo $Stock?></h1>
			<table id="projectSpreadsheet">
				<thead>
					<tr>
						<th>Time Stamp</th>
						<th># of Shares</th>
						<th>Purchase Price</th>
					</tr>
				</thead>
				<tbody id="tbodyid">
					<?php
					$result = mysqli_query( $conn, "select * from np397.SM_Transaction Where StockID='" . $_GET[ "StockID" ] . "';" );
					$numrows = mysqli_num_rows( $result );

					while ( $row = mysqli_fetch_assoc( $result ) ) {
						?>
					<tr>
						<td>
							<?php echo $row['Timestamp']?>
						</td>
						<td>
							<?php echo $row['ShareQuantity']?>
						</td>
						<td>
							<?php echo $row['UnitPrice']?></td>
					</tr>
					<?php }?>
				</tbody>
			</table>

			<button name="HISTORY" onClick='location.href="portfolio.php"'>Back</button>
		</body>

		</html>
	<?php
	}
}
?>