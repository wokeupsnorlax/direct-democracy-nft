<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
} 
    
if(isset($_POST['reply_submit'])){
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $con = mysqli_connect('localhost', 'root', '', 'phplogin');
        
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
            echo "<p>Reply successfully posted | <a href='home.php'>Return to Index</a></p>";
        }else{
            echo "<p>There was a problem posting your reply, try again</p>";
        }
    }else{
        exit();
    }



?>