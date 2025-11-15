// Connect Database

<?php
$conn_error='Could not Connect.';
$mysql_host='localhost';
$mysql_user='root';
$mysql_pass='';
$mysql_db='scms';

$con=mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
// Check connection
if (mysqli_connect_errno()){
	die("Failed to connect to MySQL: " . mysqli_connect_error() . "<br>Make sure the database 'scms' exists!");
}

// Test if database and tables exist
$test_query = "SHOW TABLES";
$test_result = mysqli_query($con, $test_query);
if (!$test_result) {
	die("Database 'scms' exists but has issues: " . mysqli_error($con));
}
?>