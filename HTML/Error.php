<html>
<head>
<title>ERROR...</title>
<link rel="stylesheet" type="text/css" href="CSS/login.css">
<script>
	$("#nav").addClass("js").before('<div id="menu">&#9776;</div>');
	$("#menu").click(function(){
		$("#nav").toggle();
	});
	$(window).resize(function(){
		if(window.innerWidth > 700) {
			$("#nav").removeAttr("style");
		}
	});
	</script>
<?php
	require_once 'DBconnect.php';
	session_start();
	
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($_SESSION['ERROR_PATH']=="Videos"){
		$var="page_downloadmovies='2'";
	} else if ($_SESSION['ERROR_PATH']=="apks"){
		$var="page_downloadmusic='2'";
	} else if ($_SESSION['ERROR_PATH']=="books"){
		$var="page_downloadbooks='2'";
	} else if ($_SESSION['ERROR_PATH']=="tvShows"){
		$var="page_downloadtvshows='2'";
	} else if ($_SESSION['ERROR_PATH']=="downloads"){
		$var="page_downloadprograms='2'";
	} else if ($_SESSION['ERROR_PATH']=="tithes"){
		$var="page_tithes='2'";
	} else if ($_SESSION['ERROR_PATH']=="FD_calls"){
		$var="page_FD_calls='2'";
	} else if ($_SESSION['ERROR_PATH']=="churchMusic"){
		$var="page_musicbooks='2'";
	} else if ($_SESSION['ERROR_PATH']=="churchHome"){
		$var="page_churchhome='2'";
	}
	
	$result = $conn->query("UPDATE np397.Members SET ".$var." where username='".$_SESSION["username"]."';");
	header("Location: index.php");
}
?>
</head>

<body>
<table border="0" width="500" align="center" class="demo-table">
 <tbody>
<tr>
<td align="center">SERVER ERROR</td>
		</tr>
<tr>
<td align="center">
	<?php 
		session_start(); 
		if (isset($_SESSION["ERROR"]))
			echo $_SESSION["ERROR"];
		else
			echo "No errors found, please return home.";

		if(isset($_SESSION["ERROR_PATH"]) and $_SESSION["ERROR"] != "Your user does not have access to this page."){?>
			<br><a href="<?php echo $_SESSION["ERROR_PATH"]?>.php">Back</a>
		<?php }else if ($_SESSION["authenticated"] == "") { ?>
			<br><a  href="index.php">Home</a>
		<?php }

if ($_SESSION["ERROR"] == "Your user does not have access to this page."){
	?> </td></tr><br><br><br><br>

<!-- START CUSTOM HEADER -->
 
 <!--END HEADER -->

 
 <br>
 <br>
  


<?php
}
	?>
	</table>
<footer>
</footer>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>


