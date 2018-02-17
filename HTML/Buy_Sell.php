<?php 	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "hiiiiiiiiiiiiiii";
		
		if (isset($_POST['BUY'])){
			echo "BUYYYYYYYYYYY";
			//do a select on stocks to make sure the is added already, if yes then move on, if no then add
			//insert into transactions
			$conn->query("INSERT INTO `np397`.`SM_Stocks` (`StockID`, `PortfolioID`, `StockSymbol`, `StockName`, `ListPrice`, `MarketCap`, `OpenPrice`, `ClosePrice`) VALUES (NULL, '1', 'HHH', 'hjkhjh', '987', '897', '889', '789');");
		} elseif (isset($_POST['SELL'])){
			//insert a sell transaction
			//test to see if stock balance is 0, if yes then remove stock
			echo "SELLLLLLLLLLL". $_GET["StockID"];
		} elseif (isset($_POST['HISTORY'])){
			//write page to display history of transactions for this stock
			echo "HISTORYYYYYY". $_GET["StockID"];
		}
}

//header("Location: portfolio.php");
?>