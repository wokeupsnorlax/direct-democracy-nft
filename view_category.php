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
$stmt = $con->prepare('SELECT email, username, id FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($email,$username,$seshid);
$stmt->fetch();
$stmt->close();


$uid = $_SESSION['id'];


            $cid = $_GET['cid'];
            if (!isset($_SESSION['loggedin'])) {
                $logged = "Please log in to create topics in this forum";
            }else{
                $logged = "<button style='width:100%;'class='btn btn-warning text-white text-outline-black'  data-bs-toggle='modal' data-bs-target='#postModal'>Create a <span class='badge bg-primary'><i class='fas fa-book-open'></i> | Topic!</span></button>";
            }
?>

<!DOCTYPE html>
<html>
<?php
   include('components/htmlhead.php');
?>
	<body class="loggedin bg-dark">
		<nav class="navtop navbar navbar-expand-lg">
			<div>
			<h1>
					<a href="home.php"><button type="button" class="btn btn-primary"><i class='far fa-bookmark'></i> | Subs</button></a>
				</h1>
				
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
		
		<div class="content bg-white">
			








            <div id="topics" class="container  bg-secondary">
					<?php
						$topics ="";
						
						//Get topics table info from database
						$stmt_topics = $con->prepare('SELECT topic_title,topic_date,id,topic_views,topic_creator FROM topics WHERE category_id = '.$cid.' ORDER BY topic_title ASC');
						$stmt_topics->execute();
						$stmt_topics->store_result();
						$stmt_topics->bind_result($t_title,$t_date,$tid,$t_views,$t_creator);
                        
                        $stmt_topics_cats = $con->prepare(
									'SELECT id,category_title,category_description,last_user_posted,last_post_date 
									FROM catergories 
									WHERE id = '.$cid.'');
								$stmt_topics_cats->execute();
								$stmt_topics_cats->store_result();
								$stmt_topics_cats->bind_result($t_cid,$t_c_title,$t_c_description,$t_c_last_user_posted,$t_c_last_post_date);
								$stmt_topics_cats->fetch();
								if (!$t_c_description){
									$t_c_description = "MissingDesc";
								}
								if (!$t_c_title){
									$t_c_title = "MissingCat";
								}
								if (!$t_cid){
									$t_cid = "1";
								}
						//if there are topics created by this user then show this stuff
						if ($stmt_topics->num_rows > 0) {

							//TOPICS Header HTML
							$topics .= "
							<div class='text-center'width='100% container' style='border-collapse:collapse;'>
								<div class='row text-center'>
									<div class='col-sm-12'>	
										<h2 class='text-white'>
											<span class='badge bg-primary'>
												<i class='fas fa-book-open'></i>
												| Topics
											</span> 
											<i class='fas fa-edit'></i> in:
											<span class='badge bg-primary'>
												<i class='far fa-bookmark'></i>
												| ".$t_c_title."
											</span>
										</h2>
									</div>
								</div>
							";

							while(($row_topics = $stmt_topics->fetch()) ){




								
								


                                $stmt555 = $con->prepare('SELECT username FROM users WHERE id = ?');
                                // In this case we can use the account ID to get the account info.
                                $stmt555->bind_param('i', $t_creator);
                                $stmt555->execute();
                                $stmt555->bind_result($topic_creator_username);
                                
        
                                if((!$topic_creator_username)){
                                    $topic_creator_username ='MissingNo';
                                }
                                $stmt555->fetch();
                                $stmt555->close();


								
								$stmt_reply_count = $con->prepare(
									"SELECT count(*) 
									FROM posts 
									WHERE topic_id = ?"); 
								$stmt_reply_count->bind_param('i', $tid);
								$stmt_reply_count->execute(); 
								$stmt_reply_count->bind_result($number_of_replies);
								$stmt_reply_count->fetch();







								//TOPICS content HTML		
								$topics .= "
									<div class='row'>
										<div class='col'>
											<a href='view_topic.php?cid=".$cid."&tid=".$tid."'>
												<button class='btn btn-dark text-white'>
													<p class='btn btn-primary'><span ><i class='fas fa-book-open text-white'></i> | ".$t_title."</span></p>

													<p class='post_info text-white' style='font-size:.5vw;' >
															<span class='badge bg-success' >
																<i class='fas fa-eye'style='font-size:.5vw;'></i>
																| ".$t_views."
															</span>
															<span class='badge bg-primary'>
																<i class='far fa-comments'style='font-size:.5vw;'></i>
																| ".$number_of_replies."
															</span>
													</p>

												</button>
											</a>
											<p>	
												<span class='post_info text-white' style='font-size:.5vw;' >
													<i class='fas fa-edit'style='font-size:.5vw;'></i>
													by: 
													<span class='post_info text-outline-black'>
                                                        <a href='profile.php?uid=".$t_creator."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$topic_creator_username."</span></a>
                                                    </span>
													to 
													<a href='view_category.php?cid=".$t_cid."'>
													<span class='badge bg-primary text-outline-black'>
														<i class='far fa-bookmark'style='font-size:.5vw;'></i>
														| ".$t_c_title."
													</span>
													</a>
													- ".$t_date."
												</span>
											</p>
										</div>	
									</div>
                                    
								<br />";

								$stmt_reply_count->close();

								
								
							}
							$topics .= "<div class='row'>
                                            <div class='col'>
                                                <h2 class='text-center text-white'>".$logged."</h2>
                                            </div>	
                                        </div>
                            </div>";
							
							echo $topics;
						}
						//if no topics exist
						else{	
							echo"<div class='row'>
							    <div class='col'>
							        <h2 class='text-center text-white'>There are no topics yet".$logged."</h2>
                                </div>	
							</div>";
							echo $topics;
						}$stmt_topics_cats->close();
						$stmt_topics->fetch();
						$stmt_topics->close();
					?>
				</div>


			

			

			
			
		</div>







<!-- The Modal -->
<div class="modal" id="postModal">
  <div class="modal-dialog">
    <div class="modal-content text-white bg-secondary">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Create a Topic</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body text-center text-white">

        <div class="post">
			
			<form action="create_topic_parse.php" method="post" autocomplete="off">
           
                <div class="mb-3 mt-3">
				<?php
					
				echo "<p><a name='username' type='text' id='username' href='profile.php?uid=".$uid."'><span class='badge bg-success'><i class='fas fa-user-circle'></i> | ".$username."</span></a></p></div>";

				?></div>


                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="1" id="topic_title" name="topic_title" type="text" placeholder="Topic you want to discuss"></textarea></div>

                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="5" id="topic_content" name="topic_content" type="text" placeholder="Comment that will be voted on"></textarea></div>


                <div class="mb-3 mt-3">
                <input type="hidden" name="cid" value="<?php echo $cid; ?>"/>
				<button class="btn btn-warning text-white text-outline-black" type="submit" name="topic_submit" value="Post">Create Topic</button></div>
                
			</form>
		</div>

      </div>

    </div>
  </div>
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