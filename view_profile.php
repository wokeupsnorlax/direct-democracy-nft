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

                        $topics .= "<tr><td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$title."</a><br /><span class='post_info'>Posted by: ".$creator." on ".$date."</span></td><td align='center'>".$total."</td><td align='center'>".$views."</td></tr>";
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





	</body>
</html>