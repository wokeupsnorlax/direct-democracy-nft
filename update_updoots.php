<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
} 
    
if(isset($_POST['updoot_submit'])){
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $con = mysqli_connect('localhost', 'root', '', 'phplogin');
        
    $creator = $_SESSION['id'];
    $tid = $_POST['tid'];
    $rating_action = $_POST['rating_action'];

    $sql = "SELECT post_id, user_id, rating_action FROM rating_info WHERE user_id='".$creator."' AND  post_id='".$tid."'";
    $res = mysqli_query($con, $sql) or die(mysqli_error());

    if($row = mysqli_fetch_assoc($res)){
        if ($rating_action != ""){
            

            $sql3 = "UPDATE rating_info SET rating_date=now(), rating_action='".$rating_action."' WHERE  user_id='".$creator."' AND  post_id='".$tid."' LIMIT 1";
            $res3 = mysqli_query($con, $sql3) or die(mysqli_error());


            //send email to ppl involved with topic

            if ( ($res3)) {
                echo "<p>'".$rating_action."' successfully updated | <a href='home.php'>Return to Index</a></p>";
            }else{
                echo "<p>There was a problem, try again</p>";
            }
        }
        
    }else{
        $sql2 = "INSERT INTO rating_info (post_id, user_id, rating_action, rating_date) VALUES ('".$tid."', '".$creator."', '".$rating_action."', now() )  LIMIT 1";
            $res2 = mysqli_query($con, $sql2) or die(mysqli_error());
            if ( ($res2)) {
                echo "<p>'".$rating_action."' successfully posted | <a href='home.php'>Return to Index</a></p>";
            }else{
                echo "<p>There was a problem, try again</p>";
            }
    }
        
    
    
}else{
    exit();
}



?>