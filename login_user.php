<?php
header('Content-type: text/html');

//Variable to connect to database
$server_name = "localhost";
$user_name = "root";
$user_password = "";
$database_name = "weather data";


//create connection 
$con =mysqli_connect($server_name, $user_name, $user_password, $database_name); 
 
//test  connection
if (mysqli_connect_errno()){
  echo "Failed to connect to MySQL: " . mysqli_connect_error(); 
} 
 
//SQL Query
$sql = "SELECT * FROM users where username = '". $_GET["user"] ."'  && password = '".$_GET["password"]."'";
//echo $sql; 
$result = mysqli_query($con,$sql); 

if($result -> num_rows > 0) { 
  //echo "Returned rows are: " . $result -> num_rows;
  echo "LoggedIN Successfully";
  return;
}

header("HTTP/1.0 400 Login Failed");
echo "Invalid Credentials!";
 
//end connection
mysqli_close($con); 

?>
