<?php 	

require_once 'DBconnect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  //get request for stock info
$url = "https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=AFS&PARAMS=0.005045997,51.875423 ,1.073132221,-0.000995944,187.36 ,1.066861448,0.003802902,100.5 ,0.984216143,0.000751952,344.5 ,0.690125203,0.003323035,152.61 ,1.098406147,0.004520559,14.62 ,1.025943826,0.000442582,142.61 ,0.717279322,0.000217747,203.42 ,0.883875360,0.006611401,51.875423 ,0.993940550,0.003667151,8.793088 ,0.743267272,2639.963934,1.2";
//$url="https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=FIRSTBUY&PARAMS=ko";
$result = $conn->query("SELECT * FROM `SM_StockList`
						JOIN `SM_Stocks` ON SM_Stocks.StockSymbol = SM_StockList.Symbol
						JOIN `SM_Portfolio` ON SM_Portfolio.portfolioID = SM_Stocks.PortfolioID
						WHERE SM_Portfolio.Username = '".$_SESSION["USER"]."' AND SM_Portfolio.portfolioID = '".$_SESSION['CURPORTFOLIO']."' ORDER BY Market,Symbol ASC;");
/*
$counter=0;

while($row = $result->fetch_assoc()){
	if($row["ER"] == null){
		$urlFirst .= "0,";
    } else {
		$urlFirst .= $row["ER"].','.$_SESSION['finalarray'][$counter].','.$row["Beta"].',';
    	$counter=$counter+1;
	}
}

$urlFirst.=$_SESSION['TOTALPORT'].','.$_POST['betaopt'];*/
echo $url;

$contents = file_get_contents($url);
echo $contents;

//If $contents is not a boolean FALSE value.
if($contents == false){
	$_SESSION["ERROR"] = 'Get request error';
	header('Location: Error.php');
	return;
}

$DATA = explode("`", $contents);
print_r($DATA);
}?>

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

					$outcount=0;
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
							<?php echo $DATA[$outcount]?>
						</td>
					</tr>
					<?php 
					$outcount++;
					}?>
				</tbody>
			</table>

			<button name="HISTORY" onClick='location.href="portfolio.php"'>Back</button>
		</body>

		</html>