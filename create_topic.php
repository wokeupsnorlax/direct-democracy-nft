<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();?>
<?php
// If the user is not logged in redirect to the login page...
if ( ( !isset($_SESSION['id']) ) || ($_GET['cid'] =="")) {
	header('Location: index.html');
	exit;
}
$cid = $_GET['cid'];
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
			<h2>Create a Topic</h2>
			<p>Welcome back, <a href="profile.php"><?=$_SESSION['name']?></a>! Make a post and let your voice be heard!
				<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#postModal">
    				Post
  				</button>

				
			</p>

			

			
			
		</div>




<!-- The Modal -->
<div class="modal" id="postModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Create a Post</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body text-center">

        <div class="post">
			
			<form action="create_topic_parse.php" method="post" autocomplete="off">
           
                <div class="mb-3 mt-3">
				<p><a name="username" type="text" id="username" href="profile.php"><?=$_SESSION['name']?></a></p></div>


                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="1" id="topic_title" name="topic_title" type="text"></textarea></div>

                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="5" id="topic_content" name="topic_content" type="text"></textarea></div>


                <div class="mb-3 mt-3">
                <input type="hidden" name="cid" value="<?php echo $cid; ?>"/>
				<input type="submit" name="topic_submit" value="Post"></div>
                
			</form>
		</div>

      </div>

    </div>
  </div>
</div>


	</body>
</html>