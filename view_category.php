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
		
		<div class="content bg-white">
			
			

			<?php
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $config = parse_ini_file('db.ini');
			$con =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
            $con->set_charset('utf8mb4'); // charset
            
            
            $cid = $_GET['cid'];
            if (!isset($_SESSION['loggedin'])) {
                $logged = "Please log in to create topics in this forum";
            }else{
                $logged = "<a href='create_topic.php?cid=".$cid."' class=''><button style='width:100%;'class='btn btn-warning text-white'>Create a <span class='badge bg-primary'><i class='fas fa-book-open'></i> | Topic!</span></button></a>";
            }

            $sql =  "SELECT id FROM catergories WHERE id='".$cid."' LIMIT 1";
			$res = mysqli_query($con, $sql) or die(mysqli_error());


            $topics ="";
			
			if (mysqli_num_rows($res) == 1){
				$sql2 = "SELECT * FROM topics WHERE category_id='".$cid."' ORDER BY topic_reply_date DESC ";
				$res2 = mysqli_query($con, $sql2) or die(mysqli_error());

                
                if (mysqli_num_rows($res2) > 0){
                    $topics .= "<table width='100%' style='border-collapse:collapse;'>";
                    $topics .= "<tr><td colspan='3'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-dark'>Return to <span class='badge bg-primary'><i class='far fa-bookmark'></i> | Sub</span> Index</button></a><hr />".$logged."</td></tr>";
                    $topics .= "<tr style='background-color:#dddddd;'><td><span class='badge bg-primary'><i class='fas fa-book-open'></i> | Topic</span> Title</td><td width='65' align='center'><span class='badge bg-primary'><i class='far fa-comments'></i> | Comments</span></td><td width='65' align='center'><span class='badge bg-success'><i class='fas fa-eye'></i> | Views</span></td></tr>";
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

                        $stmt555 = $con->prepare('SELECT username,id FROM users WHERE id = ?');
						// In this case we can use the account ID to get the account info.
						$stmt555->bind_param('i', $creator);
						$stmt555->execute();
						$stmt555->bind_result($topic_creator_username,$user_id);
						$stmt555->fetch();
						$stmt555->close();

						if((!$topic_creator_username)){
							$topic_creator_username ='MissingNo';
						}

                        $topics .= "<tr>
                            <td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'><span class='badge bg-primary'><i class='fas fa-book-open'></i> | ".$title."</span></a>
                            <br /><span class='post_info'>Posted by: <a href='profile.php?uid=".$creator."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$topic_creator_username."</span></a> on ".$date."</span>
                            </td>
                            <td align='center'>".$total."</td><td align='center'>".$views."</td>
                        </tr>";
                        $topics .= "<tr><td colspan='3'></td></tr>";
                    }
                    $topics .= "</table>";
                    echo $topics;
                }else{
                    
                    echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-dark'>Return to Sub Index</button></a></div>";
                    echo"<div class='text-center'><h2 class='text-center'>There are no topics yet".$logged."</h2></div>";
                }
			} else {
				echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-dark'>Return to Sub Index</button></a></div>";
			}
			?>

			
			
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
		 <tr><?php
					
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


	</body>
</html>