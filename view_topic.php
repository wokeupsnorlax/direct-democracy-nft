<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
$cid = $_GET['cid'];
$tid = $_GET['tid'];
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Direct Democracy Communication</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.2/jquery.min.js" integrity="sha512-tWHlutFnuG0C6nQRlpvrEhE4QpkG1nn2MOUMWmUeRePl4e3Aki0VB6W1v3oLjFtd0hVOtRQ9PHpSfN6u6/QXkQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
			<h1><a href="home.php">Direct Democracy Communication</a></h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		
		<div class="content">
			<h2>Topics</h2>
			

			<?php
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			$con = mysqli_connect('localhost', 'root', '', 'phplogin');
            
            
            $cid = $_GET['cid'];
            $tid = $_GET['tid'];
            
            $sql =  "SELECT * FROM topics WHERE category_id='".$cid."' AND id='".$tid."' LIMIT 1";
            $res = mysqli_query($con, $sql) or die(mysqli_error());

            if (mysqli_num_rows($res) == 1){
                echo "<table width='100%'>";
                if ($_SESSION['id']){
                    echo "<tr><td colspan='2'><button type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#replyModal'>Add Reply </button>";
                }else{
                    echo "<tr><td colspan='2'><p>Please login to add response</p></td></tr>";
                }
                while($row = mysqli_fetch_assoc($res)){
                    $sql2 =  "SELECT * FROM posts WHERE category_id='".$cid."' AND topic_id='".$tid."'" ;
                    $res2 = mysqli_query($con, $sql2) or die(mysqli_error());
                    while($row2 = mysqli_fetch_assoc($res2)){
                        echo "<tr><td><p><div>".$row['topic_title']."<br /> by ".$row2['post_creator']." - ".$row2['post_date']."<hr />".$row2['post_content']."<hr /> <button type='button' class='btn btn-success' >Up</button>".$row['updoots']."<button type='button' class='btn btn-primary'>Down</button> ".$row['boops']."</p></div></td></tr>";
                        
                        
                        
                    }
                    $old_views = $row['topic_views'];
                    $new_views = $old_views + 1;
                    $sql3 = "UPDATE topics SET topic_views='".$new_views."' WHERE category_id='".$cid."' AND id='".$tid."' ";
                    $res3 = mysqli_query($con, $sql3) or die(mysqli_error());

                    //if updoot then updoot

                    //if boop then boop
                }
                echo "</table>";
            }else{
                echo "<p>This topic does not exist | <a href='home.php'>Return to Forum Index</1></p>";
            }





			?>

			
			
		</div>


<!-- The Modal -->
<div class="modal" id="replyModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Reply</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body text-center">

        <div class="reply">
			
			<form action="post_reply_parse.php" method="post" autocomplete="off">
           
                <div class="mb-3 mt-3">
				<p><a name="username" type="text" id="username" href="profile.php"><?=$_SESSION['name']?></a></p></div>

                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="5" id="reply_content" name="reply_content" type="text"></textarea></div>


                <div class="mb-3 mt-3">
                <input type="hidden" name="cid" value="<?php echo $cid; ?>"/>
                <input type="hidden" name="tid" value="<?php echo $tid; ?>"/>
				<input type="submit" name="reply_submit" value="Post"></div>
                
			</form>
		</div>

      </div>

    </div>
  </div>
</div>



	</body>
</html>