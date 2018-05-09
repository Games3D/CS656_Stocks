<?php 	

require_once 'DBconnect.php';



session_start();







if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  //get request for stock info



	$urlFirst = "https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=AFS&PARAMS=";


	$result = $conn->query("SELECT * FROM `SM_StockList`
							 JOIN `SM_Stocks` ON SM_Stocks.StockSymbol = SM_StockList.Symbol
							 JOIN `SM_Portfolio` ON SM_Portfolio.portfolioID = SM_Stocks.PortfolioID
							 WHERE SM_Portfolio.Username = '".$_SESSION["USER"]."' AND SM_Portfolio.portfolioID = '".$_SESSION['CURPORTFOLIO']."' ORDER BY Market,Symbol ASC;");


$counter=0;

	while($row = $result->fetch_assoc())

	{

		if($row["ER"] == null){

			$urlFirst .= "0,";

        }
        else
        {
            $urlFirst .= $row["ER"].','.$_SESSION['finalarray'][$counter].','.$row["Beta"].',';

    $counter=$counter+1;
		}

	}
        $urlFirst.=$_SESSION['TOTALPORT'].','.$_POST['betaopt'];


	echo $urlFirst;


		$contentsFirst = file_get_contents($urlFirst);

		echo $contentsFirst;

		//If $contents is not a boolean FALSE value.



		/*if($contentsFirst == false){



			$_SESSION["ERROR"] = 'Get request error';



			header('Location: Error.php');



			return;



		}*/



		$DATAFIRST = explode(" ", $contentsFirst);
		
//print_r($DATAFIRST);

		//header("Location: portfolio.php");

}


?>

		<html>

		<head>
			<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
			<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


			<meta charset="utf-8">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
			<link rel="stylesheet" type="text/css" href="CSS/home.css">

			<title>Optimized Portfolio</title>
		</head>

		<body>
			<table id="projectSpreadsheet">
				<thead>
					<tr>
						<th>Stock Symbol</th>
						<th>Stock Name</th>
						<th>Number Of Stocks</th>
					</tr>
				</thead>
				<tbody id="tbodyid">
					<?php
					$resultss = $conn->query("SELECT * FROM `SM_StockList`
							 JOIN `SM_Stocks` ON SM_Stocks.StockSymbol = SM_StockList.Symbol
							 JOIN `SM_Portfolio` ON SM_Portfolio.portfolioID = SM_Stocks.PortfolioID
							 WHERE SM_Portfolio.Username = '".$_SESSION["USER"]."' AND SM_Portfolio.portfolioID = '".$_SESSION['CURPORTFOLIO']."' ORDER BY Market,Symbol ASC;");
					
					$numrows = mysqli_num_rows( $resultss );

					while ( $row = $resultss->fetch_assoc()) {
						?>
					<tr>
						<td>
							<?php echo $row['Symbol']?>
						</td>
						<td>
							<?php echo $row['StockName']?>
						</td>
						<td>
							<?php //echo $row['Number of Stocks']?>
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>

			<button name="HISTORY" onClick='location.href="portfolio.php"'>Back</button>
		</body>

		</html>