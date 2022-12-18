<?php
$config = parse_ini_file('db.ini');
$con = mysqli_connect("localhost",$config['username'],$config['password'],$config['db']);

mysqli_select_db($config['db']);
?>