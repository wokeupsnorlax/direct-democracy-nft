<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
$config = parse_ini_file('db.ini');
$con =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
            $con->set_charset('utf8mb4'); // charset

if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt = $con->prepare('SELECT email, username FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($email,$username);
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
			
			

			<?php
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $config = parse_ini_file('db.ini');
			$con =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
            $con->set_charset('utf8mb4'); // charset
            
            
            $cid = $_GET['cid'];
            if (!isset($_SESSION['loggedin'])) {
                $logged = "Please log in to create topics in this forum";
            }else{
                $logged = "<a href='create_topic.php?cid=".$cid."' class=''><button style='width:100%;'class='btn btn-success'>Create a topic!</button></a>";
            }

            $sql =  "SELECT id FROM catergories WHERE id='".$cid."' LIMIT 1";
			$res = mysqli_query($con, $sql) or die(mysqli_error());


            $topics ="";
			
			if (mysqli_num_rows($res) == 1){
				$sql2 = "SELECT * FROM topics WHERE category_id='".$cid."' ORDER BY topic_reply_date DESC ";
				$res2 = mysqli_query($con, $sql2) or die(mysqli_error());

                
                if (mysqli_num_rows($res2) > 0){
                    $topics .= "<table width='100%' style='border-collapse:collapse;'>";
                    $topics .= "<tr><td colspan='3'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a><hr />".$logged."</td></tr>";
                    $topics .= "<tr style='background-color:#dddddd;'><td>Topic Title</td><td width='65' align='center'>Replies</td><td width='65' align='center'>Views</td></tr>";
                    $topics .= "<tr><td colspan='3'><hr /></td></tr>";
                    while($row = mysqli_fetch_assoc($res2)){
                        $tid = $row['id'];
                        $title = $row['topic_title'];
                        $views = $row['topic_views'];
                        $date = $row['topic_date'];
                        $creator = $row['topic_creator'];
                        
                        $sql3 = "SELECT COUNT(*) FROM posts WHERE topic_id='".$tid."'";
                        $result = mysqli_query($con, $sql3);
                        $row = mysqli_fetch_array($result);
                        $total = $row[0];

                        $topics .= "<tr><td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$title."</a><br /><span class='post_info'>Posted by: <a href='profile.php?uid=".$creator."'>".$creator."</a> on ".$date."</span></td><td align='center'>".$total."</td><td align='center'>".$views."</td></tr>";
                        $topics .= "<tr><td colspan='3'></td></tr>";
                    }
                    $topics .= "</table>";
                    echo $topics;
                }else{
                    
                    echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
                    echo"<h2 class='text-center'>There are no topics yet".$logged."</h2>";
                }
			} else {
				echo"<p><a href='home.php'>Return to Forum Index</1></p>";
			}
			?>

			
			
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
		 <tr><?php
					
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