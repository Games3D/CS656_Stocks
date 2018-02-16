<?php
$servername ="sql2.njit.edu";
$username = "np397";
$password = "9hmUj8UAX";
$dbname="np397";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	echo "<body style='background-color:red'>";
    die("Connection failed: " . $conn->connect_error);
}
else
	//echo "<body style='background-color:green'>";
?>