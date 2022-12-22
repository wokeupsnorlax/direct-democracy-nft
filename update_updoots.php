<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
include_once("connect.php");


// We don't have the account info stored in sessions so instead we can get the results from the database.
$stmt = $con->prepare('SELECT email,username FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($email,$username);
$stmt->fetch();
$stmt->close();
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
		<nav class="navtop">
			<div>
				<h1><a href="home.php">Direct Democracy Communication</a></h1>
				
				<a><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-circle"></i><?=$username?></button></a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content container ">

            <?php

                
                if(isset($_POST['updoot_submit'])){
                    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                    $config = parse_ini_file('db.ini');
                            $con =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
                            $con->set_charset('utf8mb4'); // charset
                        
                    $creator = $_SESSION['id'];
                    $tid = $_POST['tid'];
                    $rating_action = $_POST['rating_action'];

                    $sql = "SELECT post_id, user_id, rating_action FROM rating_info WHERE user_id='".$creator."' AND  post_id='".$tid."'";
                    $res = mysqli_query($con, $sql) or die(mysqli_error());

                    if($row = mysqli_fetch_assoc($res)){
                        if ($rating_action != ""){
                            

                            $sql3 = "UPDATE rating_info SET rating_date=now(), rating_action='".$rating_action."' WHERE  user_id='".$creator."' AND  post_id='".$tid."' LIMIT 1";
                            $res3 = mysqli_query($con, $sql3) or die(mysqli_error());


                            //send email to ppl involved with topic

                            if ( ($res3)) {
                                echo"<div class='text-center bg-dark text-white'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to <span class='badge bg-info'><i class='fas fa-bookmark'></i> | Sub</span> Index</button></a></div>";
                                
                                if ( ($rating_action=="updoot")) {
                                echo "<div class='text-center bg-success text-white'><p>'".$rating_action."' successfully updated</p>";
                                }
                                if ( ($rating_action=="boop")) {
                                    echo "<div class='text-center bg-danger text-white'><p>'".$rating_action."' successfully updated</p>";
                                }
                                header("Refresh: 1; url=home.php");
                            }else{
                                echo "<div class='text-center bg-secondary text-white'><p>There was a problem, try again</p></div>";
                                header("Refresh: 1; url=home.php");
                            }
                        }
                        
                    }else{
                        $sql2 = "INSERT INTO rating_info (post_id, user_id, rating_action, rating_date) VALUES ('".$tid."', '".$creator."', '".$rating_action."', now() )  LIMIT 1";
                            $res2 = mysqli_query($con, $sql2) or die(mysqli_error());
                            if ( ($res2)) {
                                
                                echo"<div class='text-center bg-dark text-white'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to <span class='badge bg-info'><i class='fas fa-bookmark'></i> | Sub</span> Index</button></a></div>";

                                if ( ($rating_action=="updoot")) {
                                    echo "<div class='text-center bg-success text-white'><p>'".$rating_action."' successfully updated</p>";
                                    }
                                    if ( ($rating_action=="boop")) {
                                        echo "<div class='text-center bg-danger text-white'><p>'".$rating_action."' successfully updated</p>";
                                        
                                    }

                                    header("Refresh: 1; url=home.php");
                            }else{
                                echo "<div class='text-center bg-warning text-white'><p>There was a problem, try again</p></div>";
                                header("Refresh: 1; url=home.php");
                            }
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
                        <td><a href='profile.php?uid=".$creator."'><span class='badge bg-success'><i class='fas fa-user-circle'></i> | ".$username."<span></a></td>";
                    
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