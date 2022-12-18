<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		$con = mysqli_connect('localhost', 'root', '', 'phplogin');
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

if(isset($_POST['cat_submit'])){
    if (($_POST['catergory_title'] =="") && ($_POST['catergory_description'] =="")){
        echo "Fill out both fields";
        exit();
    }else{
        

       
        $title = $_POST['catergory_title'];
        $content = $_POST['catergory_description'];
        $creator = $_SESSION['id'];


		$sql =  "INSERT INTO catergories (catergory_title, catergory_description, last_user_posted, last_post_date) VALUES ('".$title."', '".$content."', '".$creator."', now())";
		$res = mysqli_query($con, $sql) or die(mysqli_error());
		$new_cat_id = mysqli_insert_id($con);
        
        

        if (($res)){
            header("Location: home.php";
        }else{
            echo "Error, try again.";
        }
        
    }
}
?>