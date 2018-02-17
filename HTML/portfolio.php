<!doctype html>
<html>
<head>
 <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 
 
<meta charset="utf-8">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="CSS/home.css">

<?php
	//error handling and auth test
	error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

	require_once 'DBconnect.php';
	session_start();
	
	//resets error vars
	unset($_SESSION['ERROR']);
	unset($_SESSION['ERROR_PATH']);
	$_SESSION["USER"]="jared";
	$CURPORTFOLIO="";
	
	//if ($_SESSION["authenticated"] == "" or (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800))){
	//	session_unset(); 
	//	session_destroy(); 
	//	header("Location: index.php"); /* Redirect browser */
	//	exit();
	//}
	//$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$CURPORTFOLIO=$_POST['portfolio'];
	}
	
	echo $_SESSION["USER"]. "|".$CURPORTFOLIO;
?>
	
<title>Portfolio Page</title>
</head>

<body>
<h1>Portfolio Page</h1>
<?php include 'menu.php';?>
<h2>Users Portfolio</h2>
	


<div class='row'>
<h3>Edit Portfolio</h3>	
	<form method = "post" Action ="portfolioEdit.php">
	Portfolio Name: <input type="text">
	<input type="submit" name="CHANGE" value="Change">
	</form>
	
	<form method = "post" Action ="portfolioEdit.php?PortfolioID">
	<?php 
	$result2 = $conn->query("SELECT * FROM np397.SM_Portfolio;");
	if (!$result2) {
		die('Invalid query: ' . mysql_error());
		$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
		header('Location: Error.php');
	}
	echo '<select name="portfolio">
	      <option value="" selected="selected">Select a Portfolio to delete</option>';
	while ($row2 = $result2->fetch_assoc()){
		echo '<option value="'.$row2['portfolioID'].'">'.$row2['name'].'</option>';
	}
	echo '</select>';
	 ?>
	<input type="submit" name="DELETE" value="Delete Portfolio">
	</form>
	
	<form method = "post" Action ="portfolioEdit.php">
	Portfolio Name: <input type="text">
	<input type="submit" name="ADD" value="Add Portfolio">
	</form>
</div>
<br><br>
	
	
	
<div class='row'>
<h3>Select Protfolio</h3>	
	<form name="SelectPortfolio" method="POST">
	<?php 
	$result2 = $conn->query("SELECT * FROM np397.SM_Portfolio;");
	if (!$result2) {
		die('Invalid query: ' . mysql_error());
		$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
		header('Location: Error.php');
	}
	echo '<select onchange="this.form.submit()" name="portfolio" id="portfolio">
	      <option value="" selected="selected">Select a Portfolio to Use</option>';
	while ($row2 = $result2->fetch_assoc()){
		echo '<option value="'.$row2['portfolioID'].'"';
		if ($row2['portfolioID']==$CURPORTFOLIO){
			echo 'selected="selected"';
		} 
		echo '>'.$row2['name'].'</option>';
	}
	echo '</select>';
	 ?>
</form>
</div>	
<br><br>	
	
	
<div class='row'>
<h3>Buy Stock</h3>	
	<form method = "post" Action ="Buy_Sell.php">
	Stock Symbol: <input type="text">
	# of Stocks: <input type="number">
	<input type="submit" name="BUY" value="Buy Stock">
	</form>
</div>
<br><br>	
	
	
	
<table id="projectSpreadsheet">
 <thead>
  <tr>
    <th>Stock Name</th>
    <th>Stock Symbol</th>
	<th>Total Owned Shares</th>
	<th>Total Owned Amount</th>
    <th>List Price</th>
    <th>Market Cap</th>
    <th>Open Price</th>
    <th>Close Price</th>
	<th>Sell?</th>
	<th>View History</th>
  </tr>
	</thead>
	<tbody id="tbodyid">
   <?php
	if ($CURPORTFOLIO != ""){
 		$result=mysqli_query($conn,"select * from np397.SM_Stocks left join np397.SM_Transaction on SM_Stocks.StockID=SM_Transaction.StockID Where portfolioID= '".$CURPORTFOLIO."';");
 		$numrows=mysqli_num_rows($result);

  while($row = mysqli_fetch_assoc($result)) {?>
  <tr>
    <td><?php echo $row['StockName']?></td>
	<td><?php echo $row['StockSymbol']?></td>
	<td><?php echo $row['StockSymbol']?></td>
    <td><?php echo $row['StockSymbol']?></td>
    <td><?php echo $row['ListPrice']?>.</td>
    <td><?php echo $row['MarketCap']?></td>
    <td><?php echo $row['OpenPrice']?>.</td>
    <td><?php echo $row['ClosePrice']?>.</td>
	<td>
		<form id="buy" method = "post" Action ="Buy_Sell.php?StockID=<?php echo $row['StockID']?>">
			<input type="submit" name="SELL" value="Sell Stock">
		</form>
	</td>
	<td>
		<form id="buy" method = "post" Action ="Buy_Sell.php?StockID=<?php echo $row['StockID']?>">
			<input type="submit" name="HISTORY" value="See History">
		</form>
	</td>
  </tr>
   <?php }}?>
	</tbody>
</table>
<br> 
  
  <?php
//load the database configuration file
if(!empty($_GET['status'])){
    switch($_GET['status']){
        case 'succ':
            $statusMsgClass = 'alert-success';
            $statusMsg = 'Members data has been inserted successfully.';
            break;
        case 'err':
            $statusMsgClass = 'alert-danger';
            $statusMsg = 'Some problem occurred, please try again.';
            break;
        case 'invalid_file':
            $statusMsgClass = 'alert-danger';
            $statusMsg = 'Please upload a valid CSV file.';
            break;
        default:
            $statusMsgClass = '';
            $statusMsg = '';
    }
}
?>
   <div class="container">
    <?php if(!empty($statusMsg)){
        echo '<div class="alert '.$statusMsgClass.'">'.$statusMsg.'</div>';
    } ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Members list
            <a href="javascript:void(0);" onclick="$('#importFrm').slideToggle();">Import Members</a>
        </div>
        <div class="panel-body">
   <form action="functions.php" method="post" enctype="multipart/form-data" id="importFrm">
                <input type="file" name="file" />
                <input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
            </form>
             </div>
    </div>
</div>
<br>
<br>
<br>
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