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
$stmt = $con->prepare('SELECT email,username,id FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($email,$username,$seshid);
$stmt->fetch();
$stmt->close();

$uid = $_GET['uid'];
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

	$sql99 = "SELECT username FROM accounts WHERE id='".$uid."' ";
	$res99 = mysqli_query($con, $sql99) or die(mysqli_error());
	$row99 = mysqli_fetch_assoc($res99);
	if ($row99 != "")
	{
		$prof_username = $row99['username'];
	}else{
		$prof_username = "MissingNo";
	}

	echo "<h2>$prof_username's Profile Page</h2>";
	echo "<button class='btn btn-success' data-bs-toggle='modal' data-bs-target='#messageModal'>Send Message to $prof_username</button>";
?>


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
	
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#votes">Voted</a>
    </li>
	
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#notvoted">Not Voted Yet</a>
    </li>
  </ul>

  <div class="tab-content">
    <div id="topics" class="container tab-pane active">
<?php
	
	
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




				
				
			
?></div>


<div id="votes" class="container tab-pane fade">

<?php
	$uid = $_GET['uid'];
	$votes ="";
		
	$sql888 = "SELECT * FROM rating_info WHERE user_id='".$uid."' ORDER BY rating_date DESC ";
	$res888 = mysqli_query($con, $sql888) or die(mysqli_error());

	if (mysqli_num_rows($res888) > 0){
		
		$votes .= "<table width='100%' style='border-collapse:collapse;'>";
		$votes .= "<h2>Votes</h2>";
		$votes .= "<tr style='background-color:#dddddd;'>
		<td >Votes ".$uid." Cast</td>
		<td width='200'>Topics ".$uid." Voted On</td>
		<td width='100' align='center'>Vote Cast</td>
		</tr>";
		$votes .= "<tr><td colspan='6'><hr /></td></tr>";

		while($row888 = mysqli_fetch_assoc($res888)){
			$pid = $row888['post_id'];
			$rating_action = $row888['rating_action'];
			$rating_date = $row888['rating_date'];
			$creator = $row888['user_id'];

			
			

			$sql39 = "SELECT * FROM posts WHERE  id='".$pid."'";
			$res39 = mysqli_query($con, $sql39) or die(mysqli_error());


			while($row39 = mysqli_fetch_assoc($res39)){
				$cid = $row39['category_id'];
				$content = $row39['post_content'];
				$tid = $row39['topic_id'];
				$date = $row39['post_date'];

				$sql40 = "SELECT * FROM topics WHERE  id='".$tid."'";
				$res40 = mysqli_query($con, $sql40) or die(mysqli_error());

				while($row40 = mysqli_fetch_assoc($res40)){
					
					$topic_date = $row40['topic_date'];
					$topic_title = $row40['topic_title'];
					$topic_creator = $row40['topic_creator'];
					
					
				}
				
				$votes .= "<tr>";
				$votes .= "<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$content."</a>
				<br /><span class='post_info'>Posted by: ".$creator." <br />on ".$date."</span></td>";
			
				$votes .= "<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$topic_title."</a>
				<br />
				<span class='post_info'>Created by: <a href='profile.php?uid=".$topic_creator."'>".$topic_creator."</a><br/> on ".$topic_date."</span></td>";
				
				$votes .= "
				<td align='center'>".$rating_action."</td>
				";
				$votes .= "
			
				</tr>";
				$votes .= "<tr><td colspan='6'><hr/></td></tr>";
				
			}
			

		}
		
		$votes .= "</table>";
		echo $votes;
	}
	else{
                    
		echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
		echo"<h2 class='text-center'>You haven't created a topic yet</h2>";
	}

	?>

</div>

<div id="notvoted" class="container tab-pane fade">

<?php
	$uid = $_GET['uid'];
	$notvoted ="";
		
	$sql77 = "SELECT * FROM posts ORDER BY post_date DESC ";
	$res77 = mysqli_query($con, $sql77) or die(mysqli_error());

	if (mysqli_num_rows($res77) > 0){
		
		$notvoted .= "<table width='100%' style='border-collapse:collapse;'>";
		$notvoted .= "<h2>Not Voted Yet</h2>";
		$notvoted .= "<tr style='background-color:#dddddd;'>
		<td >Posts ".$uid." Hasn't Voted On Yet</td>
		<td width='200'>From Topic</td>
		<td width='100' align='center'>Vote Cast</td>
		</tr>";
		$notvoted .= "<tr><td colspan='6'><hr /></td></tr>";

		while($row77 = mysqli_fetch_assoc($res77)){
			$pid = $row77['id'];
			$creator = $row77['post_creator'];


			



			$sql33="SELECT rating_action FROM rating_info WHERE user_id='".$uid."' AND  post_id='".$pid."' LIMIT 1";
			$res33 = mysqli_query($con, $sql33) or die(mysqli_error());
			$row33 = mysqli_fetch_array($res33);
			if (($row33 =="" ) || ($row33 =="not voted" )){

				

				$sql39 = "SELECT * FROM posts WHERE  id='".$pid."'";
				$res39 = mysqli_query($con, $sql39) or die(mysqli_error());
	
	
				while($row39 = mysqli_fetch_assoc($res39)){
					$cid = $row39['category_id'];
					$content = $row39['post_content'];
					$tid = $row39['topic_id'];
					$date = $row39['post_date'];
	
					$sql40 = "SELECT * FROM topics WHERE  id='".$tid."'";
					$res40 = mysqli_query($con, $sql40) or die(mysqli_error());
	
					while($row40 = mysqli_fetch_assoc($res40)){
						
						$topic_date = $row40['topic_date'];
						$topic_title = $row40['topic_title'];
						$topic_creator = $row40['topic_creator'];
						
						
					}
					
					
					$notvoted .= "<tr>";
					$notvoted .= "<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$content."</a>
					<br /><span class='post_info'>Posted by: ".$creator." <br />on ".$date."</span></td>";
				
					$notvoted .= "<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$topic_title."</a>
					<br />
					<span class='post_info'>Created by: <a href='profile.php?uid=".$topic_creator."'>".$topic_creator."</a><br/> on ".$topic_date."</span></td>";
					
					$notvoted .= "
					<td align='center'><div class='text-center'><a href ='view_topic.php?cid=".$cid."&tid=".$tid."' class=''><button style='width:100%;'class='btn btn-success'>Vote</button></a></div></td>";
					
					$notvoted .= "
				
					</tr>";
					$notvoted .= "<tr><td colspan='6'><hr/></td></tr>";
					
				}

			}else{
				$rating_action = $row33[0];
			}
			
            	


            

			
			
			
			

			
			

		}
		
		$notvoted .= "</table>";
		echo $notvoted;
	}
	else{
                    
		echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
		echo"<h2 class='text-center'>You haven't created a topic yet</h2>";
	}

	?>

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





<!-- The Modal -->
<div class="modal" id="messageModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Chat with <?=$prof_username?> and <?=$username?></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body text-center">
		<div class="row">
		<table>
		 <tr>
		  
		  <?php
				$messages = "<tr style='background-color:#dddddd;'><td>To/From/Date</td>
				<td>Message</td></tr>";

			$sql44 =  "SELECT * FROM dm WHERE to_id='".$uid."' AND from_id='".$seshid."'";
			$res44 = mysqli_query($con, $sql44) or die(mysqli_error());

		  echo"<tr><form action='create_message_parse.php' method='post' autocomplete='off'>
		  	
		  	<input type='hidden' name='to_id' value='".$uid."'/>
		  	<input type='hidden' name='from_id' value='".$seshid."'/>

		 	<div class='col'>
		  		<textarea class='form-control' rows='3' id='message_content' name='message_content' type='text'></textarea>
			</div>

		  <div class=''>
		  <input type='submit' name='dm_submit' value='Message'></div>
		  
	  		</form></tr>";
			  
while($row44 = mysqli_fetch_assoc($res44)){
	$to_id = $row44['to_id'];
	$from_id = $row44['from_id'];
	$message_date = $row44['message_date'];
	$message_content = $row44['message_content'];
	$messages .= "<tr><td>To: ".$to_id." |
	From: ".$from_id." <br/>on ".$message_date."</td>
	<td>" .$message_content."</td></tr>
	
	";
	
}
echo $messages ;

	  //show each message between users
		?>
		
		 </tr>
		</table>

      </div></div>

    </div>
  </div>
</div>
			
	</body>
</html>