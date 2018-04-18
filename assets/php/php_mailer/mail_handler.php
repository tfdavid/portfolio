<?php
    require_once('email_config.php');
    require('phpmailer/PHPMailer/PHPMailerAutoload.php');

    // validate POST inputs
    $message = [];
    $output = [
        'success' => null,
        'messages' => []
    ];

    // sanitize name field
    $message['c_name'] = filter_var($_POST['c_name'], FILTER_SANITIZE_STRING);
    if(empty($message['c_name'])){
        $output['success'] = false;
        $output['messages'][]= 'missing name key';
    }
    // validate email field
    $message['c_email'] = filter_var($_POST['c_email'], FILTER_SANITIZE_EMAIL);
    if(empty($message['c_email'])){
        $output['success'] = false;
        $output['messages'][]= 'invalid email key';
    }
    // sanitize message
    $message['c_message'] = filter_var($_POST['c_message'], FILTER_SANITIZE_STRING);
    if(empty($message['c_message'])){
        $output['success'] = false;
        $output['messages'][]= 'missing message key';
    }
    if ($output['success'] !== null){
        http_response_code(400);
        echo json_encode($output);
        exit();
    }


    $mail = new PHPMailer;
    // $mail->SMTPDebug = 3;           // Enable verbose debug output. Change to 0 to disable debugging output.

    $mail->isSMTP();                // Set mailer to use SMTP.
    $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers.
    $mail->SMTPAuth = true;         // Enable SMTP authentication


    $mail->Username = EMAIL_USER;   // SMTP username
    $mail->Password = EMAIL_PASS;   // SMTP password
    $mail->SMTPSecure = 'tls';      // Enable TLS encryption, `ssl` also accepted, but TLS is a newer more-secure encryption
    $mail->Port = 587;              // TCP port to connect to
    $options = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->smtpConnect($options);
    $mail->From = $message['c_email'];
    $mail->FromName = $message['c_name'];   
    $mail->addAddress(EMAIL_USER);  
    
    $mail->addReplyTo($message['c_email'], $message['c_name']);          
   

    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(true);   
                                   // Set email format to HTML

    $message['subject'] = substr($message['c_message'], 0, 78);
    $mail->Subject = $message['subject'];
    
    $message['c_message'] = nl2br($message['c_message']);
    $mail->Body    = $message['c_message'];
    $mail->AltBody = htmlentities($message['c_message']);

    if(!$mail->send()) {
        $output['success'] = false;
        $output['messages'][] = 'Message failed to send'; 
    } else {
        $output['success'] = true;
        $output['messages'][] = 'Message sent successfully!';

    }
    echo json_encode($output);
?>
