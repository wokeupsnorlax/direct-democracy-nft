<?php
$config = parse_ini_file('db.ini');
$con =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
$con->set_charset('utf8mb4'); // charset

//mysqli_select_db($config['db']);
?>