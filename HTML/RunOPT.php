<?php 	
		require_once 'DBconnect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	/*
	writes the file
	*/
	$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
	$txt = "John Doe\n";
	fwrite($myfile, $txt);
	$txt = "Jane Doe\n";
	fwrite($myfile, $txt);
	fclose($myfile);

	$handle = fopen("newfile.txt", "r");
	echo $handle;
	
	/*
	runs the java to have the file executed
	*/
		//get request for stock info
		$urlFirst = 'https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=RUN';
		$contentsFirst = file_get_contents($urlFirst);

		//If $contents is not a boolean FALSE value.
		if($contentsFirst == false){
			$_SESSION["ERROR"] = 'Get request error';
			header('Location: Error.php');
			return;
		}
		$DATAFIRST = explode("`", $contentsFirst);
		
		//header("Location: portfolio.php");
}
?>