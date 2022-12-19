<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
include_once("connect.php");

if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt = $con->prepare('SELECT email,username FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($email,$username);
$stmt->fetch();
$stmt->close();

//$uid = $_GET['uid'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
  <link rel="icon" type="image/x-icon" href="img/favicon.ico">
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1><a href="home.php">Direct Democracy Communication</a></h1>
				
				<a><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-circle"></i><?=$username?></button></a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content container ">
            
            <?php


if(isset($_POST['cat_submit'])){
    if (($_POST['catergory_title'] =="") && ($_POST['catergory_description'] =="")){
        echo "Fill out both fields";
        exit();
    }else{
        

       
        $title = $_POST['catergory_title'];
        $content = $_POST['catergory_description'];
        $creator = $_SESSION['id'];


		$sql =  "INSERT INTO catergories (catergory_title, catergory_description, last_user_posted, last_post_date) VALUES ('".$title."', '".$content."', '".$creator."', now())";
		$res = mysqli_query($con, $sql) or die(mysqli_error());
		$new_cat_id = mysqli_insert_id($con);
        
        

        if (($res)){
            header("Location: home.php");
        }else{
            echo "Error, try again.";
        }
        
    }
}
?></div>	


<!-- The Modal -->
<div class="modal" id="profileModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Account Details</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
		<table>
		 <tr>
		  <td>Username ID:</td>
		  <td><?=$_SESSION['id']?></td>
		 </tr>
		 <tr>
		  <?php
					
		echo "<td>Username:</td>
		  <td><a href='profile.php?uid=".$uid."'>".$username."</a></td>";
	
		?>
		 </tr>
		 <tr>
		  <td>Email:</td>
		  <td><?=$email?></td>
		 </tr>
		</table>

      </div>

    </div>
  </div>
</div>
</body>
</html>