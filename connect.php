<?php
// Change this to your connection info.
$config = parse_ini_file('db.ini');
$con =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
$con->set_charset('utf8mb4'); // charset

if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

//mysqli_select_db($config['db']);
?>