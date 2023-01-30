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
	<body class="loggedin bg-dark">
		<nav class="navtop navbar navbar-expand-lg">
			<div>
				<h1><a href="home.php">Direct Democracy Communication</a></h1>
				
				<a><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-circle"></i><?=$username?></button></a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content container ">
            
        <?php

    
if(isset($_POST['reply_submit'])){
    
        
    $creator = $_SESSION['id'];
    $cid = $_POST['cid'];
    $tid = $_POST['tid'];
    
    $post_replying_to = $_POST['post_replying_to'];
    $reply_content = $_POST['reply_content'];
    if(!$post_replying_to){
      $post_replying_to=0;
    }

    $sql = "INSERT INTO posts (category_id, topic_id, post_creator, post_content, post_date, post_replying_to) VALUES ('".$cid."', '".$tid."', '".$creator."', '".$reply_content."', now(),'".$post_replying_to."' )";
    $res = mysqli_query($con, $sql) or die(mysqli_error());

        $sql2 = "UPDATE catergories SET last_post_date=now(), last_user_posted='".$creator."' WHERE id='".$cid."' LIMIT 1";
        $res2 = mysqli_query($con, $sql2) or die(mysqli_error());

        $sql3 = "UPDATE topics SET topic_reply_date=now(), topic_last_user='".$creator."' WHERE id='".$tid."' LIMIT 1";
        $res3 = mysqli_query($con, $sql3) or die(mysqli_error());

        //send email to ppl involved with topic
        


        if ( ($res) && ($res2) && ($res3) ) {
            echo"<div class='text-center text-white bg-secondary'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
            echo "<div class='text-center text-white bg-secondary'><p>Reply successfully posted</p></div>";
            header("Refresh: 1; url=home.php");
        }else{
            echo"<div class='text-center text-white bg-secondary'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
            echo "<p>There was a problem posting your reply, try again</p>";
            header("Refresh: 1; url=home.php");
        }
    }else{
        exit();
    }



?>
            
            </div>	


<!-- The Modal -->
<div class="modal" id="profileModal">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary text-white">

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
		  <td><a href='profile.php?uid=".$uid."'><span class='badge bg-success'><i class='fas fa-user-circle'></i> | ".$username."</span></a></td>";
	
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