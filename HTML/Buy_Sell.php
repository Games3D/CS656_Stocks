<?php 	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "hiiiiiiiiiiiiiii";
		
		if (isset($_POST['BUY'])){
			echo "BUYYYYYYYYYYY";
			$query = "insert into np397.SM_Portfolio Where Username= '".$_SESSION["USER"]."';";
			//INSERT INTO `np397`.`SM_Stocks` (`StockID`, `PortfolioID`, `StockSymbol`, `StockName`, `ListPrice`, `MarketCap`, `OpenPrice`, `ClosePrice`) VALUES (NULL, '1', 'kjh', 'hjkhjh', '987', '897', '889', '789'), (NULL, '2', 'hjg', 'yuyuy', '8989', '87', '987', '9889');
		} elseif (isset($_POST['SELL'])){
			echo "SELLLLLLLLLLL". $_GET["StockID"];
		}
}

//header("Location: portfolio.php");
?>