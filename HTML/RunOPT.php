<?php 	
require_once 'DBconnect.php';

session_start();



if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  //get request for stock info
		$urlFirst = 'https://web.njit.edu/~jp834/webapps8/NewFile.jsp?OPCODE=RUN&PARAMS=0,2,1,5,4,7,8,7,4,5,1,0,0,2,0,1,3,4,0,5,8,';

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