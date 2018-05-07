<?php 	
require_once 'DBconnect.php';

session_start();



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  //get request for stock info

	$urlFirst = "https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=AFS&PARAMS=";

	$result = $conn->query("SELECT * FROM `SM_StockList`
							 JOIN `SM_Stocks` ON SM_Stocks.StockSymbol = SM_StockList.Symbol
							 JOIN `SM_Portfolio` ON SM_Portfolio.portfolioID = SM_Stocks.PortfolioID
							 WHERE SM_Portfolio.Username = '".$_SESSION["USER"]."';");

	while($row = $result->fetch_assoc())
	{
		if($row["ER"] == null){
			$urlFirst .= "0,";
		}else{
			$urlFirst .= $row["ER"].',';
		}
	}

	echo $urlFirst;



		

		/*$contentsFirst = file_get_contents($urlFirst);

		//If $contents is not a boolean FALSE value.

		if($contentsFirst == false){

			$_SESSION["ERROR"] = 'Get request error';

			header('Location: Error.php');

			return;

		}*/

		$DATAFIRST = explode("`", $contentsFirst);

		

		//header("Location: portfolio.php");

}

?>