<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
include_once("connect.php");

// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt_get_loggedin_user = $con->prepare('SELECT email,username,id FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt_get_loggedin_user->bind_param('i', $_SESSION['id']);
$stmt_get_loggedin_user->execute();
$stmt_get_loggedin_user->bind_result($seshemail,$seshusername,$seshid);
$stmt_get_loggedin_user->fetch();
$stmt_get_loggedin_user->close();

$prof_id = $_GET['uid'];
$uid = $_GET['uid'];




//Get users table username from database
$stmt_get_prof_username = $con->prepare('SELECT username FROM users WHERE id = ?');
$stmt_get_prof_username->bind_param('i', $prof_id);
$stmt_get_prof_username->execute();
$stmt_get_prof_username->bind_result($prof_username);
$stmt_get_prof_username->fetch();
$stmt_get_prof_username->close();

if (!$prof_username){
	$prof_username = "MissingNo";
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
					<a href="home.php"><button type="button" class="btn btn-primary" ><i class='far fa-bookmark'></i> | Subs</button></a>
				</h1>
				
				<!--a><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-circle"></i><?=$seshusername?></button></a-->

				<!--PROFILE DROPDOWN-->
				<a type="button" data-bs-toggle="dropdown"><button class="btn btn-success  dropdown-toggle" ><i class="fas fa-user-circle"></i><?=$seshusername?></button></a>
    				
					<ul class="dropdown-menu">
						<li>
							<a class="dropdown-item disabled" href="#">
							ID: <?=$seshid?> <?php echo"<a href='profile.php?uid=".$seshid."'><span class='badge bg-success'><i class='fas fa-user-circle'></i> |"?> <?=$seshusername?></span></a>
							</a>
						</li>
						<li>
							<a class="dropdown-item disabled" href="#">
								<?=$seshemail?>
							</a>
						</li>
					</ul>
				<!--PROFILE DROPDOWN-->


				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>

		<!--LIVE CHAT NAV-->
		<nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-bottom">
  			<div class="container-fluid dropup">
    			<!--a type="button" data-bs-toggle="dropdown"><button class="btn btn-info text-white  dropdown-toggle" >
					<i class='far fa-comment-alt'></i>

				</button></a-->

				<button class='btn btn-outline-info text-white' data-bs-toggle='modal' data-bs-target='#chatModal'><i class='far fa-comment-alt'></i> | Send Message</button></div>



    				<!--LIVE CHAT BOX-->
					<ul class="dropdown-menu">
		<div class="row">	
			<div class="col-2">			
					
			
			
<nav class="navbar bg-light">
  	<?php 
		$stmt_dms = $con->prepare('SELECT to_id,from_id,message_content,message_date FROM dm WHERE from_id='.$seshid.' OR to_id = '.$seshid.' ORDER BY message_date ASC');
		$stmt_dms->execute();
		$stmt_dms->store_result();
		$stmt_dms->bind_result($to_id,$from_id,$message_content,$message_date);
		
		$dms_w_prof_user = "";
		
		if ($stmt_dms->num_rows > 0) {
			while(($row_dms = $stmt_dms->fetch()) ){

				//if($from_id==$to_id){
					//to user from profile
					//if($from_id==$seshid){
						$stmt_users_who_dms_w_prof_user = $con->prepare('SELECT id,username FROM users WHERE id='.$from_id.' OR id='.$to_id.'  ORDER BY username ASC');
						$stmt_users_who_dms_w_prof_user->execute();
										
						$stmt_users_who_dms_w_prof_user->store_result();
						$stmt_users_who_dms_w_prof_user->bind_result($chat_uid,$chat_username);
							
						$users_who_dms_w_prof_user = "";
							
						if ($stmt_users_who_dms_w_prof_user->num_rows > 0) {
							while(($row_users_who_dms_w_prof_user = $stmt_users_who_dms_w_prof_user->fetch()) ){

								if($from_id!=$to_id){
								
								$users_who_dms_w_prof_user .= "
									<form method='post' action= >
										<input type='hidden' name='chat_uid' value='".$chat_uid."'/>
										<button class='btn bg-info ' type='submit' name='prof_submit' id='prof_submit' value='Up' >
											<i class='fas fa-user-circle'></i> | ".$chat_username."</span>
										</button></form>";


										
								}


							}
							echo $users_who_dms_w_prof_user;
							echo "";
						}else {
							echo "<p class='text-center text-white bg-secondary'>There are no posts available! Make a post and let your voice be heard!</p>";
						}
						$stmt_users_who_dms_w_prof_user->fetch();
						$stmt_users_who_dms_w_prof_user->close();
					//}
				//}

			}
			echo $dms_w_prof_user;
			echo "";
		}else {
			echo "<p class='text-center text-white bg-secondary'>No chats started</p>";
		}
		$stmt_dms->fetch();
		$stmt_dms->close();
		
		echo"</nav></div>";

		if(!isset($_POST['prof_submit'])){
			
		echo"

			<div class='col'>	<iframe src='displaychat.php?to_id=".$seshid."&from_id=".$seshid."' width='100%' height='500' scrolling='yes'>
					
					</iframe>";
		
		}else{
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			$to_idnew = $_POST['chat_uid'];
			echo"<div class='col'>	<iframe src='displaychat.php?to_id=".$to_idnew."&from_id=".$seshid."' width='100%' height='500' scrolling='yes'>
					
					</iframe>";
		}
			


					
					







					?>
					
					
						<li>
							<!--a class="dropdown-item disabled" href="#">
							ID: <?=$seshid?> <a href='profile.php?uid=".$seshid."'><span class='badge bg-success'><i class='fas fa-user-circle'></i> | <?=$seshusername?></span></a>
							</a-->
						</li>
						<li>
							<a class="dropdown-item">
								<form action='create_message_parse.php' method='post' autocomplete='off'>
									<input type='hidden' name='to_id' value='<?php echo $to_id; ?>'/>
									<input type='hidden' name='from_id' value='<?php echo $from_id; ?>'/>
									
									<tr>
									<td><textarea class='form-control' rows='2' id='message_content' name='message_content' type='text'></textarea></td>

									<td width='180'><button class='btn btn-primary' style='width: 100%;' type='submit' name='dm_submit' >Message</button></td>
									</tr>
								</form>
							</a>
						</li>
			</div>
		</div>

					</ul>
  			</div>
		</nav>

		<div class="content container ">
			<?php

				

				echo "<div class='row bg-dark'><h2 class=' text-white'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | $prof_username</span>'s Profile</h2>";

				echo "<button class='btn btn-outline-info text-white' data-bs-toggle='modal' data-bs-target='#messageModal'><i class='far fa-comment-alt'></i> | Send Message</button></div>";
			?>

			<div class="tab-content bg-secondary  ">
				<!-- Nav tabs -->
				<ul class="nav nav-pills " role="tablist">
					<li class="nav-item  "><a class="nav-link" href="home.php"><button class="btn btn-success">Home</button></a></li>
					<li class="nav-item "><a class="nav-link " data-bs-toggle="pill" href="#topics"><button class="btn btn-primary text-white"><i class='fas fa-book-open'></i> | Topics</button></a></li>
					<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#posts"><button class="btn btn-primary text-white"><i class='far fa-comments'></i> | Comments</button></a></li>
					

					<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#votes"><button class="btn btn-success"><i class='fas fa-balance-scale'></i> | Voted</button></a></li>


					<li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#notvoted"><button class="btn btn-danger"><i class='fas fa-biohazard'></i> | Not Voted Yet</button></a></li>
					<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#messagefrom"><button class="btn btn-dark text-white"><i class='far fa-comment-alt'></i> | DMs From <span class="badge bg-info"><i class="fas fa-user-circle"></i> | <?=$prof_username?></span></button></a></li>
					<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#messageto"><button class="btn btn-dark text-white"><i class='far fa-comment-alt'></i> | DMs To <span class="badge bg-info"><i class="fas fa-user-circle"></i> | <?=$prof_username?></button></span></a></li>
				</ul>


				<div id="topics" class="container tab-pane fade bg-secondary">
					<?php
						$prof_topics ="";
						
						//Get topics table info from database
						$stmt_prof_topics = $con->prepare('SELECT category_id,topic_title,topic_date,id,topic_views FROM topics WHERE topic_creator = '.$prof_id.' ORDER BY topic_title ASC');
						$stmt_prof_topics->execute();
						$stmt_prof_topics->store_result();
						$stmt_prof_topics->bind_result($cid,$t_title,$t_date,$tid,$t_views);

						//if there are topics created by this user then show this stuff
						if ($stmt_prof_topics->num_rows > 0) {

							//TOPICS Header HTML
							$prof_topics .= "
							<div class='text-center'width='100% container' style='border-collapse:collapse;'>
								<div class='row text-center'>
									<div class='col-sm-12'>	
										<h2 class='text-white'>
											<span class='badge bg-primary'>
												<i class='fas fa-book-open'></i>
												| Topics
											</span> 
											<i class='fas fa-edit'></i> by:
											<span class='badge bg-dark'>
												<i class='fas fa-user-circle'></i>
												| ".$prof_username."
											</span>
											Created
										</h2>
									</div>
								</div>
							";

							while(($row_prof_topics = $stmt_prof_topics->fetch()) ){
								$stmt_prof_topics_cats = $con->prepare(
									'SELECT id,category_title,category_description,last_user_posted,last_post_date 
									FROM catergories 
									WHERE id = '.$cid.'');
								$stmt_prof_topics_cats->execute();
								$stmt_prof_topics_cats->store_result();
								$stmt_prof_topics_cats->bind_result($t_cid,$t_c_title,$t_c_description,$t_c_last_user_posted,$t_c_last_post_date);
								$stmt_prof_topics_cats->fetch();
								if (!$t_c_description){
									$t_c_description = "MissingDesc";
								}
								if (!$t_c_title){
									$t_c_title = "MissingCat";
								}
								if (!$t_cid){
									$t_cid = "1";
								}
								
								
								$stmt_reply_count = $con->prepare(
									"SELECT count(*) 
									FROM posts 
									WHERE topic_id = ?"); 
								$stmt_reply_count->bind_param('i', $tid);
								$stmt_reply_count->execute(); 
								$stmt_reply_count->bind_result($number_of_replies);
								$stmt_reply_count->fetch();

								//TOPICS content HTML		
								$prof_topics .= "
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
													<span class='badge bg-dark' >
														<i class='fas fa-user-circle'style='font-size:.5vw;'></i>
														| ".$prof_username."
													</span>
													to 
													<a href='view_category.php?cid=".$t_cid."'>
													<span class='badge bg-primary'>
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

								
								$stmt_prof_topics_cats->close();
							}
							$prof_topics .= "</div>";
							
							echo $prof_topics;
						}
						//if the user hasn't created any topics yet
						else{	
							echo"<div class='row'>
							<div class='col'>
							
							
							
							<h2 class='text-center text-white'><a href='profile.php?uid=".$prof_id."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$prof_username."</span></a> hasn't created a topic yet</h2></div>	
							</div>";
							echo $prof_topics;
						}
						$stmt_prof_topics->fetch();
						$stmt_prof_topics->close();
					?>
				</div>

<div id="posts" class="container tab-pane fade bg-secondary">
	<?php
		$prof_posts ="";
		//SELECT * FROM posts WHERE post_creator = profile id
		$stmt_prof_posts = $con->prepare('SELECT id,category_id,post_content,post_date,topic_id,post_creator FROM posts WHERE post_creator = '.$prof_id.' ORDER BY post_date DESC');
		$stmt_prof_posts->execute();$stmt_prof_posts->store_result();
		$stmt_prof_posts->bind_result($pid_posts,$cid_posts,$p_content_posts,$p_date_posts,$p_tid_posts,$post_creator_posts );

		//if there are posts WHERE post_creator = profile id
		if ($stmt_prof_posts->num_rows > 0) {
			//POSTS Header HTML
			$prof_posts .= "
			<div class='text-center'width='100% container' style='border-collapse:collapse;'>
				<div class='row text-center'>
					<div class='col'>	
						<h2 class='text-white'>
							<span class='badge bg-primary'>
								<i class='far fa-comments'></i>
								| Comments
							</span>
							<i class='fas fa-edit'></i> by:
							<span class='badge bg-dark'>
								<i class='fas fa-user-circle'></i>
								| ".$prof_username."
							</span>
						</h2>
					</div>
				</div>
			";
			//show the posts WHERE post_creator = profile id loop
			while(($row_prof_posts = $stmt_prof_posts->fetch()) ){	
				
				//SELECT * FROM categories WHERE category_id = cid_posts
				$stmt_prof_posts_cats = $con->prepare(
					'SELECT id,category_title,category_description,last_user_posted,last_post_date 
					FROM catergories 
					WHERE id = '.$cid_posts.'');
				$stmt_prof_posts_cats->execute();$stmt_prof_posts_cats->store_result();
				$stmt_prof_posts_cats->bind_result($p_cid_posts,$p_c_title_posts,$p_c_description_posts,$p_c_last_user_posted_posts,$p_c_last_post_date_posts);
				
				//show the posts WHERE category_id = cid_posts id loop
				while(($row_prof_posts_cats =$stmt_prof_posts_cats->fetch()) ){
								
					//SELECT * FROM topics WHERE topic_creator = cid_posts
					$stmt_prof_posts_tops = $con->prepare('SELECT category_id,topic_title,topic_date,topic_creator,topic_views FROM topics WHERE topic_creator = '.$prof_id.'');
					$stmt_prof_posts_tops->execute();$stmt_prof_posts_tops->store_result();
					$stmt_prof_posts_tops->bind_result($t_cid_posts,$t_title_posts,$t_date_posts,$t_tid_posts,$t_views_posts);
					$stmt_prof_posts_tops->fetch();

			/////					
					if (!$p_c_description_posts){
						$p_c_description_posts = "MissingCatDesc";
					}
					if (!$p_c_title_posts){
						$p_c_title_posts = "MissingCat";
					}
					if (!$p_cid_posts){
						$p_cid_posts = "1";
					}

					
					if (!$p_content_posts){
						$p_content_posts = "MissingComment";
					}

								
				if (!$t_title_posts){
					$t_title_posts = "MissingTop";
				}
				if (!$t_tid_posts){
					$t_tid_posts = "1";
				}

								
								
			
								$stmt_prof_posts_sesh_rating_action = $con->prepare(
									'SELECT rating_action
									FROM rating_info 
									WHERE user_id = '.$seshid.' AND post_id='.$pid_posts.'');
								$stmt_prof_posts_sesh_rating_action->execute();
								$stmt_prof_posts_sesh_rating_action->store_result();
								$stmt_prof_posts_sesh_rating_action->bind_result($posts_sesh_rating_action_posts);

								$stmt_prof_posts_sesh_rating_action->fetch();

								$bgup_posts="bg-secondary";
								$bgdown_posts="bg-secondary";

								

								
								$stmt_updoot_count = $con->prepare(
									"SELECT count(*) 
									FROM rating_info 
									WHERE rating_action = 'updoot' AND post_id='".$pid_posts."'"); 
								$stmt_updoot_count->execute(); 
								$stmt_updoot_count->bind_result($number_of_updoots_posts);
								$stmt_updoot_count->fetch();

								$stmt_updoot_count->close();

								$stmt_boop_count = $con->prepare(
									"SELECT count(*) 
									FROM rating_info 
									WHERE rating_action = 'boop' AND post_id='".$pid_posts."'"); 
								$stmt_boop_count->execute(); 
								$stmt_boop_count->bind_result($number_of_boops_posts);
								$stmt_boop_count->fetch();
								$stmt_boop_count->close();

								$bgvotetally_posts="btn-vote";
								$bgvoteytally_posts="btn-dark";
								$bgvoteyicon_posts="fas fa-balance-scale";


								if(($posts_sesh_rating_action_posts == 'updoot')){
									$bgup_posts='btn-dope';
									$bgdown_posts="btn-vote-nope";
								}
								if(($posts_sesh_rating_action_posts == 'boop')){
									$bgup_posts='btn-vote-dope';
									$bgdown_posts="btn-nope ";
								}

								if(($posts_sesh_rating_action_posts != 'boop') && ($posts_sesh_rating_action_posts != 'updoot')){
									$bgup_posts='btn-vote-dope';
									$bgdown_posts="btn-vote-nope";
								}
								

								$total_votes_posts = $number_of_boops_posts + $number_of_updoots_posts;

								if($number_of_boops_posts != $number_of_updoots_posts ){
									$total_updoot_perc_posts =  round(($number_of_updoots_posts / $total_votes_posts) * 100,2);
									$total_boop_perc_posts =  round(($number_of_boops_posts / $total_votes_posts)* 100, 2);
								}
								
								
								

								
									if($number_of_boops_posts > $number_of_updoots_posts){
										$bgvotetally_posts='btn-nope';
										$bgvoteytally_posts='bg-danger';
										$bgvoteyicon_posts='fas fa-balance-scale-right';
										$winperc_posts= $total_boop_perc_posts;
										}
								
									if($number_of_boops_posts < $number_of_updoots_posts){
										$bgvotetally_posts='btn-dope';
										$bgvoteytally_posts='bg-success';
										$bgvoteyicon_posts='fas fa-balance-scale-left';
										$winperc_posts= $total_updoot_perc_posts;
										
									}
								
									if($number_of_boops_posts == $number_of_updoots_posts ){
										$winperc_posts= 50;
										$bgvotetally_posts='btn-vote';
										$bgvoteytally_posts='bg-dark';
										$bgvoteyicon_posts='fas fa-balance-scale';
										if(($number_of_boops_posts == 0)||($number_of_boops_posts == 0)){
											$bgup_posts='btn-vote-dope';
											$bgdown_posts="btn-vote-nope";
										}
	
									}

								

			/////
								
								


								//posts content HTML		
								$prof_posts .= "
									<div class='row'>
										<div class='col'>
											<a href='view_topic.php?cid=".$p_cid_posts."&tid=".$p_tid_posts."'>
												<button class='btn btn-dark text-white'>
													<p class='btn ".$bgvotetally_posts."'><span ><i class='far fa-comments text-white'></i> | ".$p_content_posts."</span></p>
												</button>
											</a>
										</div>
									</div>
									<div class='row'>
										<div class='col'></div>
										<div class='col'>
											<form action='update_updoots.php' method='post'>
												<input type='hidden' name='rating_action' value='updoot'/>
												<input type='hidden' name='tid' value='".$pid_posts."'/>
												<button class='btn ".$bgup_posts." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
													<i class='fas fa-angle-up'style='font-size:.5vw;'></i>
													| ".$number_of_updoots_posts."
												</button>
											</form>
										</div>
										<div class='col'>
											<span class='badge ".$bgvoteytally_posts."' >
												<i class='".$bgvoteyicon_posts."'style='font-size:.5vw;'></i>
												| ".$winperc_posts."
											</span>
											
										</div>
										<div class='col'>
											<form action='update_updoots.php' method='post'>
												<input type='hidden' name='rating_action' value='boop'/>
												<input type='hidden' name='tid' value='".$pid_posts."'/>
												<button class='btn ".$bgdown_posts." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
													<i class='fas fa-angle-down'style='font-size:.5vw;'></i>
													| ".$number_of_boops_posts."
												</button>
											</form>

										</div>
										<div class='col'></div>
									</div>
									<div class='row'>	
										<div class='col'>		
												<span class='post_info text-white' style='font-size:.5vw;' >
													<i class='fas fa-edit'style='font-size:.5vw;'></i>
													by: 
													<span class='badge bg-dark' >
														<i class='fas fa-user-circle'style='font-size:.5vw;'></i>
														| ".$prof_username."
													</span>
													to 
													<a href='view_category.php?cid=".$p_cid_posts."'>
													<span class='badge bg-primary'>
														<i class='far fa-bookmark'style='font-size:.5vw;'></i>
														| ".substr($p_c_title_posts, 0, 40)."
													</span>
													</a>
													- ".$p_date_posts."
												</span>
											
										</div>

									</div>
									
								<br />";
								
								
								
								}
								
								
								$stmt_prof_posts_tops->fetch();
								
								$stmt_prof_posts_tops->close();

								}
								$stmt_prof_posts_cats->fetch();
								$stmt_prof_posts_cats->close();
								
								



								$stmt_prof_posts_sesh_rating_action->close();
							
							$prof_posts .= "</div>";
							
							echo $prof_posts;
						}
						//if the user hasn't created any posts yet
						else{	
							echo"<div class='row'>
							<div class='col'><h2 class='text-center text-white'><a href='profile.php?uid=".$prof_id."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$prof_username."</span></a> hasn't commented on a topic yet</h2></div>	
							</div>";
							echo $prof_posts;
						}
						$stmt_prof_posts->fetch();
						$stmt_prof_posts->close();
					?>
				</div>






<div id="votes" class="container tab-pane fade bg-secondary">
					<?php
						$prof_votes ="";

						//Get posts table info from database
						$stmt_prof_votes = $con->prepare('SELECT post_id,rating_action,rating_date FROM rating_info WHERE user_id = '.$prof_id.' ORDER BY rating_date DESC');
						$stmt_prof_votes->execute();
						$stmt_prof_votes->store_result();
						$stmt_prof_votes->bind_result($pid_vote,$rating_action_vote,$rating_date_vote );


						if ($stmt_prof_votes->num_rows > 0) {


							//POSTS Header HTML
							$prof_votes .= "
							<div class='text-center'width='100% container' style='border-collapse:collapse;'>
								<div class='row text-center'>
									<div class='col'>	
										<h2 class='text-white'>
											<span class='badge bg-success'>
												<i class='fas fa-balance-scale'></i>
												| Votes
											</span>
											 by:
											<span class='badge bg-dark'>
												<i class='fas fa-user-circle'></i>
												| ".$prof_username."
											</span>
										</h2>
									</div>
								</div>
							";

						while(($row_prof_votes = $stmt_prof_votes->fetch()) ){



						
						//Get posts table info from database
						$stmt_prof_posts_votes  = $con->prepare('SELECT id,category_id,post_content,post_date,topic_id,post_creator FROM posts WHERE id = '.$pid_vote.' ORDER BY post_date DESC ');
						$stmt_prof_posts_votes ->execute();
						$stmt_prof_posts_votes ->store_result();
						$stmt_prof_posts_votes ->bind_result($pid_vote,$cid_vote,$p_content_vote,$p_date_vote,$p_tid,$post_creator_vote );

						//if there are posts created by this user then show this stuff
						if ($stmt_prof_posts_votes ->num_rows > 0) {

							
							//get cat info

							

							while(($row_prof_posts_votes  = $stmt_prof_posts_votes ->fetch()) ){
								
								//Get users table username from database
								$stmt_get_post_username = $con->prepare('SELECT username FROM users WHERE id = ?');
								$stmt_get_post_username->bind_param('i', $post_creator_vote);
								$stmt_get_post_username->execute();
								$stmt_get_post_username->bind_result($post_username_vote);
								$stmt_get_post_username->fetch();
								$stmt_get_post_username->close();

								if (!$post_username_vote){
									$post_username_vote = "MissingNo";
								}
								
								$stmt_prof_votes_cats = $con->prepare(
									'SELECT id,category_title,category_description,last_user_posted,last_post_date 
									FROM catergories 
									WHERE id = '.$cid_vote.'');
								$stmt_prof_votes_cats->execute();
								$stmt_prof_votes_cats->store_result();
								$stmt_prof_votes_cats->bind_result($p_cid_vote,$p_c_title_vote,$p_c_description_vote,$p_c_last_votes_posted_vote,$p_c_last_post_date_vote);

								while(($row_prof_votes_cats =$stmt_prof_votes_cats->fetch()) ){
								
								//Get topics table info from database
								$stmt_prof_votes_tops = $con->prepare('SELECT id,category_id,topic_title,topic_date,topic_creator,topic_views FROM topics WHERE topic_creator = '.$post_creator_vote.'');
								$stmt_prof_votes_tops->execute();
								$stmt_prof_votes_tops->store_result();
								$stmt_prof_votes_tops->bind_result($t_tid_vote,$t_cid_vote,$t_title_vote,$t_date_vote,$t_tid_vote,$t_views_vote);
								$stmt_prof_votes_tops->fetch();

			////					
								if (!$p_c_description_vote){
									$p_c_description_vote = "MissingDesc";
								}
								if (!$p_c_title_vote){
									$p_c_title_vote = "MissingCat";
								}
								if (!$p_cid_vote){
									$p_cid_vote = "1";
								}

								if (!$p_content_vote){
									$p_content_vote = "MissingComment";
								}
								
								if (!$t_title_vote){
									$t_title_vote = "MissingTop";
								}
								if (!$t_tid_vote){
									$t_tid_vote = "1";
								}
								

								$stmt_prof_votes_sesh_rating_action = $con->prepare(
									'SELECT rating_action
									FROM rating_info 
									WHERE user_id = '.$seshid.' AND post_id='.$pid_vote.'');
								$stmt_prof_votes_sesh_rating_action->execute();
								$stmt_prof_votes_sesh_rating_action->store_result();
								$stmt_prof_votes_sesh_rating_action->bind_result($votes_sesh_rating_action_vote);

								$stmt_prof_votes_sesh_rating_action->fetch();

								$bgupvote="bg-secondary";
								$bgdownvote="bg-secondary";

								

								
								$stmt_updoot_count_votes = $con->prepare(
									"SELECT count(*) 
									FROM rating_info 
									WHERE rating_action = 'updoot' AND post_id='".$pid_vote."'"); 
								$stmt_updoot_count_votes->execute(); 
								$stmt_updoot_count_votes->bind_result($number_of_updoots_vote);
								$stmt_updoot_count_votes->fetch();

								$stmt_updoot_count_votes->close();

								$stmt_boop_count_votes = $con->prepare(
									"SELECT count(*) 
									FROM rating_info 
									WHERE rating_action = 'boop' AND post_id='".$pid_vote."'"); 
								$stmt_boop_count_votes->execute(); 
								$stmt_boop_count_votes->bind_result($number_of_boops_vote);
								$stmt_boop_count_votes->fetch();
								$stmt_boop_count_votes->close();

								$bgvotetallyvote="btn-vote";
								$bgvoteytallyvote="btn-dark";
								$bgvoteyiconvote="fas fa-balance-scale";


								if(($votes_sesh_rating_action_vote == 'updoot')){
									$bgupvote='btn-dope';
									$bgdownvote="btn-vote-nope";
								}
								if(($votes_sesh_rating_action_vote == 'boop')){
									$bgupvote='btn-vote-dope';
									$bgdownvote="btn-nope ";
								}

								if(($votes_sesh_rating_action_vote != 'boop') && ($votes_sesh_rating_action_vote != 'updoot')){
									$bgupvote='btn-vote-dope';
									$bgdownvote="btn-vote-nope";
								}
								

								$total_votes_vote = $number_of_boops_vote + $number_of_updoots_vote;

								if($number_of_boops_vote != $number_of_updoots_vote ){
									$total_updoot_perc_vote =  round(($number_of_updoots_vote / $total_votes_vote) * 100,2);
									$total_boop_perc_vote =  round(($number_of_boops_vote / $total_votes_vote)* 100, 2);
								}
								
								
								

								
									if($number_of_boops_vote > $number_of_updoots_vote){
										$bgvotetallyvote='btn-nope';
										$bgvoteytallyvote='bg-danger';
										$bgvoteyiconvote='fas fa-balance-scale-right';
										$winpercvote= $total_boop_perc_vote;
										}
								
									if($number_of_boops_vote < $number_of_updoots_vote){
										$bgvotetallyvote='btn-dope';
										$bgvoteytallyvote='bg-success';
										$bgvoteyiconvote='fas fa-balance-scale-left';
										$winpercvote= $total_updoot_perc_vote;
										
									}
								
									if($number_of_boops_vote == $number_of_updoots_vote ){
										$winpercvote= 50;
										$bgvotetallyvote='btn-vote';
										$bgvoteytallyvote='bg-dark';
										$bgvoteyiconvote='fas fa-balance-scale';
										if(($number_of_boops_vote == 0)||($number_of_boops_vote == 0)){
											$bgupvote='btn-vote-dope';
											$bgdownvote="btn-vote-nope";
										}
	
									}

								
			////						

								
								


								//posts content HTML		
								$prof_votes .= "
									<div class='row'>
										<div class='col'>
											<a href='view_topic.php?cid=".$t_cid_vote."&tid=".$t_tid_vote."'>
												<button class='btn btn-dark text-white'>
													<p class='btn ".$bgvotetallyvote."'><span ><i class='far fa-comments text-white'></i> | ".$p_content_vote."</span></p>
												</button>
											</a>
										</div>
									</div>
									<div class='row'>
										<div class='col'></div>
										<div class='col'>
											<form action='update_updoots.php' method='post'>
												<input type='hidden' name='rating_action' value='updoot'/>
												<input type='hidden' name='tid' value='".$pid_vote."'/>
												<button class='btn ".$bgupvote." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
													<i class='fas fa-angle-up'style='font-size:.5vw;'></i>
													| ".$number_of_updoots_vote."
												</button>
											</form>
										</div>
										<div class='col'>
											<span class='badge ".$bgvoteytallyvote."' >
												<i class='".$bgvoteyiconvote."'style='font-size:.5vw;'></i>
												| ".$winpercvote."
											</span>
											
										</div>
										<div class='col'>
											<form action='update_updoots.php' method='post'>
												<input type='hidden' name='rating_action' value='boop'/>
												<input type='hidden' name='tid' value='".$pid_vote."'/>
												<button class='btn ".$bgdownvote." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
													<i class='fas fa-angle-down'style='font-size:.5vw;'></i>
													| ".$number_of_boops_vote."
												</button>
											</form>

										</div>
										<div class='col'></div>
									</div>
									<div class='row'>	
										<div class='col'>		
												<span class='post_info text-white' style='font-size:.5vw;' >
													<i class='fas fa-edit'style='font-size:.5vw;'></i>
													by: 
													<a href='profile.php?uid=".$post_creator_vote."'>
													<span class='badge bg-info' >
														<i class='fas fa-user-circle'style='font-size:.5vw;'></i>
														| ".$post_username_vote."
													</span>
													</a>
													to 
													<a href='view_category.php?cid=".$t_cid_vote."'>
													<span class='badge bg-primary'>
														<i class='fas fa-book-open'style='font-size:.5vw;'></i>
														| ".substr($t_title_vote, 0, 40)."
													</span>
													</a>
													- ".$p_date_vote."
												</span>
											
										</div>

									</div>
									
								<br />";
								
								
								
								}
								
								
								$stmt_prof_votes_tops->fetch();
								
								$stmt_prof_votes_tops->close();

								}
								$stmt_prof_votes_cats->fetch();
								$stmt_prof_votes_cats->close();
								
								



								$stmt_prof_votes_sesh_rating_action->close();
							
							
						}
						//if the user hasn't created any comments yet
						
						$stmt_prof_posts_votes->fetch();
						$stmt_prof_posts_votes->close();
						}
					
							$prof_votes .= "</div>";
							
							echo $prof_votes;
					
					}else{	
							echo"<div class='row'>
							<div class='col'><h2 class='text-center text-white'><a href='profile.php?uid=".$prof_id."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$prof_username."</span></a> hasn't voted on a comment yet</h2></div>	
							</div>";
							echo $prof_votes;
						}
						$stmt_prof_votes->fetch();
						$stmt_prof_votes->close();
					?>
</div>





<div id="notvoted" class="container tab-pane active bg-secondary">
	<?php
		
		$notvoted_new ="";	








			$sql77 = "SELECT * FROM posts ORDER BY post_date DESC ";
			$res77 = mysqli_query($con, $sql77) or die(mysqli_error());

			if (mysqli_num_rows($res77) > 0){
				
				

				//POSTS Header HTML
				$notvoted_new .= "
				<div class='text-center'width='100% container' style='border-collapse:collapse;'>
					<div class='row text-center'>
						<div class='col'>	
							<h2 class='text-white'>
								<span class='badge bg-danger'>
									<i class='fas fa-biohazard'></i>
									| Not Voted Yet
								</span>
								by:
								<span class='badge bg-dark'>
									<i class='fas fa-user-circle'></i>
									| ".$prof_username."
								</span>
							</h2>
						</div>
					</div>
				";








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
							$post_creator = $row39['post_creator'];
							
							$stmt44 = $con->prepare('SELECT username,id FROM users WHERE id = ?');
							// In this case we can use the account ID to get the account info.
							$stmt44->bind_param('i', $post_creator);
							$stmt44->execute();
							$stmt44->bind_result($post_creator_username,$user_id);
							$stmt44->fetch();
							$stmt44->close();

							if((!$post_creator_username)){
								$post_creator_username ='MissingNo';
							}

			
							$sql40 = "SELECT * FROM topics WHERE  id='".$tid."'";
							$res40 = mysqli_query($con, $sql40) or die(mysqli_error());
			
							
							while($row40 = mysqli_fetch_assoc($res40)){
								
								$topic_date = $row40['topic_date'];
								$topic_title = $row40['topic_title'];
								$topic_creator = $row40['topic_creator'];
								
								$stmt4 = $con->prepare('SELECT username,id FROM users WHERE id = ?');
								// In this case we can use the account ID to get the account info.
								$stmt4->bind_param('i', $topic_creator);
								$stmt4->execute();
								$stmt4->bind_result($tovote_username,$user_id);
								$stmt4->fetch();
								$stmt4->close();

								if((!$tovote_username)){
									$tovote_username ='MissingNo';
								}




//Get topics table info from database
$stmt_prof_votes_tops_nv = $con->prepare('SELECT id,category_id,topic_title,topic_date,topic_creator,topic_views FROM topics WHERE topic_creator = '.$topic_creator.'');
$stmt_prof_votes_tops_nv->execute();
$stmt_prof_votes_tops_nv->store_result();
$stmt_prof_votes_tops_nv->bind_result($t_tid_vote_not,$t_cid_vote_not,$t_title_vote_not,$t_date_vote_not,$t_tid_vote_not,$t_views_vote_not);
$stmt_prof_votes_tops_nv->fetch();

////					


if (!$content){
	$content = "MissingComment";
}
if (!$cid){
	$cid = "1";
}


if (!$t_title_vote_not){
	$t_title_vote_not = "MissingTop";
}
if (!$t_tid_vote_not){
	$t_tid_vote_not = "1";
}


$stmt_prof_votes_sesh_rating_action_not = $con->prepare(
	'SELECT rating_action
	FROM rating_info 
	WHERE user_id = '.$seshid.' AND post_id='.$pid.'');
$stmt_prof_votes_sesh_rating_action_not->execute();
$stmt_prof_votes_sesh_rating_action_not->store_result();
$stmt_prof_votes_sesh_rating_action_not->bind_result($votes_sesh_rating_action_vote_not);

$stmt_prof_votes_sesh_rating_action_not->fetch();

$bgupvotenot="bg-secondary";
$bgdownvotenot="bg-secondary";




$stmt_updoot_count_votes_not = $con->prepare(
	"SELECT count(*) 
	FROM rating_info 
	WHERE rating_action = 'updoot' AND post_id='".$pid."'"); 
$stmt_updoot_count_votes_not->execute(); 
$stmt_updoot_count_votes_not->bind_result($number_of_updoots_vote_not);
$stmt_updoot_count_votes_not->fetch();

$stmt_updoot_count_votes_not->close();

$stmt_boop_count_votes_not = $con->prepare(
	"SELECT count(*) 
	FROM rating_info 
	WHERE rating_action = 'boop' AND post_id='".$pid."'"); 
$stmt_boop_count_votes_not->execute(); 
$stmt_boop_count_votes_not->bind_result($number_of_boops_vote_not);
$stmt_boop_count_votes_not->fetch();
$stmt_boop_count_votes_not->close();

$bgvotetallyvote="btn-vote";
$bgvoteytallyvote="btn-dark";
$bgvoteyiconvote="fas fa-balance-scale";


if(($votes_sesh_rating_action_vote_not == 'updoot')){
	$bgupvotenot='btn-dope';
	$bgdownvotenot="btn-vote-nope";
}
if(($votes_sesh_rating_action_vote_not == 'boop')){
	$bgupvotenot='btn-vote-dope';
	$bgdownvotenot="btn-nope ";
}

if(($votes_sesh_rating_action_vote_not != 'boop') && ($votes_sesh_rating_action_vote_not != 'updoot')){
	$bgupvotenot='btn-vote-dope';
	$bgdownvotenot="btn-vote-nope";
}


$total_votes_vote_not = $number_of_boops_vote_not + $number_of_updoots_vote_not;

if($number_of_boops_vote_not != $number_of_updoots_vote_not ){
	$total_updoot_perc_vote_not =  round(($number_of_updoots_vote_not / $total_votes_vote_not) * 100,2);
	$total_boop_perc_vote_not =  round(($number_of_boops_vote_not / $total_votes_vote_not)* 100, 2);
}





	if($number_of_boops_vote_not > $number_of_updoots_vote_not){
		$bgvotetallyvotenot='btn-nope';
		$bgvoteytallyvotenot='bg-danger';
		$bgvoteyiconvotenot='fas fa-balance-scale-right';
		$winpercvotenot= $total_boop_perc_vote_not;
		}

	if($number_of_boops_vote_not < $number_of_updoots_vote_not){
		$bgvotetallyvotenot='btn-dope';
		$bgvoteytallyvotenot='bg-success';
		$bgvoteyiconvotenot='fas fa-balance-scale-left';
		$winpercvotenot= $total_updoot_perc_vote_not;
		
	}

	if($number_of_boops_vote_not == $number_of_updoots_vote_not ){
		$winpercvotenot= 50;
		$bgvotetallyvotenot='btn-vote';
		$bgvoteytallyvotenot='bg-dark';
		$bgvoteyiconvotenot='fas fa-balance-scale';
		if(($number_of_boops_vote_not == 0)||($number_of_boops_vote_not == 0)){
			$bgupvotenot='btn-vote-dope';
			$bgdownvotenot="btn-vote-nope";
		}

	}

								
							













								
								//posts content HTML		
								$notvoted_new .= "
									<div class='row'>
										<div class='col'>
											<a href='view_topic.php?cid=".$cid."&tid=".$tid."'>
												<button class='btn btn-dark text-white'>
													<p class='btn ".$bgvotetallyvotenot."'><span ><i class='far fa-comments text-white'></i> | ".$content."</span></p>
												</button>
											</a>
										</div>
									</div>
									<div class='row'>
										<div class='col'></div>
										<div class='col'>
											<form action='update_updoots.php' method='post'>
												<input type='hidden' name='rating_action' value='updoot'/>
												<input type='hidden' name='tid' value='".$pid."'/>
												<button class='btn ".$bgupvotenot." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
													<i class='fas fa-angle-up'style='font-size:.5vw;'></i>
													| ".$number_of_updoots_vote_not."
												</button>
											</form>
										</div>
										<div class='col'>
											<span class='badge ".$bgvoteytallyvotenot."' >
												<i class='".$bgvoteyiconvotenot."'style='font-size:.5vw;'></i>
												| ".$winpercvotenot."
											</span>
											
										</div>
										<div class='col'>
											<form action='update_updoots.php' method='post'>
												<input type='hidden' name='rating_action' value='boop'/>
												<input type='hidden' name='tid' value='".$pid."'/>
												<button class='btn ".$bgdownvotenot." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
													<i class='fas fa-angle-down'style='font-size:.5vw;'></i>
													| ".$number_of_boops_vote_not."
												</button>
											</form>

										</div>
										<div class='col'></div>
									</div>
									<div class='row'>	
										<div class='col'>		
												<span class='post_info text-white' style='font-size:.5vw;' >
													<i class='fas fa-edit'style='font-size:.5vw;'></i>
													by: 
													<a href='profile.php?uid=".$post_creator."'>
													<span class='badge bg-info' >
														<i class='fas fa-user-circle'style='font-size:.5vw;'></i>
														| ".$post_creator_username."
													</span>
													</a>
													to 
													<a href='view_category.php?cid=".$cid."'>
													<span class='badge bg-primary'>
														<i class='fas fa-book-open'style='font-size:.5vw;'></i>
														| ".substr($topic_title, 0, 40)."
													</span>
													</a>
													- ".$date."
												</span>
											
										</div>

									</div>
									
								<br />";

								
							}
							
							
							
						
							
							
						}

					}else{
						$rating_action = $row33[0];
					}
					
						


					

					
					
					
					

					
					

				}
				

				$notvoted_new .= "</div>";
				echo $notvoted_new;
			}
			else{
							
				echo"<div class='row'>
						<div class='col'>
							<h2 class='text-center text-white'>
								There are no comments for <a href='profile.php?uid=".$prof_id."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$prof_username."</span></a> to vote on
							</h2>
						</div>	
					</div>";
					$notvoted_new .= "</div>";



							
			}

		?>
	</div>








	
	
			<div id="messagefrom" class="container tab-pane ">
		<?php
			$uid = $_GET['uid'];
			$messagestuffer = "";

			$sql57 = "SELECT * FROM dm WHERE from_id=".$uid." ORDER BY message_date DESC ";
			$res57 = mysqli_query($con, $sql57) or die(mysqli_error());

			if (mysqli_num_rows($res57) > 0){
				
				$messagestuffer .= "<table width='100%' style='border-collapse:collapse;'>";
				$messagestuffer .= "<h2>Messages</h2>";

				
				$messagestuffer .= "<tr style='background-color:#dddddd;'>
								<td ><span class='badge bg-dark'><i class='fas fa-user-circle'></i> | ".$prof_username."</span>'s Messages</td>
								<td width='65'>To</td>
								<td width='100'>On</td>
							</tr>";
				$messagestuffer .= "<tr><td colspan='6' ><hr /></td></tr>";

				while($row57 = mysqli_fetch_assoc($res57)){
							$message_content = $row57['message_content'];
							$message_date = $row57['message_date'];
							$from_id = $row57['from_id'];
							$to_id = $row57['to_id'];
							
							$messagestuffer .= "<tr>
							<td >".$message_content."</td>";


							$stmt3 = $con->prepare('SELECT username,id FROM users WHERE id = ?');
							// In this case we can use the account ID to get the account info.
							$stmt3->bind_param('i', $to_id);
							$stmt3->execute();
							$stmt3->bind_result($to_username,$user_id);
							$stmt3->fetch();
							$stmt3->close();

							if((!$to_username)){
								$to_username ='MissingNo';
							}
							
							
							$messagestuffer .= "
							<td><a href='profile.php?uid=".$to_id."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$to_username."</span></a>
							</td>
							<td>".$message_date."</td>
							</tr>";
							$messagestuffer .= "<tr><td colspan='6' ><hr/></td></tr>";
							

					
					
						


					

					
					
				}
					

					
					

				
				
				$messagestuffer .= "</table>";
				echo $messagestuffer;
			}
			else{
							
				
				echo"<h2 class='text-center text-white'><a href='profile.php?uid=".$prof_id."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$prof_username."</span></a> hasn't messaged anyone yet</h2>";
			}

		?>
			</div>
	
			<div id="messageto" class="container tab-pane fade">
		<?php
			$uid = $_GET['uid'];
			$messagestufferto = "";

			$sql52 = "SELECT * FROM dm WHERE to_id=".$uid." ORDER BY message_date DESC ";
			$res52 = mysqli_query($con, $sql52) or die(mysqli_error());

			if (mysqli_num_rows($res52) > 0){
				
				$messagestufferto .= "<table width='100%' style='border-collapse:collapse;'>";
				$messagestufferto .= "<h2>Messages</h2>";

				
				$messagestufferto .= "<tr style='background-color:#dddddd;'>
								<td ><span class='badge bg-dark'><i class='fas fa-user-circle'></i> | ".$prof_username."</span>'s Messages</td>
								<td width='65'>From</td>
								<td width='100'>On</td>
							</tr>";
				$messagestufferto .= "<tr><td colspan='6' ><hr /></td></tr>";

				while($row52 = mysqli_fetch_assoc($res52)){
							$message_content = $row52['message_content'];
							$message_date = $row52['message_date'];
							$from_id = $row52['from_id'];
							$to_id = $row52['to_id'];


							$messagestufferto .= "<tr>
							<td >".$message_content."</td>";


							$stmt2 = $con->prepare('SELECT username,id FROM users WHERE id = ?');
							// In this case we can use the account ID to get the account info.
							$stmt2->bind_param('i', $from_id);
							$stmt2->execute();
							$stmt2->bind_result($from_username,$user_id);
							$stmt2->fetch();
							$stmt2->close();

							if((!$from_username)){
								$from_username ='MissingNo';
							}
							
							
							$messagestufferto .= "
							<td><a href='profile.php?uid=".$from_id."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$from_username."</span></a>
							</td>
							<td>".$message_date."</td>
							</tr>";
							$messagestufferto .= "<tr><td colspan='6' ><hr/></td></tr>";
							

					
					
						


					

					
					
				}
					

					
					

				
				
				$messagestufferto .= "</table>";
				echo $messagestufferto;
			}
			else{
							
				
				echo"<h2 class='text-center text-white'><a href='profile.php?uid=".$prof_id."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$prof_username."</span></a> hasn't received a messaged from anyone yet</h2>";
			}

		?>
			</div>
		
	

</div>




			
		</div>








<!-- The Modal -->
<div class="modal " id="messageModal">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary">

      <!-- Modal Header -->
      <div class="modal-header text-white">
        <h4 class="modal-title">Chat with <span class='badge bg-info'><i class='fas fa-user-circle'></i> | <?=$prof_username?></span> and <span class='badge bg-success'><i class='fas fa-user-circle'></i> | <?=$seshusername?></span></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body text-center" >
		<div class="row">
		
		
		
		
		
			
		<?php 
		

		$to_id = "$uid";
		$from_id = "$seshid";

		echo"<iframe src='displaychat.php?to_id=".$to_id."&from_id=".$from_id."' width='100%' height='500' scrolling='yes'>
		
		</iframe>";
		
		?>
			
			
			
		
		</div>
		<div class="row">
			<p></p>
		</div>
		<div class="row">
		<form action='create_message_parse.php' method='post' autocomplete='off'>
				<input type='hidden' name='to_id' value='<?php echo $to_id; ?>'/>
				<input type='hidden' name='from_id' value='<?php echo $from_id; ?>'/>
				
				<tr>
		  		<td><textarea class='form-control' rows='2' id='message_content' name='message_content' type='text'></textarea></td>

				<td width='180'><button class='btn btn-primary' style='width: 100%;' type='submit' name='dm_submit' >Message</button></td>
				</tr>
		</form>
		  
	
		
		 



		

      </div></div>

    </div>
  </div>
</div>

















<!-- The Modal -->
<div class="modal " id="chatModal">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary">

      <!-- Modal Header -->
      <div class="modal-header text-white">
        
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body text-center" >
		
		<div class="row">	
			<div class="col-2">			
					
				<nav class="navbar bg-light">
					<?php 
						$stmt_dms = $con->prepare('SELECT to_id,from_id,message_content,message_date FROM dm WHERE from_id='.$seshid.' OR to_id = '.$seshid.' ORDER BY message_date ASC');
						$stmt_dms->execute();
						$stmt_dms->store_result();
						$stmt_dms->bind_result($to_id,$from_id,$message_content,$message_date);
						
						$dms_w_prof_user = "";
					
						if ($stmt_dms->num_rows > 0) {
							while(($row_dms = $stmt_dms->fetch()) ){

								//if($from_id==$to_id){
									//to user from profile
									//if($from_id==$seshid){
								$stmt_users_who_dms_w_prof_user = $con->prepare('SELECT id,username FROM users WHERE id='.$from_id.' OR id='.$to_id.'  ORDER BY username ASC LIMIT 1');
								$stmt_users_who_dms_w_prof_user->execute();
													
								$stmt_users_who_dms_w_prof_user->store_result();
								$stmt_users_who_dms_w_prof_user->bind_result($chat_uid,$chat_username);
										
								$users_who_dms_w_prof_user = "";
										
								if ($stmt_users_who_dms_w_prof_user->num_rows > 0) {
									while(($row_users_who_dms_w_prof_user = $stmt_users_who_dms_w_prof_user->fetch()) ){

										if($from_id!=$to_id){
											
											$users_who_dms_w_prof_user .= "
						<form method='post' action= >
							<input type='hidden' name='chat_uid' value='".$chat_uid."'/>
							<button class='btn bg-info ' type='submit' name='prof_submit' id='prof_submit' value='Up' ><i class='fas fa-user-circle'></i> | ".$chat_username."</span></button>
						</form>";

										}


									}
									echo $users_who_dms_w_prof_user;
									echo "";
								}else {
									echo "<p class='text-center text-white bg-secondary'>There are no posts available! Make a post and let your voice be heard!</p>";
								}
								$stmt_users_who_dms_w_prof_user->fetch();
								$stmt_users_who_dms_w_prof_user->close();
								//}
								//}
							}
							echo $dms_w_prof_user;
							echo "";
						}else {
							echo "<p class='text-center text-white bg-secondary'>No chats started</p>";
						}
						$stmt_dms->fetch();
						$stmt_dms->close();
					
						echo"
				</nav>
			</div>";

						if(!isset($_POST['prof_submit'])){
						
							echo"

			<div class='col'>	
				<iframe src='displaychat.php?to_id=".$seshid."&from_id=".$seshid."' width='100%' height='500' scrolling='yes'></iframe>";
					
						}else{
							mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
							$to_idnew = $_POST['chat_uid'];
							echo"
			<div class='col'>	
				<iframe src='displaychat.php?to_id=".$to_idnew."&from_id=".$seshid."' width='100%' height='500' scrolling='yes'></iframe>";
						}
					?>
			
			</div>
		</div>
			
			
			
		
		<div class="row">
			<form action='create_message_parse.php' method='post' autocomplete='off'>
					<input type='hidden' name='to_id' value='<?php echo $to_id; ?>'/>
					<input type='hidden' name='from_id' value='<?php echo $from_id; ?>'/>
					
					<tr>
					<td><textarea class='form-control' rows='2' id='message_content' name='message_content' type='text'></textarea></td>

					<td width='180'><button class='btn btn-primary' style='width: 100%;' type='submit' name='dm_submit' >Message</button></td>
					</tr>
			</form>
		</div>
	


    </div>
  </div>
</div>






			
	</body>
</html>