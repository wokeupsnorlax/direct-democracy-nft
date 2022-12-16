<?php
// Change this to your connection info.
$servername  = 'localhost';
$username  = 'root';
$password  = '';
$dbname  = 'phplogin';
$title = $_POST['title'];
$comment = $_POST['comment'];
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}




if (strlen($_POST['title']) > 50 || strlen($_POST['title']) < 3) {
	exit('Post must be between 3 and 50 characters long');
}
if (strlen($_POST['comment']) > 2500 || strlen($_POST['comment']) < 3) {
	exit('Post must be between 50 and 2500 characters long');
}



if (mysqli_query($conn, $sql)) {
    $stmt = $sql->prepare ("INSERT INTO categories (username, catergory_title, catergory_description) VALUES (?, ?, ?)") ;
    $stmt->bind_param('sss', $username, $title, $comment);

     $stmt->execute();
     
     $conn->close();
     echo "New record created successfully";
  } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }
  
  mysqli_close($conn);


	


?>