<?php
//load the database configuration file
require_once 'DBconnect.php';
	session_start();
	//resets error vars
	unset($_SESSION['ERROR']);
	unset($_SESSION['ERROR_PATH']);
	
	if ($_SESSION["authenticated"] == "" or (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800))){
		session_unset(); 
		session_destroy(); 
		header("Location: index.php"); /* Redirect browser */
		exit();
	}
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
if(isset($_POST['importSubmit'])){
	//get the user's current balance
	$result2 = $conn->query("SELECT Balance FROM np397.SM_Portfolio where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
			if (!$result2) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			$row2 = $result2->fetch_assoc();
			$BALANCE=$row2['Balance'];
	
    //validate whether uploaded file is a csv file
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    
	if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'],$csvMimes)){
		if(is_uploaded_file($_FILES['file']['tmp_name'])){
            //open uploaded csv file with read only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
			//parse data from csv file line by line
			while(($line = fgetcsv($csvFile)) !== FALSE){
//*************************************************************************************
//BUY
//*************************************************************************************				
				if ($line[0]=="BUY"){
					if (!isset($_SESSION['CURPORTFOLIO']) || !isset($line[1]) || !isset($line[2])){//error checking
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
					
		
		
		
		
		
		
		
		
		
		//Check the 70/30 rule here
		//select count(of .ns stock) from SM_Stocks join SM_StockList where PortfolioID= cur
		//select count(of american stock) from SM_Stocks join SM_StockList where PortfolioID= cur
		//if american is more then 7 dont allow another american
		//if .ns is more then 7 dont allow another .ns
		$result2 = $conn->query("SELECT sum(SM_Transaction.ShareQuantity * SM_Transaction.UnitPrice) as aa FROM np397.SM_Stocks join np397.SM_Transaction on SM_Stocks.StockID = SM_Transaction.StockID join np397.SM_StockList on SM_Stocks.StockSymbol = SM_StockList.Symbol where SM_StockList.Market='DOW' and SM_Stocks.portfolioID='".$_SESSION['CURPORTFOLIO']."';");
		$row22 = $result2->fetch_assoc();
		$DOW=$row22['aa'];
		
		$result2 = $conn->query("SELECT sum(SM_Transaction.ShareQuantity * SM_Transaction.UnitPrice) as aa FROM np397.SM_Stocks join np397.SM_Transaction on SM_Stocks.StockID = SM_Transaction.StockID join np397.SM_StockList on SM_Stocks.StockSymbol = SM_StockList.Symbol where SM_StockList.Market='NSE' and SM_Stocks.portfolioID='".$_SESSION['CURPORTFOLIO']."';");		
		$row22 = $result2->fetch_assoc();
		$NSE=$row22['aa'];
		
		echo "NSE:".$NSE."|DOW:".$DOW."|".$NSE/($NSE+$DOW);//<.30;
		if (($NSE/($NSE+$DOW))>.35){
			$_SESSION["ERROR"] = 'Too much NSE stocks';
			header('Location: Error.php');
			return;
		}
		if (($DOW/($NSE+$DOW))>.75){
			$_SESSION["ERROR"] = 'Too much DOW stocks';
			header('Location: Error.php');
			return;
		}
		
		
		
		
		
		
		
		
		//check the 90/10 rule here, in sell, and when adding or removing money
		//sum up all current portfolio's total owned amount and compare to the portfolio balance
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//Check to see if the stock is valid
			$result2 = $conn->query("SELECT count(*) as total FROM np397.SM_StockList where Symbol='".strtoupper($line[1])."';");
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
		
		$sss=$line[1];
		$result2 = $conn->query("SELECT * FROM np397.SM_StockList where Symbol='".strtoupper($line[1])."';");
		$row22 = $result2->fetch_assoc();
		if ($row22['Market']=="NSE"){
			$sss=$line[1].".ns";
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

		if (($line[2]*$unitPrice) > $BALANCE){
			$_SESSION["ERROR"] = 'not enough funds to buy this stock';
			header('Location: Error.php');
			return;
		}

		//checks to see if the stock is ther already or not
		$result2 = $conn->query("SELECT StockID FROM np397.SM_Stocks where StockSymbol='".$line[1]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
			$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
			header('Location: Error.php');
			return;
		}
		$row2 = $result2->fetch_assoc();
		if ($row2['StockID'] != ""){//tests to see if the stock is there already or not
			echo "|trans|";
			$conn->query("INSERT INTO np397.SM_Transaction (StockID, ShareQuantity, UnitPrice, Timestamp) VALUES ('".$row2["StockID"]."', '".$line[2]."', '".$unitPrice."', CURRENT_TIMESTAMP);");
		}else{//new stock
			echo "|stock|";
			$unitPrice=$FirstPrice;//sets the price to the old price 
//echo "UNIT2:".$unitPrice."|";
			$conn->query("INSERT INTO np397.SM_Stocks (PortfolioID, StockSymbol, StockName) VALUES ('".$_SESSION['CURPORTFOLIO']."', '".$line[1]."', '".$StockName."');");

			$result3 = $conn->query("SELECT StockID FROM np397.SM_Stocks where StockSymbol='".$line[1]."' and PortfolioID='".$_SESSION['CURPORTFOLIO']."';");
			if (!$result3) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			$row3 = $result3->fetch_assoc();

			$conn->query("INSERT INTO np397.SM_Transaction (StockID, ShareQuantity, UnitPrice, Timestamp) VALUES ('".$row3["StockID"]."', '".$line[2]."', '".$unitPrice."', CURRENT_TIMESTAMP);");
			echo $row3["StockID"]."|".$line[1]."}";
			
			echo ("INSERT INTO np397.SM_Stocks (PortfolioID, StockSymbol, StockName) VALUES ('".$_SESSION['CURPORTFOLIO']."', '".$line[1]."', '".$StockName."');");
		}

		//Updates balance
		$result2 = $conn->query("UPDATE np397.SM_Portfolio set Balance='".($BALANCE-($line[2]*$unitPrice))."' where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
//*************************************************************************************
//SELL
//*************************************************************************************	
				} elseif ($line[0]=="SELL"){
		$result2 = $conn->query("select * from np397.SM_Stocks Where StockSymbol='".$line[1]."' and PortfolioID='".$_SESSION['CURPORTFOLIO']."';");
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
			$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
			header('Location: Error.php');
			return;
		}
		$row2 = $result2->fetch_assoc();

		$line[1]=$row2['StockID'];
					
					
			$SHARES=$line[2];				
		if ($SHARES==-1){
			$resultSuma = mysqli_query( $conn, "select sum(ShareQuantity) as aa from np397.SM_Transaction Where StockID='" . $line[1] . "';" );
			$SHARES = mysqli_fetch_assoc( $resultSuma )['aa'];
		}

		//get request for stock info
		$result2 = $conn->query("select * from np397.SM_Stocks Where StockID='".$line[1]."';");
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
		$result2 = $conn->query("select sum(ShareQuantity) as aa from np397.SM_Transaction Where StockID='".$line[1]."';");
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
			$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
			header('Location: Error.php');
			return;
		}
		$row2 = $result2->fetch_assoc();

		if ($row2['aa']<$SHARES || ($row2['aa']-$SHARES)<0){
			$_SESSION["ERROR"] = 'not enough shares to sell';
			header('Location: Error.php');
			return;
		}

		$conn->query("INSERT INTO np397.SM_Transaction (StockID, ShareQuantity, UnitPrice, Timestamp) VALUES ('".$_GET["StockID"]."', '-".$SHARES."', '".$unitPrice."', CURRENT_TIMESTAMP);");

		//Updates balance
		$conn->query("UPDATE np397.SM_Users set BankBalance='".($BALANCE+($SHARES*$unitPrice))."' where Username='".$_SESSION["USER"]."';");

		//tests to see if there are anymore shares left, if not then it removes the stock
		$resultSuma = mysqli_query( $conn, "select sum(ShareQuantity) as aa from np397.SM_Transaction Where StockID='" . $line[1] . "';" );
		$SHARES = mysqli_fetch_assoc( $resultSuma )['aa'];
		
		if ($SHARES==0){
			$conn->query("DELETE from np397.SM_Stocks where StockID='".$line[1]."';");
		}
		
		header("Location: portfolio.php");
				} else{
					echo "ERROR";
				}
			}
            //close opened csv file
            fclose($csvFile);
		  
			header("Location: functions.php");
			$qstring = '?status=succ';
        }else{
            $qstring = '?status=err';
        }
    }else{
        $qstring = '?status=invalid_file';
    }
} 
//redirect to the listing page
header("Location: portfolio.php".$qstring);
?>
