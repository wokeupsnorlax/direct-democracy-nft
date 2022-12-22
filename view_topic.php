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
$stmt = $con->prepare('SELECT email, username, id FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($email,$username,$seshid);
$stmt->fetch();
$stmt->close();

$cid = $_GET['cid'];
$tid = $_GET['tid'];

$uid = $_SESSION['id'];
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Direct Democracy Communication</title>
  <link rel="icon" type="image/x-icon" href="img/favicon.ico">
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.min.js" integrity="sha512-tWHlutFnuG0C6nQRlpvrEhE4QpkG1nn2MOUMWmUeRePl4e3Aki0VB6W1v3oLjFtd0hVOtRQ9PHpSfN6u6/QXkQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	</head>
	<body class="loggedin bg-dark">
		<nav class="navtop">
			<div>
			<h1>
					<a href="home.php"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#profileModal"><i class='far fa-bookmark'></i> | Subs</button></a>
				</h1>
				
            <a><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-circle"></i><?=$username?></button></a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		
		<div class="content">
			
			

			<?php
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			$config = parse_ini_file('db.ini');
			$con =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
            $con->set_charset('utf8mb4'); // charset
            
            
            $cid = $_GET['cid'];
            $tid = $_GET['tid'];
            $pid = "";
            $rating_action = "";
            $current_rating_action = "not voted";
            
            $sql =  "SELECT * FROM topics WHERE category_id='".$cid."' AND id='".$tid."' LIMIT 1";
            $res = mysqli_query($con, $sql) or die(mysqli_error());

            $sql2 =  "SELECT * FROM posts WHERE category_id='".$cid."' AND topic_id='".$tid."'" ;
            $res2 = mysqli_query($con, $sql2) or die(mysqli_error());

            

            if (mysqli_num_rows($res) == 1){
                echo "<table width='100%'>";
                if ($_SESSION['id']){
                    
                //show reply button
                    
                    
                }else{
                    echo "<tr><td colspan='2'><p>Please login to add response</p></td></tr>";
                }
                while($row = mysqli_fetch_assoc($res)){
                    


                    echo "<h2 class='text-light'><br/><span class='badge bg-primary'><i class='fas fa-book-open'></i> | ".$row['topic_title']."</span></h2>";

                    
                    
                    
                    while($row2 = mysqli_fetch_assoc($res2)){
                        $sql7 = "SELECT COUNT(*) FROM rating_info WHERE post_id='".$row2['id']."' AND rating_action='updoot'";
                        $result7 = mysqli_query($con, $sql7);
                        $row7 = mysqli_fetch_array($result7);
                        $total7 = $row7[0];

                        $sql8 = "SELECT COUNT(*) FROM rating_info WHERE post_id='".$row2['id']."' AND rating_action='boop'";
                        $result8 = mysqli_query($con, $sql8);
                        $row8 = mysqli_fetch_array($result8);
                        $total8 = $row8[0];

                        $sql9 = "SELECT COUNT(*) FROM rating_info WHERE post_id='".$row2['id']."'";
                        $result9 = mysqli_query($con, $sql9);
                        $row9 = mysqli_fetch_array($result9);
                        $total9 = $row9[0];
                        
                        if($total9 != 0){
                            $total_updoot_perc =  round(($total7 / $total9) * 100,2);
                            $total_boop_perc =  round(($total8 / $total9)* 100, 2);
                        }
                        
                        if($total9 == 0){
                            $total_updoot_perc =  0;
                            $total_boop_perc =  0;
                        }

                        $post_id=$row2['id'];
                        $sql33="SELECT rating_action FROM rating_info WHERE user_id='".$_SESSION['id']."' AND  post_id='".$post_id."' LIMIT 1";
                        $res33 = mysqli_query($con, $sql33) or die(mysqli_error());
                        $row33 = mysqli_fetch_array($res33);
                        if (($row33 =="" ) || ($row33 =="not voted" )){
                            $current_rating_action ="not voted";
                        }else{
                            $current_rating_action = $row33[0];
                        }



                        


                        $post_creator = $row2['post_creator'];

                        $stmt666 = $con->prepare('SELECT username,id FROM users WHERE id = ?');
						// In this case we can use the account ID to get the account info.
						$stmt666->bind_param('i', $post_creator);
						$stmt666->execute();
						$stmt666->bind_result($post_creator_username,$user_id);
						$stmt666->fetch();
						$stmt666->close();

						if((!$post_creator_username)){
							$post_creator_username ='MissingNo';
						}



    echo "<div class='row bg-secondary text-white'>
            <p>".$row2['post_content']."</p>
            <p class='text-center'>by <a href='profile.php?uid=".$post_creator."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> - ".$post_creator_username."</span></a> - ".$row2['post_date']."</p>
        <hr /><p class='text-center'>Your Current Vote On This Comment: ".$current_rating_action."</p>
        <hr /> <div class='col-sm-6 text-center'>
                    <form action='update_updoots.php' method='post'>
                        <input type='hidden' name='rating_action' value='updoot'/>
                        <input type='hidden' name='tid' value='".$row2['id']."'/>
                        <button class='btn btn-success' type='submit' name='updoot_submit' id='updoot_submit' value='Up' ><i class='fas fa-angle-up'></i></button>
                        <p>".$total7."/".$total9." - ".$total_updoot_perc." %</p>
                    </form>
                </div>

                <div class='col-sm-6 text-center'>
                    <form action='update_updoots.php' method='post'>
                        
                        <input type='hidden' name='rating_action' value='boop'/>

                        <input type='hidden' name='tid' value='".$row2['id']."'/>

                        <button class='btn btn-danger' type='submit' name='updoot_submit' id='updoot_submit' value='Down'><i class='fas fa-angle-down'></i></button><p>".$total8."/".$total9." - ".$total_boop_perc." %</p>
                        
                    </form>
                </div>
        </div>";


                        

                       

                    }
                    $old_views = $row['topic_views'];
                    $new_views = $old_views + 1;
                    $sql3 = "UPDATE topics SET topic_views='".$new_views."' WHERE category_id='".$cid."' AND id='".$tid."' ";
                    $res3 = mysqli_query($con, $sql3) or die(mysqli_error());

                    //if updoot then updoot

                    //if boop then boop
                }
                echo"<button type='button' style='width:100%' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#replyModal'>Add Reply </button>";
                echo "</table>";
            }else{
                echo "<p>This topic does not exist | <a href='home.php'>Return to Forum Index</1></p>";
            }


            


			?>

			
			
		</div>





<!-- The Modal -->
<div class="modal" id="replyModal">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary">

      <!-- Modal Header -->
      <div class="modal-header text-white">
        <h4 class="modal-title">Reply</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body text-center">

        <div class="reply">
			
			<form action="post_reply_parse.php" method="post" autocomplete="off">
           
                <div class="mb-3 mt-3">
				<?php
					
				echo "<p><a name='username' type='text' id='username' href='profile.php?uid=".$uid."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$username."</span></a></p></div>";

				?></div>

                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="5" id="reply_content" name="reply_content" type="text"></textarea></div>


                <div class="mb-3 mt-3">
                <input type="hidden" name="cid" value="<?php echo $cid; ?>"/>
                <input type="hidden" name="tid" value="<?php echo $tid; ?>"/>
				<button class="btn btn-warning text-white" type="submit" name="reply_submit" value="Post">Reply</button></div>
                
			</form>
		</div>

        



      </div>

    </div>
  </div>
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
<?php

    
if(isset($_POST['reply_submit'])){
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $config = parse_ini_file('db.ini');
			$con =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
            $con->set_charset('utf8mb4'); // charset
        
    $creator = $_SESSION['id'];
    $cid = $_POST['cid'];
    $tid = $_POST['tid'];
    $reply_content = $_POST['reply_content'];

    $sql = "INSERT INTO posts (category_id, topic_id, post_creator, post_content, post_date) VALUES ('".$cid."', '".$tid."', '".$creator."', '".$reply_content."', now() )";
    $res = mysqli_query($con, $sql) or die(mysqli_error());

        $sql2 = "UPDATE catergories SET last_post_date=now(), last_user_posted='".$creator."' WHERE id='".$cid."' LIMIT 1";
        $res2 = mysqli_query($con, $sql2) or die(mysqli_error());

        $sql3 = "UPDATE topics SET topic_reply_date=now(), topic_last_user='".$creator."' WHERE id='".$tid."' LIMIT 1";
        $res3 = mysqli_query($con, $sql3) or die(mysqli_error());

        //send email to ppl involved with topic
        


        if ( ($res) && ($res2) && ($res3) ) {
            echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
            echo "<p>Reply successfully posted</p>";
        }else{
            echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
            echo "<p>There was a problem posting your reply, try again</p>";
        }
    }else{
        exit();
    }



?>

<script src="script.js" > </script>

	</body>
</html>