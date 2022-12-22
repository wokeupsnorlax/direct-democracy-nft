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
?>

<!DOCTYPE html>
<html>
	<head><meta charset="utf-8"><title>Profile Page</title>
  		<link rel="icon" type="image/x-icon" href="img/favicon.ico">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		
		<link rel="stylesheet" href="fontawesome-free-5.15.4-web/css/all.css">

		<link href="style.css" rel="stylesheet" type="text/css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
		  <link rel="stylesheet" href="style.scss" />
		  
		<style>
.btn-vote {
    --bs-btn-color: #fff;
  --bs-btn-bg: #212529;
  --bs-btn-border-color: #212529;
  --bs-btn-hover-color: #fff;
  --bs-btn-hover-bg: #424649;
  --bs-btn-hover-border-color: #373b3e;
  --bs-btn-focus-shadow-rgb: 66, 70, 73;
  --bs-btn-active-color: #fff;
  --bs-btn-active-bg: #4d5154;
  --bs-btn-active-border-color: #373b3e;
  --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
  --bs-btn-disabled-color: #fff;
  --bs-btn-disabled-bg: #212529;
  --bs-btn-disabled-border-color: #212529;
}
.btn-nope{
  --bs-btn-color: #fff;
  --bs-btn-bg: #dc3545;
  --bs-btn-border-color: #dc3545;
  --bs-btn-hover-color: #fff;
  --bs-btn-hover-bg: #bb2d3b;
  --bs-btn-hover-border-color: #b02a37;
  --bs-btn-focus-shadow-rgb: 225, 83, 97;
  --bs-btn-active-color: #fff;
  --bs-btn-active-bg: #b02a37;
  --bs-btn-active-border-color: #a52834;
  --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
  --bs-btn-disabled-color: #fff;
  --bs-btn-disabled-bg: #dc3545;
  --bs-btn-disabled-border-color: #dc3545;
}


.btn-vote-nope {
	--bs-btn-color: #fff;
  --bs-btn-bg: #212529;
  --bs-btn-border-color: #212529;
  --bs-btn-hover-color: #fff;
  --bs-btn-hover-bg: #bb2d3b;
  --bs-btn-hover-border-color: #b02a37;
  --bs-btn-focus-shadow-rgb: 66, 70, 73;
  --bs-btn-active-color: #fff;
  --bs-btn-active-bg: #4d5154;
  --bs-btn-active-border-color: #373b3e;
  --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
  --bs-btn-disabled-color: #fff;
  --bs-btn-disabled-bg: #212529;
  --bs-btn-disabled-border-color: #212529;
}




.btn-dope{
  --bs-btn-color: #fff;
  --bs-btn-bg: #198754;
  --bs-btn-border-color: #198754;
  --bs-btn-hover-color: #fff;
  --bs-btn-hover-bg: #157347;
  --bs-btn-hover-border-color: #146c43;
  --bs-btn-focus-shadow-rgb: 60, 153, 110;
  --bs-btn-active-color: #fff;
  --bs-btn-active-bg: #146c43;
  --bs-btn-active-border-color: #13653f;
  --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
  --bs-btn-disabled-color: #fff;
  --bs-btn-disabled-bg: #198754;
  --bs-btn-disabled-border-color: #198754;
}

