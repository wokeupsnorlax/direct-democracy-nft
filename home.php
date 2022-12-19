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
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1><a href="home.php">Direct Democracy Communication</a></h1>
				
				<a><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-circle"></i><?=$username?></button></a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		
		<div class="content">
			<h2>Home</h2>


			<?php
				
			echo "<p>Welcome back, <a href='profile.php?uid=".$uid."'>".$username."</a>! Join a Sub and start a discussion!
			<button type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#catModal'>Create Sub</button></p>";

			?>
			

			<?php
			

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
           
				<?php
				//find the surrent user's id, then send info to get to profile page
				?>


                <div class="mb-3 mt-3">
				<?php
					
				echo "<p><a name='username' type='text' id='username' href='profile.php?uid=".$uid."'>".$username."</a></p></div>";

				?>
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