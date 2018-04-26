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
				
			echo $line[0].$line[1].$line[2];	
				if ($line[0]=="BUY"){
?>
					<form id="buyForm" method="get" Action="Buy_Sell.php">
						Stock Symbol: <input name="symbol" type="text" value="<?php echo $line[1] ?>"> 
						# of Stocks: <input name="amount" type="number" value="<?php echo $line[2] ?>">
						<input name="AUTO_BUY" type="text" value="BUY">
					<input type="submit" name="BUY" value="Buy Stock">
				</form>
<script>
    document.forms["buyForm"].submit();
	alert('test');
</script>
<?php
					echo "*";
				} elseif ($line[0]=="SELL"){
					
				} else{
					echo "ERROR";
				}
			}
            //close opened csv file
            fclose($csvFile);
		  
			$qstring = '?status=succ';
        }else{
            $qstring = '?status=err';
        }
    }else{
        $qstring = '?status=invalid_file';
    }
}
//redirect to the listing page
//header("Location: portfolio.php".$qstring);
?>
