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
	error_reporting( 0 );
	ini_set( 'display_errors', TRUE );
	ini_set( 'display_startup_errors', TRUE );
	require_once 'DBconnect.php';
	session_start();
	//resets error vars
	unset( $_SESSION[ 'ERROR' ] );
	unset( $_SESSION[ 'ERROR_PATH' ] );
	if ( $_SESSION[ "authenticated" ] == ""
		or( isset( $_SESSION[ 'LAST_ACTIVITY' ] ) && ( time() - $_SESSION[ 'LAST_ACTIVITY' ] > 1800 ) ) ) {
		session_unset();
		session_destroy();
		header( "Location: index.php" );
		exit();
	}
	$_SESSION[ 'LAST_ACTIVITY' ] = time(); // update last activity time stamp
	if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
		$_SESSION[ 'CURPORTFOLIO' ] = $_POST[ 'portfolio' ];
	}
	
	$result2 = $conn->query( "SELECT Balance FROM np397.SM_Portfolio where Username='" . $_SESSION[ "USER" ] . "' and portfolioID='" . $_SESSION[ 'CURPORTFOLIO' ] . "';" );
		if ( !$result2 ) {
			die( 'Invalid query: ' . mysql_error() );
			$_SESSION[ "ERROR" ] = 'Invalid query: ' . mysql_error();
			header( 'Location: Error.php' );
		}
		$row2 = $result2->fetch_assoc();
	//	echo "Current Portfolio Balance: " . $row2[ 'Balance' ];
	?>

	<title>Portfolio Page</title>
</head>

