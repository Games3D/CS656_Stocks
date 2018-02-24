<link rel="stylesheet" type="text/css" href="CSS/home.css">

<?php 	
	require_once 'DBconnect.php';
	session_start();
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {	
		echo("SELL!!!".$_GET['SELL_NUM']);
		if (isset($_GET['BUY'])){
			echo "BUYYYYYYYYYYY";
			//do a select on stocks to make sure the is added already, if yes then move on, if no then add
			//insert into transactions
			echo "S:".$_GET['symbol'];
			echo "A:".$_GET['amount'];
			echo "P:".$_SESSION['CURPORTFOLIO'];
			
			if (!isset($_SESSION['CURPORTFOLIO']) || !isset($_GET['symbol']) || !isset($_GET['amount'])){//error checking
				$_SESSION["ERROR"] = 'Buy parameters are invalid: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			
			//get request for stock info
			
			//TODO get request
			$unitPrice=9;
			$StockName="new stock";
			$ListPrice=343;
			$MarketCap=456565;
			$OpenPrice=2323;
			$ClosePrice=23232;
			
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
				$conn->query("INSERT INTO np397.SM_Stocks (PortfolioID, StockSymbol, StockName, ListPrice, MarketCap, OpenPrice, ClosePrice) VALUES ('".$_SESSION['CURPORTFOLIO']."', '".$_GET['symbol']."', '".$StockName."', '".$ListPrice."', '".$MarketCap."', '".$OpenPrice."', '".$ClosePrice."');");
				
				$result3 = $conn->query("SELECT StockID FROM np397.SM_Stocks where StockSymbol='".$_GET['symbol']."' and PortfolioID='".$_SESSION['CURPORTFOLIO']."';");
			if (!$result3) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			$row3 = $result3->fetch_assoc();
				
				$conn->query("INSERT INTO np397.SM_Transaction (StockID, ShareQuantity, UnitPrice, Timestamp) VALUES ('".$row3["StockID"]."', '".$_GET['amount']."', '".$unitPrice."', CURRENT_TIMESTAMP);");
				echo $row3["StockID"]."|";
			}
		
			//Updates balance
			$result2 = $conn->query("UPDATE np397.SM_Portfolio set Balance='".($BALANCE-($_GET['amount']*$unitPrice))."' where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
			
			header("Location: portfolio.php");
		} elseif (isset($_GET['SELL'])){
			//insert a sell transaction
			//test to see if stock balance is 0, if yes then remove stock
			echo "SELLLLLLLLLLL". $_GET["StockID"];
			$SHARES=$_GET['SELL_NUM'];
			
			//get request for stock info
			
			//TODO get request
			$unitPrice=9;
			
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
			
			//gets the total shares which the current use ownes at the moment
			$result2 = $conn->query("SELECT sum(ShareQuantity) as aa from np397.SM_Transaction Where StockID='".$_GET['StockID']."';");
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
			$result2 = $conn->query("UPDATE np397.SM_Portfolio set Balance='".($BALANCE+($SHARES*$unitPrice))."' where Username='".$_SESSION["USER"]."' and portfolioID='".$_SESSION['CURPORTFOLIO']."';");
			
			header("Location: portfolio.php");
			
		} elseif ($_GET['OP']=='HISTORY'){
			//write page to display history of transactions for this stock
			
			$result2 = $conn->query("SELECT * FROM np397.SM_Stocks where StockID='".$_GET["StockID"]."';");
			if (!$result2) {
				die('Invalid query: ' . mysql_error());
				$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
				header('Location: Error.php');
				return;
			}
			$row2 = $result2->fetch_assoc();
			$Stock=$row2['StockName']." (".$row2['StockSymbol'].")";
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
	<th>Amount</th>
  </tr>
	</thead>
	<tbody id="tbodyid">
   <?php
 		$result=mysqli_query($conn,"SELECT * from np397.SM_Transaction Where StockID='".$_GET["StockID"]."';");
 		$numrows=mysqli_num_rows($result);
  while($row = mysqli_fetch_assoc($result)) {?>
  <tr>
    <td><?php echo $row['Timestamp']?></td>
	<td><?php echo $row['ShareQuantity']?></td>
    <td><?php echo $row['UnitPrice']?>.</td>
  </tr>
   <?php }?>
	</tbody>
</table>

<button name="HISTORY" onClick='location.href="portfolio.php"'>Back</button>
<br><br><br><br>
 <a href="#" id="xx" style="text-decoration:none;color:#000;background-color:#ddd;border:1px solid #ccc;padding:8px;">Export Table data into Excel</a>
</body>
<script>
    $(document).ready(function () {
    function exportTableToCSV($table, filename) {
    
        var $rows = $table.find('tr:has(td),tr:has(th)'),
    
            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            tmpColDelim = String.fromCharCode(11), // vertical tab character
            tmpRowDelim = String.fromCharCode(0), // null character
    
            // actual delimiter characters for CSV format
            colDelim = '","',
            rowDelim = '"\r\n"',
    
            // Grab text from table into CSV formatted string
            csv = '"' + $rows.map(function (i, row) {
                var $row = $(row), $cols = $row.find('td,th');
    
                return $cols.map(function (j, col) {
                    var $col = $(col), text = $col.text();
    
                    return text.replace(/"/g, '""'); // escape double quotes
    
                }).get().join(tmpColDelim);
    
            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + '"',
    
            
    
            // Data URI
            csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);
            
            console.log(csv);
            
            if (window.navigator.msSaveBlob) { // IE 10+
                //alert('IE' + csv);
                window.navigator.msSaveOrOpenBlob(new Blob([csv], {type: "text/plain;charset=utf-8;"}), "csvname.csv")
            } 
            else {
                $(this).attr({ 'download': filename, 'href': csvData, 'target': '_blank' }); 
            }
    }
    
    // This must be a hyperlink
    $("#xx").on('click', function (event) {
        
        exportTableToCSV.apply(this, [$('#projectSpreadsheet'), 'export.csv']);
        
        // IF CSV, don't do event.preventDefault() or return false
        // We actually need this to be a typical hyperlink
    });
});
    </script>
</html>
<?php
		}
}
?>