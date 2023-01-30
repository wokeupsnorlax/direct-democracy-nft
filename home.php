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
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link href="dopenope.css" rel="stylesheet" type="text/css">
	</head>
	<body class="loggedin bg-dark">
		<nav class="navtop navbar navbar-expand-lg">
			<div>
				<h1><a href="home.php">Direct Democracy Communication</a></h1>
				
				<!--SEARCH BAR-->
				<a class="search-container">
					<form method="post">
					<input type="text" placeholder="Search.." name="searchSubs">
					<button type="submit" name="submitSearch"><i class="fa fa-search"></i></button>
					</form>
				</a>

				
				<!--PROFILE DROPDOWN-->
				<a type="button" data-bs-toggle="dropdown"><button class="btn btn-success  dropdown-toggle" ><i class="fas fa-user-circle"></i><?=$username?></button></a>
    				
					<ul class="dropdown-menu">
						<li>
							<a class="dropdown-item disabled" href="#">
							ID: <?=$seshid?> <?php echo"<a href='profile.php?uid=".$seshid."'><span class='badge bg-success'><i class='fas fa-user-circle'></i> |"?> <?=$username?></span></a>
							</a>
						</li>
						<li>
							<a class="dropdown-item disabled" href="#">
								<?=$email?>
							</a>
						</li>
					</ul>
				<!--PROFILE DROPDOWN-->




				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>

			
		</nav>

		
		
		<div class="content">
			<h2 class="text-white">Home</h2>


			<?php
				
			//echo "<p class='text-center text-white bg-secondary'>Welcome back! Click this --> <a href='profile.php?uid=".$uid."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$username."</span></a> to get started!</p>";
			
			echo "<p class='text-center text-white bg-secondary'>Join a <span class='badge bg-primary text-outline-black'><i class='far fa-bookmark'></i> | Sub</span> and start a discussion!</p>";

			?>
			

			<?php
			$stmt01 = $con->prepare('SELECT id,category_title,category_description FROM catergories ORDER BY category_title ASC');
			$stmt01->execute();
			
			$stmt01->store_result();
			$stmt01->bind_result($cid,$c_title,$c_content);

			$catergories = "";

			if ($stmt01->num_rows > 0) {
				while(($row01 = $stmt01->fetch()) ){
					$catergories .= "<div class='text-center bg-secondary'><a href ='view_category.php?cid=".$cid."' class=''><button style='width:100%;'class='btn btn-dark'><h1 ><span class='badge bg-primary'><i class='far fa-bookmark'></i> | ".$c_title."</span></h1><p>".$c_content."</p></button></a></div>";

				}
				echo $catergories;


				echo "";





			
			}else {

				echo "<p class='text-center text-white bg-secondary'>There are no posts available! Make a post and let your voice be heard!</p>";
			}
			$stmt01->fetch();
			$stmt01->close();

			
			?>

			<p class='text-center text-white bg-secondary'>
				Can't find what you're looking for? 
					<button type='button' class='btn btn-warning  text-white text-outline-black' data-bs-toggle='modal' data-bs-target='#catModal'>
						Create a <span class='badge bg-primary '><i class='far fa-bookmark'></i> | Sub</span>
					</button>
			</p>
			
		</div>




<!-- The Modal -->
<div class="modal" id="catModal">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary text-white">

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
				<?php
					
				echo "<p><a name='username' type='text' id='username' href='profile.php?uid=".$uid."'><span class='badge bg-success'><i class='fas fa-user-circle'></i> | ".$username."</span></a></p></div>";

				?>
                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="1" id="category_title" name="category_title" type="text" placeholder="Sub Title. Short and Concise is best"></textarea></div>

                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="5" id="category_description" name="category_description" type="text" placeholder="Sub description. Short and Concise is best" ></textarea></div>

                <div class="mb-3 mt-3">
				<button class="btn btn-warning text-white text-outline-black" type="submit" name="cat_submit" value="Post">Create Sub</button></div>
                
			</form>
		</div>

      </div>

    </div>
  </div>
</div>








	</body>
</html>

<?php

$con_search = new PDO("mysql:host=localhost;dbname=phplogin", 'root', '');

if (isset($_POST["submitSearch"])){
	$user_search_query = $_POST["searchSubs"];
	$stmt_search_query = $con_search->prepare("SELECT * FROM 'catergories' WHERE category_title='$user_search_query'");

	$stmt_search_query->setFetchMode(PDO:: FETCH_OBJ);
	$stmt_search_query->execute();

	if ($row_search = $stmt_search_query->fetch()){

		
		
		
		echo"<iframe src='profile.php?uid=".$uid."' width='100%' height='500' scrolling='yes'>
		




		</iframe>";



	}else{
		echo"Category doesn't exist";
	}

}



?>