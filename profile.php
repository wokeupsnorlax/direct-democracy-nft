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
		<div class="content container mt-3">

<h2>Profile Page</h2>

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link" href="home.php">Home</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" data-bs-toggle="tab" href="#topics">Topics</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#posts">Comments</a>
    </li>
  </ul>

  <div class="tab-content">
    <div id="topics" class="container tab-pane active">
<?php
	
	$uid = $_GET['uid'];
	$topics ="";
			
	$sql3 = "SELECT * FROM topics WHERE topic_creator='".$uid."' ORDER BY topic_reply_date DESC ";
	$res3 = mysqli_query($con, $sql3) or die(mysqli_error());

	if (mysqli_num_rows($res3) > 0){
		$topics .= "<table width='100%' style='border-collapse:collapse;'>";
		
		$topics .= "<h2>Topics</h2>";
		$topics .= "<tr style='background-color:#dddddd;'><td>Topics ".$uid." Created</td><td width='65' align='center'>Replies</td><td width='65' align='center'>Views</td></tr>";
		$topics .= "<tr><td colspan='3'><hr /></td></tr>";
				
		while($row = mysqli_fetch_assoc($res3)){
			$tid = $row['id'];
			$title = $row['topic_title'];
			$views = $row['topic_views'];
			$date = $row['topic_date'];
			$creator = $row['topic_creator'];
							
			$sql4 = "SELECT COUNT(*) FROM posts WHERE topic_id='".$tid."'";
			$result4 = mysqli_query($con, $sql4);
			$row4 = mysqli_fetch_array($result4);
			$total = $row4[0];
			$cid = $row['category_id'];

			
	
			$topics .= "<tr><td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$title."</a><br /><span class='post_info'>Posted by: ".$creator." on ".$date."</span></td><td align='center'>".$total."</td><td align='center'>".$views."</td></tr>";
			$topics .= "<tr><td colspan='3'><hr/></td></tr>";
		}


		$topics .= "</table>";
		echo $topics;

	}else{
                    
		echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
		echo"<h2 class='text-center'>You haven't created a topic yet</h2>";
	}




				
				
			
?>
</div>

<div id="posts" class="container tab-pane fade">



<?php
	
	$posts ="";
			
	$sql12 = "SELECT * FROM posts WHERE post_creator='".$uid."' ORDER BY post_date DESC ";
	$res12 = mysqli_query($con, $sql12) or die(mysqli_error());

	

	if (mysqli_num_rows($res12) > 0){
		$posts .= "<table width='100%' style='border-collapse:collapse;'>";
		$posts .= "<h2>Posts</h2>";
		$posts .= "<tr style='background-color:#dddddd;'>
		<td >Posts ".$uid." Created</td>
		<td width='200'>Topics ".$uid." Commented On</td>
		<td width='65' align='center'>Updoots</td>
		<td width='65' align='center'>Boops</td>
		<td width='65' align='center'>Votes Cast</td>
		</tr>";
		$posts .= "<tr><td colspan='6'><hr /></td></tr>";
				
		while($row11 = mysqli_fetch_assoc($res12)){
			$tid = $row11['topic_id'];
			$content = $row11['post_content'];
			
			$date = $row11['post_date'];
			$creator = $row11['post_creator'];
							
			$sql7 = "SELECT COUNT(*) FROM rating_info WHERE post_id='".$row11['id']."' AND rating_action='updoot'";
            $result7 = mysqli_query($con, $sql7);
            $row7 = mysqli_fetch_array($result7);
            $updoots = $row7[0];

            $sql8 = "SELECT COUNT(*) FROM rating_info WHERE post_id='".$row11['id']."' AND rating_action='boop'";
            $result8 = mysqli_query($con, $sql8);
            $row8 = mysqli_fetch_array($result8);
            $boops = $row8[0];

            $sql9 = "SELECT COUNT(*) FROM rating_info WHERE post_id='".$row11['id']."'";
            $result9 = mysqli_query($con, $sql9);
            $row9 = mysqli_fetch_array($result9);
            $votescast = $row9[0];


			$cid = $row11['category_id'];

			

	
			$posts .= "<tr>
			<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$content."</a>
			<br />
			<span class='post_info'>Posted by: ".$creator." <br />on ".$date."</span></td>";

			$sql33 = "SELECT * FROM topics WHERE id='".$tid."'";
			$res33 = mysqli_query($con, $sql33) or die(mysqli_error());
			while($row33 = mysqli_fetch_assoc($res33)){
				$topic_title = $row33['topic_title'];
				$topic_creator = $row33['topic_creator'];
				$topic_date = $row33['topic_date'];
			

				$posts .= "<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$topic_title."</a>
				<br />
				<span class='post_info'>Created by: <a href='profile.php?uid=".$topic_creator."'>".$topic_creator."</a><br/> on ".$topic_date."</span></td>";
			}


			$posts .= "<td align='center'>".$updoots."</td>
			<td align='center'>".$boops."</td>
			<td align='center'>".$votescast."</td>
			</tr>";
			$posts .= "<tr><td colspan='6'><hr/></td></tr>";
		}


		$posts .= "</table>";
		echo $posts;

	}else{
                    
		echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
		echo"<h2 class='text-center'>You haven't created a topic yet</h2>";
	}




				
				
			
?></div></div>




			
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