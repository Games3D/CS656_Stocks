<?php 	

require_once 'DBconnect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  //get request for stock info
$url = "https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=AFS&PARAMS=";
//$url="https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=FIRSTBUY&PARAMS=ko";
$result = $conn->query("SELECT * FROM `SM_StockList`
						JOIN `SM_Stocks` ON SM_Stocks.StockSymbol = SM_StockList.Symbol
						JOIN `SM_Portfolio` ON SM_Portfolio.portfolioID = SM_Stocks.PortfolioID
						WHERE SM_Portfolio.Username = '".$_SESSION["USER"]."' AND SM_Portfolio.portfolioID = '".$_SESSION['CURPORTFOLIO']."' ORDER BY Market,Symbol ASC;");

$counter=0;

while($row = $result->fetch_assoc()){
	if($row["ER"] == null){
		$url .= "0,";
    } else {
		$url .= round($row["ER"],2).','.round($_SESSION['finalarray'][$counter],2).','.round($row["Beta"],2).',';
    	$counter=$counter+1;
	}
}

$urlFirst.=$_SESSION['TOTALPORT'].','.$_POST['betaopt'];
echo $url;

$contents = file_get_contents($url);
echo $contents;
	
	
	
	
	
	
	
// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
echo "CURL:".$resp;
// Close request to clear up some resources
curl_close($curl);
	
	
	
	
	
	
	
	

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