.btn-vote-dope {
	--bs-btn-color: #fff;
  --bs-btn-bg: #212529;
  --bs-btn-border-color: #212529;
  --bs-btn-hover-color: #fff;
  --bs-btn-hover-bg: #157347;
  --bs-btn-hover-border-color: #146c43;
  --bs-btn-focus-shadow-rgb: 66, 70, 73;
  --bs-btn-active-color: #fff;
  --bs-btn-active-bg: #4d5154;
  --bs-btn-active-border-color: #373b3e;
  --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
  --bs-btn-disabled-color: #fff;
  --bs-btn-disabled-bg: #212529;
  --bs-btn-disabled-border-color: #212529;
}



		</style>
	</head>
	<body class="loggedin bg-dark">
		<nav class="navtop">
			<div>
				<h1>
					<a href="home.php"><button type="button" class="btn btn-primary" ><i class='far fa-bookmark'></i> | Subs</button></a>
				</h1>
				<a><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-circle"></i><?=$seshusername?></button></a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content container ">
			<?php

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

				echo "<div class='row bg-dark'><h2 class=' text-white'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | $prof_username</span>'s Profile</h2>";

				echo "<button class='btn btn-info text-white' data-bs-toggle='modal' data-bs-target='#messageModal'><i class='far fa-comment-alt'></i> | Send Message</button></div>";
			?>

			<div class="tab-content bg-secondary  ">
				<!-- Nav tabs -->
				<ul class="nav nav-pills " role="tablist">
					<li class="nav-item  "><a class="nav-link" href="home.php"><button class="btn btn-success">Home</button></a></li>
					<li class="nav-item "><a class="nav-link " data-bs-toggle="pill" href="#topics"><button class="btn btn-primary text-white"><i class='fas fa-book-open'></i> | Topics</button></a></li>
					<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#posts"><button class="btn btn-primary text-white"><i class='far fa-comments'></i> | Comments</button></a></li>
					
					<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#oldposts"><button class="btn btn-primary text-white"><i class='far fa-comments'></i> | OLD Comments</button></a></li>

					<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#votes"><button class="btn btn-success"><i class='fas fa-balance-scale'></i> | Voted</button></a></li>
					<li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#oldvotes"><button class="btn btn-success"><i class='fas fa-balance-scale'></i> | OLD Voted</button></a></li>
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

								$stmt_prof_topics_cats->fetch();
								$stmt_prof_topics_cats->close();
							}
							$prof_topics .= "</div>";
							
							echo $prof_topics;
						}
						//if the user hasn't created any topics yet
						else{	
							echo"<div class='row'>
							<div class='col'><h2 class='text-center'>You haven't created a topic yet</h2></div>	
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
						
						//Get posts table info from database
						$stmt_prof_posts = $con->prepare('SELECT id,category_id,post_content,post_date,topic_id,post_creator FROM posts WHERE post_creator = '.$prof_id.' ORDER BY post_date DESC');
						$stmt_prof_posts->execute();
						$stmt_prof_posts->store_result();
						$stmt_prof_posts->bind_result($pid,$cid,$p_content,$p_date,$p_tid,$post_creator );

						//if there are posts created by this user then show this stuff
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

							while(($row_prof_posts = $stmt_prof_posts->fetch()) ){
								
								
								$stmt_prof_posts_cats = $con->prepare(
									'SELECT id,category_title,category_description,last_user_posted,last_post_date 
									FROM catergories 
									WHERE id = '.$cid.'');
								$stmt_prof_posts_cats->execute();
								$stmt_prof_posts_cats->store_result();
								$stmt_prof_posts_cats->bind_result($p_cid,$p_c_title,$p_c_description,$p_c_last_user_posted,$p_c_last_post_date);

								while(($row_prof_posts_cats =$stmt_prof_posts_cats->fetch()) ){
								
								//Get topics table info from database
								$stmt_prof_posts_tops = $con->prepare('SELECT category_id,topic_title,topic_date,topic_creator,topic_views FROM topics WHERE topic_creator = '.$cid.'');
								$stmt_prof_posts_tops->execute();
								$stmt_prof_posts_tops->store_result();
								$stmt_prof_posts_tops->bind_result($t_cid,$t_title,$t_date,$t_tid,$t_views);
								$stmt_prof_posts_tops->fetch();

								
								if (!$p_c_description){
									$t_c_description = "MissingDesc";
								}
								if (!$p_c_title){
									$t_c_title = "MissingCat";
								}
								if (!$p_cid){
									$t_cid = "1";
								}

								
								if (!$t_title){
									$t_c_title = "MissingTop";
								}
								if (!$t_tid){
									$t_cid = "1";
								}
								

								$stmt_prof_posts_sesh_rating_action = $con->prepare(
									'SELECT rating_action
									FROM rating_info 
									WHERE user_id = '.$seshid.' AND post_id='.$pid.'');
								$stmt_prof_posts_sesh_rating_action->execute();
								$stmt_prof_posts_sesh_rating_action->store_result();
								$stmt_prof_posts_sesh_rating_action->bind_result($posts_sesh_rating_action);

								$stmt_prof_posts_sesh_rating_action->fetch();

								$bgup="bg-secondary";
								$bgdown="bg-secondary";

								

								
								$stmt_updoot_count = $con->prepare(
									"SELECT count(*) 
									FROM rating_info 
									WHERE rating_action = 'updoot' AND post_id='".$pid."'"); 
								$stmt_updoot_count->execute(); 
								$stmt_updoot_count->bind_result($number_of_updoots);
								$stmt_updoot_count->fetch();

								$stmt_updoot_count->close();

								$stmt_boop_count = $con->prepare(
									"SELECT count(*) 
									FROM rating_info 
									WHERE rating_action = 'boop' AND post_id='".$pid."'"); 
								$stmt_boop_count->execute(); 
								$stmt_boop_count->bind_result($number_of_boops);
								$stmt_boop_count->fetch();
								$stmt_boop_count->close();

								$bgvotetally="btn-vote";
								$bgvoteytally="btn-dark";
								$bgvoteyicon="fas fa-balance-scale";


								if(($posts_sesh_rating_action == 'updoot')){
									$bgup='btn-dope';
									$bgdown="btn-vote-nope";
								}
								if(($posts_sesh_rating_action == 'boop')){
									$bgup='btn-vote-dope';
									$bgdown="btn-nope ";
								}

								if(($posts_sesh_rating_action != 'boop') && ($posts_sesh_rating_action != 'updoot')){
									$bgup='btn-vote-dope';
									$bgdown="btn-vote-nope";
								}
								

								$total_votes = $number_of_boops + $number_of_updoots;

								if($number_of_boops != $number_of_updoots ){
									$total_updoot_perc =  round(($number_of_updoots / $total_votes) * 100,2);
									$total_boop_perc =  round(($number_of_boops / $total_votes)* 100, 2);
								}
								
								
								

								
									if($number_of_boops > $number_of_updoots){
										$bgvotetally='btn-nope';
										$bgvoteytally='bg-danger';
										$bgvoteyicon='fas fa-balance-scale-right';
										$winperc= $total_boop_perc;
										}
								
									if($number_of_boops < $number_of_updoots){
										$bgvotetally='btn-dope';
										$bgvoteytally='bg-success';
										$bgvoteyicon='fas fa-balance-scale-left';
										$winperc= $total_updoot_perc;
										
									}
								
									if($number_of_boops == $number_of_updoots ){
										$winperc= 50;
										$bgvotetally='btn-vote';
										$bgvoteytally='bg-dark';
										$bgvoteyicon='fas fa-balance-scale';
										if(($number_of_boops == 0)||($number_of_boops == 0)){
											$bgup='btn-vote-dope';
											$bgdown="btn-vote-nope";
										}
	
									}

								


								
								


								//posts content HTML		
								$prof_posts .= "
									<div class='row'>
										<div class='col'>
											<a href='view_topic.php?cid=".$t_cid."&tid=".$p_tid."'>
												<button class='btn btn-dark text-white'>
													<p class='btn ".$bgvotetally."'><span ><i class='far fa-comments text-white'></i> | ".$p_content."</span></p>
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
												<button class='btn ".$bgup." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
													<i class='fas fa-angle-up'style='font-size:.5vw;'></i>
													| ".$number_of_updoots."
												</button>
											</form>
										</div>
										<div class='col'>
											<span class='badge ".$bgvoteytally."' >
												<i class='".$bgvoteyicon."'style='font-size:.5vw;'></i>
												| ".$winperc."
											</span>
											
										</div>
										<div class='col'>
											<form action='update_updoots.php' method='post'>
												<input type='hidden' name='rating_action' value='boop'/>
												<input type='hidden' name='tid' value='".$pid."'/>
												<button class='btn ".$bgdown." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
													<i class='fas fa-angle-down'style='font-size:.5vw;'></i>
													| ".$number_of_boops."
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
													<a href='view_category.php?cid=".$t_cid."'>
													<span class='badge bg-primary'>
														<i class='fas fa-book-open'style='font-size:.5vw;'></i>
														| ".substr($t_title, 0, 40)."
													</span>
													</a>
													- ".$p_date."
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
							<div class='col'><h2 class='text-center'>You haven't created a topic yet</h2></div>	
							</div>";
							echo $prof_posts;
						}
						$stmt_prof_posts->fetch();
						$stmt_prof_posts->close();
					?>
				</div>






				<div id="votes" class="container tab-pane fade bg-secondary">
					<?php
						$prof_posts ="";
						
						//Get posts table info from database
						$stmt_prof_posts = $con->prepare('SELECT id,category_id,post_content,post_date,topic_id,post_creator FROM posts WHERE post_creator = '.$prof_id.' ORDER BY post_date DESC');
						$stmt_prof_posts->execute();
						$stmt_prof_posts->store_result();
						$stmt_prof_posts->bind_result($pid,$cid,$p_content,$p_date,$p_tid,$post_creator );

						//if there are posts created by this user then show this stuff
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

							while(($row_prof_posts = $stmt_prof_posts->fetch()) ){
								
								
								$stmt_prof_posts_cats = $con->prepare(
									'SELECT id,category_title,category_description,last_user_posted,last_post_date 
									FROM catergories 
									WHERE id = '.$cid.'');
								$stmt_prof_posts_cats->execute();
								$stmt_prof_posts_cats->store_result();
								$stmt_prof_posts_cats->bind_result($p_cid,$p_c_title,$p_c_description,$p_c_last_user_posted,$p_c_last_post_date);

								while(($row_prof_posts_cats =$stmt_prof_posts_cats->fetch()) ){
								
								//Get topics table info from database
								$stmt_prof_posts_tops = $con->prepare('SELECT category_id,topic_title,topic_date,topic_creator,topic_views FROM topics WHERE topic_creator = '.$cid.'');
								$stmt_prof_posts_tops->execute();
								$stmt_prof_posts_tops->store_result();
								$stmt_prof_posts_tops->bind_result($t_cid,$t_title,$t_date,$t_tid,$t_views);
								$stmt_prof_posts_tops->fetch();

								
								if (!$p_c_description){
									$t_c_description = "MissingDesc";
								}
								if (!$p_c_title){
									$t_c_title = "MissingCat";
								}
								if (!$p_cid){
									$t_cid = "1";
								}

								
								if (!$t_title){
									$t_c_title = "MissingTop";
								}
								if (!$t_tid){
									$t_cid = "1";
								}
								

								$stmt_prof_posts_sesh_rating_action = $con->prepare(
									'SELECT rating_action
									FROM rating_info 
									WHERE user_id = '.$seshid.' AND post_id='.$pid.'');
								$stmt_prof_posts_sesh_rating_action->execute();
								$stmt_prof_posts_sesh_rating_action->store_result();
								$stmt_prof_posts_sesh_rating_action->bind_result($posts_sesh_rating_action);

								$stmt_prof_posts_sesh_rating_action->fetch();

								$bgup="bg-secondary";
								$bgdown="bg-secondary";

								

								
								$stmt_updoot_count = $con->prepare(
									"SELECT count(*) 
									FROM rating_info 
									WHERE rating_action = 'updoot' AND post_id='".$pid."'"); 
								$stmt_updoot_count->execute(); 
								$stmt_updoot_count->bind_result($number_of_updoots);
								$stmt_updoot_count->fetch();

								$stmt_updoot_count->close();

								$stmt_boop_count = $con->prepare(
									"SELECT count(*) 
									FROM rating_info 
									WHERE rating_action = 'boop' AND post_id='".$pid."'"); 
								$stmt_boop_count->execute(); 
								$stmt_boop_count->bind_result($number_of_boops);
								$stmt_boop_count->fetch();
								$stmt_boop_count->close();

								$bgvotetally="btn-vote";
								$bgvoteytally="btn-dark";
								$bgvoteyicon="fas fa-balance-scale";


								if(($posts_sesh_rating_action == 'updoot')){
									$bgup='btn-dope';
									$bgdown="btn-vote-nope";
								}
								if(($posts_sesh_rating_action == 'boop')){
									$bgup='btn-vote-dope';
									$bgdown="btn-nope ";
								}

								if(($posts_sesh_rating_action != 'boop') && ($posts_sesh_rating_action != 'updoot')){
									$bgup='btn-vote-dope';
									$bgdown="btn-vote-nope";
								}
								

								$total_votes = $number_of_boops + $number_of_updoots;

								if($number_of_boops != $number_of_updoots ){
									$total_updoot_perc =  round(($number_of_updoots / $total_votes) * 100,2);
									$total_boop_perc =  round(($number_of_boops / $total_votes)* 100, 2);
								}
								
								
								

								
									if($number_of_boops > $number_of_updoots){
										$bgvotetally='btn-nope';
										$bgvoteytally='bg-danger';
										$bgvoteyicon='fas fa-balance-scale-right';
										$winperc= $total_boop_perc;
										}
								
									if($number_of_boops < $number_of_updoots){
										$bgvotetally='btn-dope';
										$bgvoteytally='bg-success';
										$bgvoteyicon='fas fa-balance-scale-left';
										$winperc= $total_updoot_perc;
										
									}
								
									if($number_of_boops == $number_of_updoots ){
										$winperc= 50;
										$bgvotetally='btn-vote';
										$bgvoteytally='bg-dark';
										$bgvoteyicon='fas fa-balance-scale';
										if(($number_of_boops == 0)||($number_of_boops == 0)){
											$bgup='btn-vote-dope';
											$bgdown="btn-vote-nope";
										}
	
									}

								


								
								


								//posts content HTML		
								$prof_posts .= "
									<div class='row'>
										<div class='col'>
											<a href='view_topic.php?cid=".$t_cid."&tid=".$p_tid."'>
												<button class='btn btn-dark text-white'>
													<p class='btn ".$bgvotetally."'><span ><i class='far fa-comments text-white'></i> | ".$p_content."</span></p>
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
												<button class='btn ".$bgup." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
													<i class='fas fa-angle-up'style='font-size:.5vw;'></i>
													| ".$number_of_updoots."
												</button>
											</form>
										</div>
										<div class='col'>
											<span class='badge ".$bgvoteytally."' >
												<i class='".$bgvoteyicon."'style='font-size:.5vw;'></i>
												| ".$winperc."
											</span>
											
										</div>
										<div class='col'>
											<form action='update_updoots.php' method='post'>
												<input type='hidden' name='rating_action' value='boop'/>
												<input type='hidden' name='tid' value='".$pid."'/>
												<button class='btn ".$bgdown." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
													<i class='fas fa-angle-down'style='font-size:.5vw;'></i>
													| ".$number_of_boops."
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
													<a href='view_category.php?cid=".$t_cid."'>
													<span class='badge bg-primary'>
														<i class='fas fa-book-open'style='font-size:.5vw;'></i>
														| ".substr($t_title, 0, 40)."
													</span>
													</a>
													- ".$p_date."
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
							<div class='col'><h2 class='text-center'>You haven't created a topic yet</h2></div>	
							</div>";
							echo $prof_posts;
						}
						$stmt_prof_posts->fetch();
						$stmt_prof_posts->close();
					?>
				</div>













	<div id="oldposts" class="container tab-pane fade">
		
		
		
		
		
		
		<?php
			
			$posts ="";
					
			$sql12 = "SELECT * FROM posts WHERE post_creator='".$uid."' ORDER BY post_date DESC ";
			$res12 = mysqli_query($con, $sql12) or die(mysqli_error());

			

			if (mysqli_num_rows($res12) > 0){
				$posts .= "<table width='100%' style='border-collapse:collapse;' >";
				$posts .= "<h2 class='text-white'>Posts</h2>";
				$posts .= "<tr style='background-color:#dddddd;'>
				<td ><span class='badge bg-primary'><i class='far fa-comments'></i> | Comments</span> <span class='badge bg-dark'><i class='fas fa-user-circle'></i> | ".$prof_username."</span> Created</td>
				<td width='200'><span class='badge bg-primary'><i class='fas fa-book-open'></i> | Topics</span> <span class='badge bg-dark'><i class='fas fa-user-circle'></i> | ".$prof_username."</span><span class='badge bg-primary'><i class='far fa-comments'></i> | Commented</span> On</td>
				<td width='65' align='center'><span class='badge bg-success'><i class='fas fa-angle-up'></i> | Updoots</span></td>
				<td width='65' align='center'><span class='badge bg-danger'>Boops | <i class='fas fa-angle-down'></i></span></td>
				<td width='65' align='center'><span class='badge bg-success'><i class='fas fa-balance-scale'></i> | Votes</span></td>
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

					

			
					$posts .= "<tr class=' text-white'>
					<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'><button class='btn bg-primary text-white'>
					
					<table>
								  <tbody>
									<tr>
									  <td class='align-middle'><p><i class='far fa-comments text-white'></i></p></td>
									  <td class='align-middle'><p>".$content."</p></td>
									</tr>
								  </tbody>
								</table>
					
					</button></a>
					<br />
					<span class='post_info text-white'>Posted by: <span class='badge bg-dark'><i class='fas fa-user-circle'></i> | ".$prof_username."</span> <br />on ".$date."</span></td>";

					$sql33 = "SELECT * FROM topics WHERE id='".$tid."'";
					$res33 = mysqli_query($con, $sql33) or die(mysqli_error());
					while($row33 = mysqli_fetch_assoc($res33)){
						$topic_title = $row33['topic_title'];
						$topic_creator = $row33['topic_creator'];
						$topic_date = $row33['topic_date'];
					

						

						$stmt5 = $con->prepare('SELECT username,id FROM users WHERE id = ?');
						// In this case we can use the account ID to get the account info.
						$stmt5->bind_param('i', $topic_creator);
						$stmt5->execute();
						$stmt5->bind_result($topic_creator_username,$user_id);
						$stmt5->fetch();
						$stmt5->close();

						if((!$topic_creator_username)){
							$topic_creator_username ='MissingNo';
						}

						$posts .= "<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>
						
						<button class='btn bg-primary'>
						

						 <table>
								  <tbody>
									<tr>
									  <td class='align-middle'><p><i class='fas fa-book-open text-white'></i></p></td>
									  <td class='align-middle text-white'><p>".$topic_title."</p></td>
									</tr>
								  </tbody>
								</table>
						 </button>
						
						</a>
						<br />
						<span class='post_info text-white'>Created by: <a href='profile.php?uid=".$topic_creator."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$topic_creator_username."</span></a><br/> on ".$topic_date."</span></td>";
					}


					$posts .= "<td align='center' class=' text-white'>".$updoots."</td>
					<td align='center'>".$boops."</td>
					<td align='center'>".$votescast."</td>
					</tr>";
					$posts .= "<tr><td colspan='6'><hr/></td></tr>";
				}


				$posts .= "</table>";
				echo $posts;

			}else{
							
				
				echo"<h2 class='text-center'>You haven't created a topic yet</h2>";
			}




						
						
					
		?>
	</div>

	<div id="oldvotes" class="container tab-pane fade">
		<?php
			$uid = $_GET['uid'];
			$votes ="";
				
			$sql888 = "SELECT * FROM rating_info WHERE user_id='".$uid."' ORDER BY rating_date DESC ";
			$res888 = mysqli_query($con, $sql888) or die(mysqli_error());

			if (mysqli_num_rows($res888) > 0){
				
				$votes .= "<table width='100%' style='border-collapse:collapse;'>";
				$votes .= "<h2 class=' text-white'>Votes</h2>";
				$votes .= "<tr style='background-color:#dddddd;'>
				<td ><span class='badge bg-primary'><i class='far fa-comments'></i> | Comments</span> <span class='badge bg-dark'><i class='fas fa-user-circle'></i> | ".$prof_username."</span> <span class='badge bg-success'><i class='fas fa-balance-scale'></i> | Voted</span> On</td>
				<td width='200'><span class='badge bg-primary'><i class='fas fa-book-open'></i> | Topics</span> <span class='badge bg-dark'><i class='fas fa-user-circle'></i> | ".$prof_username."</span> <span class='badge bg-success'><i class='fas fa-balance-scale'></i> | Voted</span> On</td>
				<td width='100' align='center'><span class='badge bg-success'><i class='fas fa-balance-scale'></i> | Voted</span></td>
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
						
						$post_creator = $row39['post_creator'];



						$stmt55 = $con->prepare('SELECT username,id FROM users WHERE id = ?');
						// In this case we can use the account ID to get the account info.
						$stmt55->bind_param('i', $post_creator);
						$stmt55->execute();
						$stmt55->bind_result($post_creator_username,$user_id);
						$stmt55->fetch();
						$stmt55->close();

						if((!$post_creator_username)){
							$post_creator_username ='MissingNo';
						}


						$sql40 = "SELECT * FROM topics WHERE  id='".$tid."'";
						$res40 = mysqli_query($con, $sql40) or die(mysqli_error());

						while($row40 = mysqli_fetch_assoc($res40)){
							
							$topic_date = $row40['topic_date'];
							$topic_title = $row40['topic_title'];
							$topic_creator = $row40['topic_creator'];




							$stmt5 = $con->prepare('SELECT username,id FROM users WHERE id = ?');
							// In this case we can use the account ID to get the account info.
							$stmt5->bind_param('i', $topic_creator);
							$stmt5->execute();
							$stmt5->bind_result($topic_creator_username,$user_id);
							$stmt5->fetch();
							$stmt5->close();

							if((!$topic_creator_username)){
								$topic_creator_username ='MissingNo';
							}

							

							
							
						}
						
						$votes .= "<tr >";
						$votes .= "
						<td>

							<a href='view_topic.php?cid=".$cid."&tid=".$tid."'>
								<button class='btn bg-primary text-white'>
								
									
									

								  <table>
								  <tbody>
									<tr>
									  <td class='align-middle'><p><i class='far fa-comments text-white'></i></p></td>
									  <td class='align-middle'><p>".$content."</p></td>
									</tr>
									
								  </tbody>
								</table>



								</button>
							</a>



							<br />

							<span class='post_info text-white'>
								Posted by: <a href='profile.php?uid=".$post_creator."'>
									<span class='badge bg-info'>
										<i class='fas fa-user-circle'></i>
										 | ".$post_creator_username."
									</span>
								</a> 
								<br />on ".$date."</span></td>";
					
						$votes .= "<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'><button class='btn bg-primary'>
						<table>
								  <tbody>
									<tr>
									  <td class='align-middle'><p><i class='fas fa-book-open text-white'></i></p></td>
									  <td class='align-middle text-white'><p>".$topic_title."</p></td>
									</tr>
								  </tbody>
								</table>
						
						</button>
						
						</a>
						<br />
						<span class='post_info text-white'>Created by: <a href='profile.php?uid=".$topic_creator."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$topic_creator_username."</span></a><br/> on ".$topic_date."</span></td>";
						
						$votes .= "
						<td align='center' class='text-white'>".$rating_action."</td>
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

	<div id="notvoted" class="container tab-pane  active">
		<?php
			$uid = $_GET['uid'];
			$notvoted ="";
				
			$sql77 = "SELECT * FROM posts ORDER BY post_date DESC ";
			$res77 = mysqli_query($con, $sql77) or die(mysqli_error());

			if (mysqli_num_rows($res77) > 0){
				
				$notvoted .= "<table width='100%' style='border-collapse:collapse;'>";
				$notvoted .= "<h2>Not Voted Yet</h2>";
				$notvoted .= "<tr style='background-color:#dddddd;'>
				<td >Posts <span class='badge bg-dark'><i class='fas fa-user-circle'></i> | ".$prof_username."</span> Hasn't Voted On Yet</td>
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
			$notvoted .= "<tr>";
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


								
								$notvoted .= "<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$content."</a>
								<br /><span class='post_info'>Posted by: <a href='profile.php?uid=".$post_creator."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$post_creator_username."</span></a> <br />on ".$date."</span></td>";

								
							}
							
							
							
						
							$notvoted .= "<td><a href='view_topic.php?cid=".$cid."&tid=".$tid."'>".$topic_title."</a>
							<br />
							<span class='post_info'>Created by: <a href='profile.php?uid=".$topic_creator."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$tovote_username."</span></a><br/> on ".$topic_date."</span></td>";
							
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
							
				echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
				echo"<h2 class='text-center'>You haven't Messaged anyone yet</h2>";
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
							
				echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
				echo"<h2 class='text-center'>You haven't Messaged anyone yet</h2>";
			}

		?>
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
		 <tr>
		  <?php
					
		echo "<td>Username:</td>
		  <td><a href='profile.php?uid=".$seshid."'><span class='badge bg-success'><i class='fas fa-user-circle'></i> | ".$seshusername."</span></a></td>";
	

		  
		?>
		 </tr>
		 <tr>
		  <td>Email:</td>
		  <td><?=$seshemail?></td>
		 </tr>
		</table>

      </div>

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
			
	</body>
</html>