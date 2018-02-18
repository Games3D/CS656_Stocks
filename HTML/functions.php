<?php
//load the database configuration file
require_once 'DBconnect.php';

	session_start();
	echo($_SESSION["USER"]);
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
    //validate whether uploaded file is a csv file
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    
	if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'],$csvMimes)){
        
		if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            //open uploaded csv file with read only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            //skip first line
            fgetcsv($csvFile);
           
			//parse data from csv file line by line
           
			while(($line = fgetcsv($csvFile)) !== FALSE){
				
                //check whether member already exists in database with same email
                $prevQuery = "SELECT * FROM SM_Stocks WHERE Username = '".$_SESSION["USER"]."'";
				echo("   ".$prevQuery."  ");
                $prevResult = $conn->query($prevQuery);
                if($prevResult->num_rows > 0){
                    //update member data
                    $conn->query("UPDATE SM_Stocks SET StockName = '".$line[0]."', StockSymbol = '".$line[1]."', ListPrice = '".$line[2]."', MarketCap = '".$line[3]."', OpenPrice = '".$line[4]."', ClosePrice = '".$line[5]."'");
					echo("  Update String  ");
                }else{
                    //insert member data into database
                    $conn  ->query("INSERT INTO SM_Stocks (StockName, StockSymbol, ListPrice, MarketCap, OpenPrice, ClosePrice) VALUES ('".$line[0]."','".$line[1]."','".$line[2]."','".$line[3]."','".$line[4]."','".$line[5]."')");
					echo("  Insert String   ");
                }
				echo("INSERT INTO SM_Stocks (StockName, StockSymbol, ListPrice, MarketCap, OpenPrice, ClosePrice) VALUES ('".$line[0]."','".$line[1]."','".$line[2]."','".$line[3]."','".$line[4]."','".$line[5]."')");
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