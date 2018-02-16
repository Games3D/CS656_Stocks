<?php

    require_once 'PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer();

    $mail->Host = "smtp.gmail.com";

    $mail->SMTPAuth = true;

    $mail->Username = "CS673SchoolProject@gmail.com";

    $mail->Password = "schoolproject";

    $mail->SMTPSecure = 'ssl';

    $mail->Port = 465;

    //Change the email Subject
    $mail->Subject = 'Stock Market Project';


    //Change the body message 
    $mail->Body = 'This is a plain-text message body';

    $mail->setFrom('CS673SchoolProject@gmail.com', 'Nidhi Patel');

    //Replace parameters with email and username of user
    $mail->addAddress('np397@njit.edu', 'Nidhi Patel');

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
         echo "Message sent";
       
    }

?>
