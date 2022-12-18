<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
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
			<h2>Home</h2>
			<p>Welcome back, <a href="profile.php"><?=$_SESSION['name']?></a>! Join a Sub and start a discussion!
				<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#catModal">
    				Create Sub
  				</button>

				
			</p>

			<?php
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			$config = parse_ini_file('db.ini');
			$con = mysqli_connect("localhost",$config['username'],$config['password'],$config['db']);

			$sql =  "SELECT * FROM catergories ORDER BY catergory_title ASC";
			$res = mysqli_query($con, $sql) or die(mysqli_error());
			$categories = "";
			if (mysqli_num_rows($res) > 0){
				while($row = mysqli_fetch_assoc($res)){
					$id = $row['id'];
					$title = $row['catergory_title'];
					$description = $row['catergory_description'];
					$categories .= "<div class='text-center'><a href ='view_category.php?cid=".$id."' class=''><button style='width:100%;'class='btn btn-success'><h1 >".$title."</h1><p>".$description."</p></button></a></div>";
				}
				echo $categories;
			} else {
				echo"<p>There are no posts available! Make a post and let your voice be heard!</p>";
			}
			?>

			
			
		</div>




<!-- The Modal -->
<div class="modal" id="catModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Create a Sub</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body text-center">

        <div class="post">
			
			<form action="create_cat_parse.php" method="post" autocomplete="off">
           
                <div class="mb-3 mt-3">
				<p><a name="username" type="text" id="username" href="profile.php"><?=$_SESSION['name']?></a></p></div>


                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="1" id="catergory_title" name="catergory_title" type="text"></textarea></div>

                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="5" id="catergory_description" name="catergory_description" type="text"></textarea></div>

                <div class="mb-3 mt-3">
				<input type="submit" name="cat_submit" value="Post"></div>
                
			</form>
		</div>

      </div>

    </div>
  </div>
</div>


	</body>
</html>