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

$cid = $_GET['cid'];
$tid = $_GET['tid'];

$uid = $_SESSION['id'];
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
		
		<div class="content">
			
			

			<?php
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			$config = parse_ini_file('db.ini');
			$con =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
            $con->set_charset('utf8mb4'); // charset
            
            
            $cid = $_GET['cid'];
            $tid = $_GET['tid'];
            $pid = "";
            $rating_action = "";
            $current_rating_action = "not voted";
            









            $view_topics_votes= "";


            $stmt_posts = $con->prepare('SELECT id,post_content,post_date,post_creator,post_replying_to FROM posts WHERE category_id = '.$cid.' AND topic_id = '.$tid.' ORDER BY post_date ASC');
			$stmt_posts->execute();
			$stmt_posts->store_result();
			$stmt_posts->bind_result($pid,$post_content,$post_date,$post_creator,$post_replying_to);
            
            //Get topics table info from database
            $stmt_post_topics = $con->prepare('SELECT topic_title,topic_date,topic_creator,topic_views FROM topics WHERE category_id = '.$cid.' AND id = '.$tid.'');
            $stmt_post_topics->execute();
            $stmt_post_topics->store_result();
            $stmt_post_topics->bind_result($topic_title,$topic_date,$topic_creator,$topic_views);
            $stmt_post_topics->fetch();

            //Get catergories table info from database
            $stmt_post_cats = $con->prepare('SELECT category_title,category_description FROM catergories WHERE id = '.$cid.'');
            $stmt_post_cats->execute();
            $stmt_post_cats->store_result();
            $stmt_post_cats->bind_result($category_title,$category_description);
            $stmt_post_cats->fetch();

                $stmt777 = $con->prepare('SELECT username FROM users WHERE id = ?');
                // In this case we can use the account ID to get the account info.
                $stmt777->bind_param('i', $topic_creator);
                $stmt777->execute();
                $stmt777->bind_result($topic_creator_username);
                $stmt777->fetch();
                $stmt777->close();
        
                if((!$topic_creator_username)){
                    $topic_creator_username ='MissingNo';
                }
            
            if ($stmt_post_topics ->num_rows > 0) {
			
                $old_views = $topic_views;
                $new_views = $old_views + 1;

                $sql44 = "UPDATE topics SET topic_views='".$new_views."' WHERE category_id='".$cid."' AND id='".$tid."' ";
                $stmt44 = $con->prepare($sql44);
                $stmt44->execute();


                $stmt_post_count = $con->prepare(
                    'SELECT count(*) 
                    FROM posts 
                    WHERE category_id = '.$cid.' AND topic_id = '.$tid.''); 
                $stmt_post_count->execute(); 
                $stmt_post_count->bind_result($topic_comment_count);
                $stmt_post_count->fetch();
                $stmt_post_count->close();

                

                //POSTS Header HTML
				$view_topics_votes .= "
				<div class='text-center container  bg-secondary'width='100%' style='border-collapse:collapse;'>
					<div class='row text-center'>
						<div class='col'>	
							<h1 class='text-white'>
								<span class='badge bg-primary text-outline-black'>
									<i class='fas fa-book-open'></i>
									| ".$topic_title."
								</span>
                                <br>
                                <span class='badge bg-success' style='font-size: 50%;'><i class='fas fa-eye text-outline-black'></i> | ".$topic_views."</span>
                                <span class='badge bg-primary' style='font-size: 50%;'><i class='far fa-comments text-outline-black'></i> | ".$topic_comment_count."</span>
                            </h1>
                            <p class='text-white'>
                                in: 
                                <span class='badge bg-primary text-outline-black'>
                                    <i class='far fa-bookmark'></i>
                                     | ".$category_title."
                                </span>
                                <i class='fas fa-edit'style='font-size:.5vw;'></i>
                                by:
                                <a href='profile.php?uid=".$topic_creator."'><span class='badge bg-info text-outline-black'>
									<i class='fas fa-user-circle'></i>
									| ".$topic_creator_username."
								</span></a>
                                <br>
                                <span style='font-size: 75%;'>".$topic_date."</span>
							</p>
						</div>
					</div>
				";

                while(($row_posts  = $stmt_posts ->fetch()) ){
                    if((!$post_creator_username)){
                        $post_creator_username ='MissingNo';
                    }
                    /////////////////////
                    $bgupvotenot="bg-secondary";
                    $bgdownvotenot="bg-secondary";

                    $bgvotetallyvote="btn-vote";
                    $bgvoteytallyvote="btn-dark";
                    $bgvoteyiconvote="fas fa-balance-scale";   


                        $stmt_sesh_rating_action = $con->prepare(
                            'SELECT rating_action
                            FROM rating_info 
                            WHERE user_id = '.$seshid.' AND post_id='.$pid.'');
                        $stmt_sesh_rating_action->execute();
                        $stmt_sesh_rating_action->store_result();
                        $stmt_sesh_rating_action->bind_result($sesh_rating_action);

                        $stmt_sesh_rating_action->fetch();

                            

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

                            

                            if(($sesh_rating_action == 'updoot')){
                                $bgupvotenot='btn-dope';
                                $bgdownvotenot="btn-vote-nope";
                            }
                            if(($sesh_rating_action == 'boop')){
                                $bgupvotenot='btn-vote-dope';
                                $bgdownvotenot="btn-nope ";
                            }
                            if(($sesh_rating_action != 'boop') && ($sesh_rating_action != 'updoot')){
                                $bgupvotenot='btn-vote-dope';
                                $bgdownvotenot="btn-vote-nope";
                            }
                            $total_votes = $number_of_boops + $number_of_updoots;

                            if($number_of_boops != $number_of_updoots ){
                                $total_updoot_perc =  round(($number_of_updoots / $total_votes) * 100,2);
                                $total_boop_perc =  round(($number_of_boops / $total_votes)* 100, 2);
                            }

                            if($number_of_boops > $number_of_updoots){
                                $bgvotetallyvotenot='btn-nope';
                                $bgvoteytallyvotenot='bg-danger';
                                $bgvoteyiconvotenot='fas fa-balance-scale-right';
                                $winpercvotenot= $total_boop_perc;
                                }

                            if($number_of_boops < $number_of_updoots){
                                $bgvotetallyvotenot='btn-dope';
                                $bgvoteytallyvotenot='bg-success';
                                $bgvoteyiconvotenot='fas fa-balance-scale-left';
                                $winpercvotenot= $total_updoot_perc;
                                
                            }

                            if($number_of_boops == $number_of_updoots ){
                                $winpercvotenot= 50;
                                $bgvotetallyvotenot='btn-vote';
                                $bgvoteytallyvotenot='bg-dark';
                                $bgvoteyiconvotenot='fas fa-balance-scale';
                                if(($number_of_boops == 0)||($number_of_boops == 0)){
                                    $bgupvotenot='btn-vote-dope';
                                    $bgdownvotenot="btn-vote-nope";
                                }

                            }
                    
                           

                            $stmt666 = $con->prepare('SELECT username FROM users WHERE id = ?');
                            // In this case we can use the account ID to get the account info.
                            $stmt666->bind_param('i', $post_creator);
                            $stmt666->execute();
                            $stmt666->bind_result($post_creator_username);
                            $stmt666->fetch();
                            $stmt666->close();
    
                            if((!$post_creator_username)){
                                $post_creator_username ='MissingNo';
                            }
                            
                            if($post_replying_to==null){
                            //posts content HTML		
                            $view_topics_votes .= "
                            <hr class='text-white'>
                            <div class='row'>
                                <div class='col'>
                                    <a href='view_topic.php?cid=".$cid."&tid=".$tid."'>
                                        <button class='btn btn-dark text-white'>
                                            <p class='btn ".$bgvotetallyvotenot."'><span ><i class='far fa-comments text-white text-outline-black'></i> | ".$post_content."</span></p>
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
                                            | ".$number_of_updoots."
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
                                        <a href='profile.php?uid=".$post_creator."'>
                                            <span class='badge bg-info text-outline-black' >
                                                <i class='fas fa-user-circle'style='font-size:.5vw;'></i>
                                                | ".$post_creator_username."
                                            </span>
                                        </a>
                                        to 
                                        <a href='view_category.php?cid=".$cid."'>
                                            <span class='badge bg-primary text-outline-black'>
                                                <i class='fas fa-book-open'style='font-size:.5vw;'></i>
                                                | ".substr($topic_title, 0, 40)."
                                            </span>
                                        </a>
                                        - ".$post_date."
                                    </span>    
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col'>
                                
                                    <div class='reply'>
                
                                        <form action='post_reply_parser.php' method='post' autocomplete='off'>
                                    
                                            
                            
                                            <div class='mb-3 mt-3'>
                                            <textarea class='form-control' rows='5' id='reply_content' name='reply_content' type='text'></textarea></div>
                            
                            
                                            <div class='mb-3 mt-3'>
                                            <input type='hidden' name='cid' value='".$cid."'/>
                                            <input type='hidden' name='tid' value='".$tid."'/>
                                            <input type='hidden' name='post_replying_to' value='".$pid."'/>
                                            <button class='btn btn-warning text-white text-outline-black' type='submit' name='reply_submit' value='Post'>Add Your Thoughts about <span class='badge bg-dark text-outline-black'><i class='far fa-comments'></i> | ".$post_content."</span> </button></div>
                                            
                                        </form>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                                        
                            <br />";
                            }
                            

                                $stmt_posts_replies = $con->prepare('SELECT id,post_content,post_date,post_creator,post_replying_to FROM posts WHERE category_id = '.$cid.' AND topic_id = '.$tid.' AND post_replying_to = '.$pid.' ORDER BY post_date ASC');
                                $stmt_posts_replies->execute();
                                $stmt_posts_replies->store_result();
                                $stmt_posts_replies->bind_result($reply_pid,$reply_post_content,$reply_post_date,$reply_post_creator,$reply_post_replying_to);

                                if ($stmt_posts_replies ->num_rows > 0) {

                                    while(($row_posts_replies  = $stmt_posts_replies ->fetch()) ){






                                        if (!$reply_post_content){
                                            $reply_post_content = "MissingComment";
                                        }
                
                
                                        $stmt_sesh_rating_action_reply = $con->prepare(
                                            'SELECT rating_action
                                            FROM rating_info 
                                            WHERE user_id = '.$seshid.' AND post_id='.$reply_pid.'');
                                        $stmt_sesh_rating_action_reply->execute();
                                        $stmt_sesh_rating_action_reply->store_result();
                                        $stmt_sesh_rating_action_reply->bind_result($sesh_rating_action_reply);
                
                                        $stmt_sesh_rating_action_reply->fetch();
                
                                            $bgupvotenot_reply="bg-secondary";
                                            $bgdownvotenot_reply="bg-secondary";
                
                                            $stmt_updoot_count_reply = $con->prepare(
                                                "SELECT count(*) 
                                                FROM rating_info 
                                                WHERE rating_action = 'updoot' AND post_id='".$reply_pid."'"); 
                                            $stmt_updoot_count_reply->execute(); 
                                            $stmt_updoot_count_reply->bind_result($number_of_updoots_reply);
                                            $stmt_updoot_count_reply->fetch();
                                            $stmt_updoot_count_reply->close();
                
                                            $stmt_boop_count_reply = $con->prepare(
                                                "SELECT count(*) 
                                                FROM rating_info 
                                                WHERE rating_action = 'boop' AND post_id='".$reply_pid."'"); 
                                            $stmt_boop_count_reply->execute(); 
                                            $stmt_boop_count_reply->bind_result($number_of_boops_reply);
                                            $stmt_boop_count_reply->fetch();
                                            $stmt_boop_count_reply->close();
                
                                            $bgvotetallyvote_reply="btn-vote";
                                            $bgvoteytallyvote_reply="btn-dark";
                                            $bgvoteyiconvote_reply="fas fa-balance-scale";
                
                                            if(($sesh_rating_action_reply == 'updoot')){
                                                $bgupvotenot_reply='btn-dope';
                                                $bgdownvotenot_reply="btn-vote-nope";
                                            }
                                            if(($sesh_rating_action_reply == 'boop')){
                                                $bgupvotenot_reply='btn-vote-dope';
                                                $bgdownvotenot_reply="btn-nope ";
                                            }
                                            if(($sesh_rating_action_reply != 'boop') && ($sesh_rating_action_reply != 'updoot')){
                                                $bgupvotenot_reply='btn-vote-dope';
                                                $bgdownvotenot_reply="btn-vote-nope";
                                            }
                                            $total_votes_reply = $number_of_boops_reply + $number_of_updoots_reply;
                
                                            if($number_of_boops_reply != $number_of_updoots_reply ){
                                                $total_updoot_perc_reply =  round(($number_of_updoots_reply / $total_votes_reply) * 100,2);
                                                $total_boop_perc_reply =  round(($number_of_boops_reply / $total_votes_reply)* 100, 2);
                                            }
                
                                            if($number_of_boops_reply > $number_of_updoots_reply){
                                                $bgvotetallyvotenot_reply='btn-nope';
                                                $bgvoteytallyvotenot_reply='bg-danger';
                                                $bgvoteyiconvotenot_reply='fas fa-balance-scale-right';
                                                $winpercvotenot_reply= $total_boop_perc_reply;
                                                }
                
                                            if($number_of_boops_reply < $number_of_updoots_reply){
                                                $bgvotetallyvotenot_reply='btn-dope';
                                                $bgvoteytallyvotenot_reply='bg-success';
                                                $bgvoteyiconvotenot_reply='fas fa-balance-scale-left';
                                                $winpercvotenot_reply= $total_updoot_perc_reply;
                                                
                                            }
                
                                            if($number_of_boops_reply == $number_of_updoots_reply ){
                                                $winpercvotenot_reply= 50;
                                                $bgvotetallyvotenot_reply='btn-vote';
                                                $bgvoteytallyvotenot_reply='bg-dark';
                                                $bgvoteyiconvotenot_reply='fas fa-balance-scale';
                                                if(($number_of_boops_reply == 0)||($number_of_boops_reply == 0)){
                                                    $bgupvotenot_reply='btn-vote-dope';
                                                    $bgdownvotenot_reply="btn-vote-nope";
                                                }
                
                                            }
                                    
                                           
                
                                            $stmt669 = $con->prepare('SELECT username FROM users WHERE id = ?');
                                            // In this case we can use the account ID to get the account info.
                                            $stmt669->bind_param('i', $reply_post_creator);
                                            $stmt669->execute();
                                            $stmt669->bind_result($reply_post_creator_username);
                                            $stmt669->fetch();
                                            $stmt669->close();
                    
                                            if((!$reply_post_creator_username)){
                                                $reply_post_creator_username ='MissingNo';
                                            }








                                        $view_topics_votes .= "
                    <div class='row'>
                        <div class='col'></div>
                        <div class='col-sm-10'>
                            <div class='row'>
                                <div class='col'>
                                    <a href='view_topic.php?cid=".$cid."&tid=".$tid."'>
                                        <button class='btn btn-dark text-white'>
                                            <p class='btn ".$bgvotetallyvotenot_reply."'><span ><i class='far fa-comments text-white text-outline-black'></i> | ".$reply_post_content."</span></p>
                                        </button>
                                    </a>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col'></div>
                                <div class='col'>
                                    <form action='update_updoots.php' method='post'>
                                        <input type='hidden' name='rating_action' value='updoot'/>
                                        <input type='hidden' name='tid' value='".$reply_pid."'/>
                                        <button class='btn ".$bgupvotenot_reply." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
                                            <i class='fas fa-angle-up'style='font-size:.5vw;'></i>
                                            | ".$number_of_updoots_reply."
                                        </button>
                                    </form>
                                </div>
                                <div class='col'>
                                    <span class='badge ".$bgvoteytallyvotenot_reply." text-outline-black' >
                                        <i class='".$bgvoteyiconvotenot_reply."'style='font-size:.5vw;'></i>
                                        | ".$winpercvotenot_reply."
                                    </span>   
                                </div>
                                <div class='col'>
                                    <form action='update_updoots.php' method='post'>
                                        <input type='hidden' name='rating_action' value='boop'/>
                                        <input type='hidden' name='tid' value='".$reply_pid."'/>
                                        <button class='btn ".$bgdownvotenot_reply." ' type='submit' name='updoot_submit' id='updoot_submit' value='Up' >
                                            <i class='fas fa-angle-down'style='font-size:.5vw;'></i>
                                            | ".$number_of_boops_reply."
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
                                        <a href='profile.php?uid=".$reply_post_creator."'>
                                            <span class='badge bg-info text-outline-black' >
                                                <i class='fas fa-user-circle'style='font-size:.5vw;'></i>
                                                | ".$reply_post_creator_username."
                                            </span>
                                        </a>
                                        replying to 
                                        <a href='view_category.php?cid=".$cid."'>
                                            <span class='badge bg-dark text-outline-black'>
                                                <i class='far fa-comments'style='font-size:.5vw;'></i>
                                                | ".substr($post_content, 0, 40)."
                                            </span>
                                        </a>
                                        - ".$reply_post_date."
                                    </span>    
                                </div>
                            </div>
                            
                        </div>
                    </div>
                                        
                            <br />";
                                    }

                                }else{
                                    
                                }
                                
                                $stmt_posts_replies->close();
                            



                    


                        $stmt_sesh_rating_action->close();
                    
                }
                
                $view_topics_votes .= "
                    <button type='button' style='width:100%' class='btn btn-warning text-white text-outline-black' data-bs-toggle='modal' data-bs-target='#replyModal'>Add Your Thoughts about <span class='badge bg-primary text-outline-black'><i class='fas fa-book-open'></i> | ".$topic_title."</span> </button>
                </div>";
                echo $view_topics_votes;


            
            }else{

                $view_topics_votes .= "Nothing to see here, move along";
                echo $view_topics_votes;
            }
            $stmt_post_cats->close();
            $stmt_post_topics->close();
            
            $stmt_posts->close();













            


			?>

			
			
		</div>





<!-- The Modal -->
<div class="modal" id="replyModal">
  <div class="modal-dialog">
    <div class="modal-content bg-secondary">

      <!-- Modal Header -->
      <div class="modal-header text-white">
        <h4 class="modal-title">Reply</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body text-center">

        <div class="reply">
			
			<form action="post_reply_parse.php" method="post" autocomplete="off">
           
                <div class="mb-3 mt-3">
				<?php
					
				echo "<p><a name='username' type='text' id='username' href='profile.php?uid=".$uid."'><span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$username."</span></a></p></div>";

				?></div>

                <div class="mb-3 mt-3">
				<textarea class="form-control" rows="5" id="reply_content" name="reply_content" type="text"></textarea></div>


                <div class="mb-3 mt-3">
                <input type="hidden" name="cid" value="<?php echo $cid; ?>"/>
                <input type="hidden" name="tid" value="<?php echo $tid; ?>"/>
                <input type="hidden" name="post_replying_to" value="<?php echo $post_replying_to; ?>"/>
				<button class="btn btn-warning text-white" type="submit" name="reply_submit" value="Post">Reply</button></div>
                
			</form>
		</div>

        



      </div>

    </div>
  </div>
</div>



<?php

    
if(isset($_POST['reply_submit'])){
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $config = parse_ini_file('db.ini');
			$con =  new mysqli("localhost",$config['username'],$config['password'],$config['db']);
            $con->set_charset('utf8mb4'); // charset
        
    $creator = $_SESSION['id'];
    $cid = $_POST['cid'];
    $tid = $_POST['tid'];
    $reply_content = $_POST['reply_content'];

    $sql = "INSERT INTO posts (category_id, topic_id, post_creator, post_content, post_date) VALUES ('".$cid."', '".$tid."', '".$creator."', '".$reply_content."', now() )";
    $res = mysqli_query($con, $sql) or die(mysqli_error());

        $sql2 = "UPDATE catergories SET last_post_date=now(), last_user_posted='".$creator."' WHERE id='".$cid."' LIMIT 1";
        $res2 = mysqli_query($con, $sql2) or die(mysqli_error());

        $sql3 = "UPDATE topics SET topic_reply_date=now(), topic_last_user='".$creator."' WHERE id='".$tid."' LIMIT 1";
        $res3 = mysqli_query($con, $sql3) or die(mysqli_error());

        //send email to ppl involved with topic
        


        if ( ($res) && ($res2) && ($res3) ) {
            echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
            echo "<p>Reply successfully posted</p>";
        }else{
            echo"<div class='text-center'><a href ='home.php' class=''><button style='width:100%;'class='btn btn-success'>Return to Sub Index</button></a></div>";
            echo "<p>There was a problem posting your reply, try again</p>";
        }
    }else{
        exit();
    }



?>

<script src="script.js" > </script>

	</body>
</html>