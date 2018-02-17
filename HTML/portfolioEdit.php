<?php 	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "hiiiiiiiiiiiiiii";
		
		if (isset($_POST['CHANGE'])){
			echo "CHANGE";
			//$query = "insert into np397.SM_Portfolio Where Username= '".$_SESSION["USER"]."';";
		} elseif (isset($_POST['ADD'])){
			echo "ADD";
		} elseif (isset($_POST['DELETE'])){
			echo "DELETE";
		}
}

//header("Location: portfolio.php");
?>