<body>
	<h1>Portfolio Page</h1>
	<?php include 'menu.php';?>
	<h2>Users Portfolio</h2>



	<div class="row">
		<h3>Balance and Reporting</h3>
		<?php 
	$result2 = $conn->query("SELECT BankBalance FROM np397.SM_Users where Username='".$_SESSION["USER"]."';");
	if (!$result2) {
		die('Invalid query: ' . mysql_error());
		$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
		header('Location: Error.php');
	}
	$row2 = $result2->fetch_assoc();
	echo "Current Bank Balance: ". $row2['BankBalance'];
	?><br>
		<?php
		$result2 = $conn->query( "SELECT Balance FROM np397.SM_Portfolio where Username='" . $_SESSION[ "USER" ] . "' and portfolioID='" . $_SESSION[ 'CURPORTFOLIO' ] . "';" );
		if ( !$result2 ) {
			die( 'Invalid query: ' . mysql_error() );
			$_SESSION[ "ERROR" ] = 'Invalid query: ' . mysql_error();
			header( 'Location: Error.php' );
		}
		$row2 = $result2->fetch_assoc();
		echo "Current Portfolio Balance: " . $row2[ 'Balance' ];
		$_SESSION['sesbankbal'] = $row2[ 'Balance' ];
		?>
		<form method="post" Action="portfolioEdit.php">
			Amount of $ to add to your balance: <input name="funds" step=".01" type="number">
			<input type="submit" name="BALANCE_ADD" value="Add Funds">
		</form>
		<form method="post" Action="portfolioEdit.php">
			Amount of $ to remove from your balance: <input name="funds" step=".01" type="number">
			<input type="submit" name="BALANCE_SUB" value="Remove Funds">
		</form>
	</div>


	<div class="panel panel-default">
		<div class="panel-heading">
			<a href="javascript:void(0);" onclick="$('#editPort').slideToggle();"><h3>Edit Portfolio</h3></a>
		</div>
		<div class="panel-body">
			<div class='row' id="editPort">
				<!-- <h3>Edit Portfolio</h3> 	-->
				<form method="post" Action="portfolioEdit.php">
					Portfolio Name: <input name="name" type="text">
					<input type="submit" name="CHANGE" value="Change">
				</form>

				<form method = "post" Action ="portfolioEdit.php?PortfolioID">
	<?php 
	$result2 = $conn->query("SELECT * FROM np397.SM_Portfolio where Username='".$_SESSION["USER"]."';");
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

				<form method="post" Action="portfolioEdit.php">
					Portfolio Name: <input name="name" type="text">
					<input type="submit" name="ADD" value="Add Portfolio">
				</form>
			</div>
		</div>
	</div>


	<div class="panel panel-default">
		<div class="panel-heading">
			<a href="javascript:void(0);" onclick="$('#buys').slideToggle();"><h3>Buy Stock</h3></a>
		</div>
		<div class="panel-body">
			<div class='row' id='buys'>
				<form method="get" Action="Buy_Sell.php">
					Stock Symbol: <input name="symbol" type="text"> # of Stocks: <input name="amount" type="number">
					<input type="submit" name="BUY" value="Buy Stock">
				</form>
			</div>
		</div>


		<div class='row'>
			<h3>Select Portfolio</h3>
			<form name="SelectPortfolio" method="POST">
				<?php 
	$result2 = $conn->query("SELECT * FROM np397.SM_Portfolio where Username='".$_SESSION["USER"]."';");
	if (!$result2) {
		die('Invalid query: ' . mysql_error());
		$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
		header('Location: Error.php');
	}
	echo '<select onchange="this.form.submit()" name="portfolio" id="portfolio">
	      <option value="" selected="selected">Select a Portfolio to Use</option>';
	while ($row2 = $result2->fetch_assoc()){
		echo '<option value="'.$row2['portfolioID'].'"';
		if ($row2['portfolioID']==$_SESSION['CURPORTFOLIO']){
			echo 'selected="selected"';
		} 
		echo '>'.$row2['name'].'</option>';
	}
	echo '</select>';
	 ?>
			</form>
			<br>
		<!--	<div class="row">
				<a href="portfolio.php"><button>American</button>
				<a href ="portfoliosing.php"><button>Singapore</button>
				<a href="portfolioindia.php"><button>India</button>
				</a>
			</div>-->
		</div>
		<br><br>

		<div class="table-responsive">
			<table id="projectSpreadsheet">
				<thead>
					<tr>
						<th>Stock Name</th>
						<th>Stock Symbol</th>
						<th>Total Owned Shares</th>
						<th>Total Owned Amount</th>
						<th>Total Bought</th>
						<th>List Price</th>
						<th>Market Cap</th>
						<th>Open Price</th>
						<th>Close Price</th>
						<th>Rate Of Return</th>
						<th>Sell?</th>
						<th>View History</th>
					</tr>
				</thead>
				<tbody id="tbodyid">
					<?php
					if ( $_SESSION[ 'CURPORTFOLIO' ] != "" ) {
$result = mysqli_query( $conn, "select * from np397.SM_Stocks join np397.SM_StockList on StockSymbol = Symbol Where portfolioID='" . $_SESSION[ 'CURPORTFOLIO' ] . "' order by SM_StockList.Market asc;" );						$numrows = mysqli_num_rows( $result );
			  $TotalStock=0;

						  $myarray=array(0,0,0,0,0,0,0,0,0,0);
                          $_SESSION['finalarray']=array(0,0,0,0,0,0,0,0,0,0);
                          $_SESSION['TOTALPORT']=0;
						  $counter=0;

						while ( $row = mysqli_fetch_assoc( $result ) ) {
							$sss=$row['StockSymbol'];
							$result2 = $conn->query("SELECT * FROM np397.SM_StockList where Symbol='".strtoupper($sss)."';");
							$row22 = $result2->fetch_assoc();
							if ($row22['Market']=="NSE"){
								$sss=$row['StockSymbol'].".ns";
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
							
							$unitPriceS=$DATA[7];
							$unitPrice=$DATA[6];
							$MarketCap=$DATA[1];
							$OpenPrice=$DATA[3];
							$ClosePrice=$DATA[5];
							
							//gets the sum of shares
							$resultSuma = mysqli_query( $conn, "select sum(ShareQuantity) as aa from np397.SM_Transaction Where StockID='" . $row[ 'StockID' ] . "';" );
							$rowSuma = mysqli_fetch_assoc( $resultSuma );
							
							//idk anymore
							$resultSumb = mysqli_query( $conn, "select UnitPrice as bb from np397.SM_Transaction Where StockID='" . $row[ 'StockID' ] . "';" );
							$rowSumb = mysqli_fetch_assoc( $resultSumb );
							
							
								
							//gets the total bought
							$resultl = mysqli_query( $conn, "select * from np397.SM_Transaction Where StockID='" . $row[ 'StockID' ] . "';");
							$TOTALBOUGHT=0;
							//$TOTALPORT=0;
              
							while ( $row3 = mysqli_fetch_assoc($resultl)) {
								$TOTALBOUGHT+=$row3['ShareQuantity']*$row3['UnitPrice'];
								
							
							}
							$_SESSION['TOTALPORT']=$_SESSION['TOTALPORT']+($rowSuma['aa']*$unitPrice);
								
                                                                                  
							$TotalStock=$TotalStock+1;
                                                                        
							$rateofreturn=((($unitPrice*$rowSuma['aa'])-$TOTALBOUGHT)/$TOTALBOUGHT)*100;
							$expectedreturn=($rateofreturn*.10);
							
							
							$myarray[$counter]=($unitPriceS);//*$rowSuma['aa']);
							$counter=$counter+1;
							//echo("Expected reutun".$expectedreturn);
							
                $SRate=$SRate.round($rateofreturn,2)."|";                                                                             
						 
						//echo $TOTALPORT;
							
							?>
					<tr>
						<td>
							<?php echo $row['StockName']?>
						</td>
						<td>
							<?php echo $row['StockSymbol']?>
						</td>
						<td>
							<?php echo $rowSuma['aa']?>
						</td>
						<td>
							<?php echo $unitPrice*$rowSuma['aa']?>
						</td>
						<td>
							<?php echo $TOTALBOUGHT;?>
						</td>
						<td>
							<?php echo $unitPrice?></td>
						<td>
							<?php echo $MarketCap?>
						</td>
						<td>
							<?php echo $OpenPrice?></td>
						<td>
							<?php echo $ClosePrice?></td>
						<td>
							<?php echo round($rateofreturn,2)."%"?></td>
						<td>
							<form method="get" Action="Buy_Sell.php">
								<input name="StockID" type="hidden" value="<?php echo $row['StockID']?>">
								<input type="number" name="SELL_NUM">
								<input type="submit" name="SELL" value="Sell">
							</form>
							<?php //echo "<input type='checkbox' name='checkbox[".$row['StockID']."]' value='[".$row['StockID']."]'";?>
							<form method="get" Action="Buy_Sell.php">
								<input name="StockID" type="hidden" value="<?php echo $row['StockID']?>">
								<input type="number" type="hidden" value="-1" name="SELL_NUM">
								<input type="submit" name="SELL" value="Sell All">
							</form>
							<td>
								<button name="HISTORY" onClick='location.href="Buy_Sell.php?OP=HISTORY&StockID=<?php echo $row['StockID']?>"'>See History</button>
							</td>
					</tr>
					<?php }} 
					//echo ($_SESSION['sesbankbal']+$TOTALPORT)*0.75." is 75% of ".$TOTALPORT;
					?>
				</tbody>
			</table>
		</div>
		<br>
   <div>

<?php

$result = $conn->query("SELECT * FROM `SM_StockList`
JOIN `SM_Stocks` ON SM_Stocks.StockSymbol = SM_StockList.Symbol
JOIN `SM_Portfolio` ON SM_Portfolio.portfolioID = SM_Stocks.PortfolioID
WHERE SM_Portfolio.Username = '".$_SESSION["USER"]."' AND SM_Portfolio.portfolioID = '".$_SESSION['CURPORTFOLIO']."';");


$counts=0;
$arrayER=array(0,0,0,0,0,0,0,0,0,0);
$arrayBETA=array(0,0,0,0,0,0,0,0,0,0);
while($row = $result->fetch_assoc())
{

$arrayER[$counts]=$row["ER"];
$arrayBETA[$counts]=$row["Beta"];

$counts=$counts+1;

}


?>
   <h3>Current Portfolio Expected Return = <?php echo round((($arrayER[0]*($myarray[0]/$_SESSION['TOTALPORT']))+($arrayER[1]*($myarray[1]/$_SESSION['TOTALPORT']))+($arrayER[2]*($myarray[2]/$_SESSION['TOTALPORT']))+($arrayER[3]*($myarray[3]/$_SESSION['TOTALPORT']))+($arrayER[4]*($myarray[4]/$_SESSION['TOTALPORT']))+($arrayER[5]*($myarray[5]/$_SESSION['TOTALPORT']))+($arrayER[6]*($myarray[6]/$_SESSION['TOTALPORT']))+($arrayER[7]*($myarray[7]/$_SESSION['TOTALPORT']))+($arrayER[8]*($myarray[8]/$_SESSION['TOTALPORT']))+($arrayER[9]*($myarray[9]/$_SESSION['TOTALPORT']))),4)?></h3>

   <h3>Current Portfolio Beta = <?php echo round((($arrayBETA[0]*($myarray[0]/$_SESSION['TOTALPORT']))+($arrayBETA[1]*($myarray[1]/$_SESSION['TOTALPORT']))+($arrayBETA[2]*($myarray[2]/$_SESSION['TOTALPORT']))+($arrayBETA[3]*($myarray[3]/$_SESSION['TOTALPORT']))+($arrayBETA[4]*($myarray[4]/$_SESSION['TOTALPORT']))+($arrayBETA[5]*($myarray[5]/$_SESSION['TOTALPORT']))+($arrayBETA[6]*($myarray[6]/$_SESSION['TOTALPORT']))+($arrayBETA[7]*($myarray[7]/$_SESSION['TOTALPORT']))+($arrayBETA[8]*($myarray[8]/$_SESSION['TOTALPORT']))+($arrayBETA[9]*($myarray[9]/$_SESSION['TOTALPORT']))),4)?></h3>





   <h3>Optimizer</h3>
   <form method="post" Action="RunOPT.php">
   
  <?php
   $_SESSION['finalarray'][0]=$myarray[0];
   $_SESSION['finalarray'][1]=$myarray[1];
   $_SESSION['finalarray'][2]=$myarray[2];
   $_SESSION['finalarray'][3]=$myarray[3];
   $_SESSION['finalarray'][4]=$myarray[4];
   $_SESSION['finalarray'][5]=$myarray[5];
   $_SESSION['finalarray'][6]=$myarray[6];
   $_SESSION['finalarray'][7]=$myarray[7];
   $_SESSION['finalarray'][8]=$myarray[8];
   $_SESSION['finalarray'][9]=$myarray[9];
   
   if((($_SESSION['sesbankbal']+$_SESSION['TOTALPORT'])*.10)<($_SESSION['sesbankbal']))
   {
   $_SESSION['ALLOW']="NO";
   }
   else{
   $_SESSION['ALLOW']="Yes";
   }
   
  if($TotalStock==10)
  {
    $inputopt="Input Expected Portfolio Beta: <input name=\"betaopt\" type=\"text\">";
  $OPTIMIZE="<input type=\"submit\" name=\"RUN\" value=\"Run\">";
  echo $inputopt.$OPTIMIZE;
  
  }
  else{
  echo "Can't Optimize Portfolio.";
  $number10=10-$TotalStock;
  echo "You Need To Purchase ".$number10." More Stocks";
  }
   ?>
</form></div>
 
		<?php
		//load the database configuration file
		if ( !empty( $_GET[ 'status' ] ) ) {
			switch ( $_GET[ 'status' ] ) {
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
			<?php 
      
      //echo($TOTALPORT);
      
      if(!empty($statusMsg)){
        echo '<div class="alert '.$statusMsgClass.'">'.$statusMsg.'</div>';
    } ?>
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<a href="javascript:void(0);" onclick="$('#importFrm').slideToggle();"><h3>Import and Export</h3></a>
				</div>
				<div class="panel-body" id="importFrm">
					<form action="functions.php" method="post" enctype="multipart/form-data">
						<input type="file" name="file"/>
						<input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
					</form>

					<br>
					<br>
					<br>
					<a href="#" id="xx" style="text-decoration:none;color:#000;background-color:#ddd;border:1px solid #ccc;padding:8px;">Export Table data into Excel</a>
					
					
				</div>
			</div>
		</div>

</body>
<script>
	$( '#buys' ).slideToggle();
	$( '#editPort' ).slideToggle();
	$( '#importFrm' ).slideToggle();
	$( document ).ready( function () {
		function exportTableToCSV( $table, filename ) {
			var $rows = $table.find( 'tr:has(td),tr:has(th)' ),
				// Temporary delimiter characters unlikely to be typed by keyboard
				// This is to avoid accidentally splitting the actual contents
				tmpColDelim = String.fromCharCode( 11 ), // vertical tab character
				tmpRowDelim = String.fromCharCode( 0 ), // null character
				// actual delimiter characters for CSV format
				colDelim = '","',
				rowDelim = '"\r\n"',
				// Grab text from table into CSV formatted string
				csv = '"' + $rows.map( function ( i, row ) {
					var $row = $( row ),
						$cols = $row.find( 'td,th' );
					return $cols.map( function ( j, col ) {
						var $col = $( col ),
							text = $col.text();
						return text.replace( /"/g, '""' ); // escape double quotes
					} ).get().join( tmpColDelim );
				} ).get().join( tmpRowDelim )
				.split( tmpRowDelim ).join( rowDelim )
				.split( tmpColDelim ).join( colDelim ) + '"',
				// Data URI
				csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent( csv );
			console.log( csv );
			if ( window.navigator.msSaveBlob ) { // IE 10+
				//alert('IE' + csv);
				window.navigator.msSaveOrOpenBlob( new Blob( [ csv ], {
					type: "text/plain;charset=utf-8;"
				} ), "csvname.csv" )
			} else {
				$( this ).attr( {
					'download': filename,
					'href': csvData,
					'target': '_blank'
				} );
			}
		}
		// This must be a hyperlink
		$( "#xx" ).on( 'click', function ( event ) {
			exportTableToCSV.apply( this, [ $( '#projectSpreadsheet' ), 'export.csv' ] );
			// IF CSV, don't do event.preventDefault() or return false
			// We actually need this to be a typical hyperlink
		} );
	} );
</script>


</html>
