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
				//get request for stock info
			
			//TODO get request
			$unitPrice=9;
			$StockName="new stock";
			$ListPrice=343;
			$MarketCap=456565;
			$OpenPrice=2323;
			$ClosePrice=23232;
				
				if ($line[0]=='BUY'){
					if (($line[2]*$unitPrice) > $BALANCE){
						$_SESSION["ERROR"] = 'not enough funds to buy this stock';
						header('Location: Error.php');
						return;
					}
				} else {
					$result2 = $conn->query("select sum(ShareQuantity) as aa from np397.SM_Transaction Where StockID='".$line[1]."';");
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
				}
				
                //check whether member already exists in database with same email
                $prevResult = $conn->query("SELECT * FROM np397.SM_Stocks WHERE PortfolioID='".$_SESSION['CURPORTFOLIO']."' and StockSymbol='".$line[1]."'");
				$row2 = $prevResult->fetch_assoc();
				echo $line[0];
                if($prevResult->num_rows > 0){//stock aready there
					echo "4.1";
									
					if ($line[0]=='BUY')
						$a=$line[2];
					else 
						$a='-'.$line[2];
					
                    //update member data
                    $conn->query("INSERT INTO np397.SM_Transaction (StockID, ShareQuantity, UnitPrice, Timestamp) VALUES ('".$row2["StockID"]."', '".$a."', '".$unitPrice."', CURRENT_TIMESTAMP);");
					
                }else{//have to add new stock
					echo "4.2";
					
					if ($line[0]=='BUY')
						$a=$line[2];
					else 
						$a='-'.$line[2];
					
                    //insert member data into database
                  	$conn->query("INSERT INTO np397.SM_Stocks (PortfolioID, StockSymbol, StockName, ListPrice, MarketCap, OpenPrice, ClosePrice) VALUES ('".$_SESSION['CURPORTFOLIO']."', '".$line[1]."', '".$StockName."', '".$ListPrice."', '".$MarketCap."', '".$OpenPrice."', '".$ClosePrice."');");
				
					$result3 = $conn->query("SELECT StockID FROM np397.SM_Stocks where StockSymbol='".$line[1]."' and PortfolioID='".$_SESSION['CURPORTFOLIO']."';");
					
					if (!$result3) {
						die('Invalid query: ' . mysql_error());
						$_SESSION["ERROR"] = 'Invalid query: ' . mysql_error();
						header('Location: Error.php');
						return;
					}
					$row3 = $result3->fetch_assoc();
				
					$conn->query("INSERT INTO np397.SM_Transaction (StockID, ShareQuantity, UnitPrice, Timestamp) VALUES ('".$row3["StockID"]."', '".$a."', '".$unitPrice."', CURRENT_TIMESTAMP);");
					echo $row3["StockID"]."|";
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
header("Location: portfolio.php".$qstring);