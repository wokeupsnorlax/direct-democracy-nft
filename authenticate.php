<?php
session_start();
include_once("connect.php");

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('Please fill both the username and password fields!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
	
	if ($stmt->num_rows > 0) {
	$stmt->bind_result($id, $password);
	$stmt->fetch();
	// Account exists, now we verify the password.
	// Note: remember to use password_hash in your registration file to store the hashed passwords.
	if (password_verify($_POST['password'], $password)) {
		// Verification success! User has logged-in!
		// Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.






		
		session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $_POST['username'];
		$_SESSION['id'] = $id;






		//update username, user_id and unique_id in users table so password only has to be called at login
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		$config = parse_ini_file('db.ini');
				$conn =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
				$conn->set_charset('utf8mb4'); // charset
			
		$user_id = $_SESSION['id'];
		$user_name = $_SESSION['name'] ;

		$sql = "SELECT username, id FROM users WHERE username='".$user_name."' AND  id='".$user_id."'";
		$res = mysqli_query($conn, $sql) or die(mysqli_error());

		if($row = mysqli_fetch_assoc($res)){
			if ($user_name != ""){
				

				$sql3 = "UPDATE users SET username='".$user_name."',uneek_id='".$user_id."',id='".$user_id."' WHERE id='".$user_id."' LIMIT 1";
				$res3 = mysqli_query($conn, $sql3) or die(mysqli_error());


			}
			
		}else{
			$sql2 = "INSERT INTO users (username, id,uneek_id ) VALUES ('".$user_name."', '".$user_id."','".$user_id."' )  LIMIT 1";
				$res2 = mysqli_query($conn, $sql2) or die(mysqli_error());
				
		}
			
		













		header("Refresh: 0; url=profile.php?uid=".$_SESSION['id']."");
	} else {
		// Incorrect password
		echo "Incorrect username and/or password! <a href='index.html'>Back</a>";
	}
} else {
	// Incorrect username
	echo "Incorrect username and/or password! <a href='index.html'>Back</a>";
}
	$stmt->close();
}
?>