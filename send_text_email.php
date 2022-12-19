<?php
$to = "Recipient's Email";
$from = "Sender's Email";
$subject = "Subject of Email";

//This message can only contain test. NO HTML tags! The HTML tags will just be printed into the email.
$message = "Message of Email";

$headers="From: $from\r\nReply-To: $from";
mail($to,$subject,$message,$headers);











$sql4 = "SELECT post_creator FROM posts WHERE category_id='".$cid."' AND topic_id''".$tid."' GROUP BY post_creator";
        $res4 = mysqli_query($con, $sql4) or die(mysqli_error());
        while($row4 = mysqli_fetch_assoc($res4)){
            $userids[] .=$row4['post_creator'];
        }
        foreach ($userids as $key){
            $sql5 = "SELECT id, email FROM accounts WHERE id='".$key."' AND forum_notifications='1' LIMIT 1";
            $res5 = mysqli_query($con, $sql5) or die(mysqli_error());
            if(mysqli_num_rows($res5) > 0){
                $row5 = mysqli_fetch_assoc($res5);
                if($row5['id'] != $creator){
                    $email .= $row5['email'].", ";
                }
            }
        }
        $email = substr($email, 0, (strlen($email) - 2));
        $to = "test@test.com";
        $from = "inftgames69@gmail.com";
        $bcc = $email;
        $subject = "Forum Reply";

        //This message can only contain test. NO HTML tags! The HTML tags will just be printed into the email.
        $message = "Someone has replied to a conversation you were a part of on the Direct Democracy Forum";

        $headers="From: $from\r\nReply-To: $from";
        $headers.="\r\nBcc: {$bcc}";
        mail($to,$subject,$message,$headers);
?>