<?php
//load the database configuration file
include 'DBconnect.php';
echo("Got Into Here \n");
if(isset($_POST['importSubmit'])){
    echo("Got Into Here2 \n");
    //validate whether uploaded file is a csv file
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'],$csvMimes)){
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
             echo("Got Into Here3\n");
            //open uploaded csv file with read only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            //skip first line
            fgetcsv($csvFile);
            
            //parse data from csv file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE){
				echo("Got Into Here4\n");
                //check whether member already exists in database with same email
                $prevQuery = "SELECT * FROM SM_Portfolio WHERE Username = '".$_SESSION["USER"]."'";
                $prevResult = $db->query($prevQuery);
                if($prevResult->num_rows > 0){
                    //update member data
                    $db->query("UPDATE members SET StockName = '".$line[0]."', StockSymbol = '".$line[1]."', ListPrice = '".$line[2]."', MarketPrice = '".$line[3]."', OpenPrice = '".$line[4]."', ClosePrice = '".$line[5]."' WHERE Username = '".$_SESSION["USER"]."'");
					echo("Update String");
                }else{
                    //insert member data into database
                    $db->query("INSERT INTO members (StockName, StockSymbol, ListPrice, MarketPrice, OpenPrice, ClosePrice) VALUES ('".$line[0]."','".$line[1]."','".$line[2]."','".$line[3]."','".$line[3]."','".$line[4]."')");
					echo("Insert String");
                }
				echo("finishef while");
            }
            
            //close opened csv file
            fclose($csvFile);
			echo("towards th end");
            $qstring = '?status=succ';
        }else{
            $qstring = '?status=err';
        }
    }else{
        $qstring = '?status=invalid_file';
    }
}
echo("end and redirect");
//redirect to the listing page
header("Location: portfolio.php".$qstring);