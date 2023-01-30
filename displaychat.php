<?php
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
include_once("connect.php");

// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt = $con->prepare('SELECT email,username,id FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($seshemail,$seshusername,$seshid);
$stmt->fetch();
$stmt->close();

$stored_to_id = $_GET['to_id'];
$stored_from_id = $_GET['from_id'];
//$prof_username = $_GET['uname'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
  <link rel="icon" type="image/x-icon" href="img/favicon.ico">
		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script><link href="style.css" rel="stylesheet" type="text/css">
        
	</head>
	<body class="loggedin bg-dark">
		
		<div class="text-center">

        <a href="#topButton"><button style="width:100%;" class="btn btn-success" id="bottomButton" title="Go to Bottom">Newest</button></a>
<?php

$stmt5 = $con->prepare('SELECT username FROM users WHERE id = ?');
$stmt5->bind_param('i', $stored_to_id);
$stmt5->execute();
$stmt5->bind_result($to_name);
$stmt5->fetch();
$stmt5->close();

if((!$to_name)){
	$to_name ='MissingNo';
}


$stmt6 = $con->prepare('SELECT username FROM users WHERE id = ?');
$stmt6->bind_param('i', $stored_from_id);
$stmt6->execute();
$stmt6->bind_result($from_name);
$stmt6->fetch();
$stmt6->close();

if((!$from_name)){
	$from_name ='MissingNo';
}




$stmt8 = $con->prepare('SELECT from_id,to_id,message_date,message_content FROM dm WHERE (to_id = '.$stored_to_id.' AND from_id = '.$stored_from_id.') OR (to_id = '.$stored_from_id.' AND from_id = '.$stored_to_id.')  ORDER BY message_date');
$stmt8->execute();
$stmt8->store_result();
$stmt8->bind_result($from_id,$to_id,$message_date,$message_content);



if ($stmt8->num_rows > 0) {


    $messages= "
    <table style='width:100%'>
     <tr class='bg-info text-white'>
	  <td style='width:70%'>Message</td>
	  <td>To/From/Date</td>
	 </tr>";
     




     
     while(($row8 = $stmt8->fetch()) ){
        
        
        if($from_id != $to_id){    
        if($from_id == $stored_from_id){
        
        
        $messages .= "
            <tr class=' text-white'>
            <td>" .$message_content."</td>
            <td>To: <span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$to_name."</span>
            <br/> From: <span class='badge bg-success'><i class='fas fa-user-circle'></i> | ".$from_name."</span> 
            <br/>on ".$message_date."</td>
            </tr>
            <tr  class=' text-white'><td colspan='2'><hr /></td></tr>";
        
        }
        if($from_id == $stored_to_id){
        
        
        $messages .= "
            <tr class=' text-white'>
            <td>" .$message_content."</td>
            <td>To: <span class='badge bg-success'><i class='fas fa-user-circle'></i> | ".$from_name."</span>
            <br/> From: <span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$to_name."</span> 
            <br/>on ".$message_date."</td>
            </tr>
            <tr  class=' text-white'><td colspan='2'><hr /></td></tr>";
    
        }
    }else{
        $messages .= "
            <tr class=' text-white'>
            <td>" .$message_content."</td>
            <td>To: <span class='badge bg-info'><i class='fas fa-user-circle'></i> | ".$to_name."</span>
            <br/> From: <span class='badge bg-success'><i class='fas fa-user-circle'></i> | ".$from_name."</span> 
            <br/>on ".$message_date."</td>
            </tr>
            <tr  class=' text-white'><td colspan='2'><hr /></td></tr>";
    }

     }
     
    







        $messages .= "</table>";
        echo $messages ;

}
else{

    echo "didn't work";
}



$stmt8->fetch();





$stmt8->close();




	

	
 
	  //show each message between users
		?>
        
    
    <a href="#bottomButton"><button style="width:100%;" class= "btn btn-info text-white" id="topButton" title="Go to top">Oldest</button></a>
    </div>
			

            

    <script type="text/javascript">
            
            function autoScrolling() {
   window.scrollTo(0,document.body.scrollHeight);
}

autoScrolling();
            </script>
</body>
    </html>