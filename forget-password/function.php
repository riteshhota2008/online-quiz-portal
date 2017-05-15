<?php

function checkUser($emailID)
{
    global $dbConn;

    $query = mysqli_query($dbConn, "SELECT ID FROM a1_users WHERE emailID = '$emailID'");

    if (mysqli_num_rows($query) > 0) {
        return 'true';
    } else {
        return 'false';
    }
}

function UserID($emailID)
{
    global $dbConn;

    $query = mysqli_query($dbConn, "SELECT ID FROM a1_users WHERE emailID = '$emailID'");
    $row = mysqli_fetch_assoc($query);

    return $row['ID'];
}


function generateRandomString($length = 20)
{
    // This function has taken from stackoverflow.com

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return md5($randomString);
}

function send_mail($to, $token)
{
    require 'PHPMailer/PHPMailerAutoload.php';

    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Host = 'mail.smtp2go.com';//smtp.gmail.com
    $mail->SMTPAuth = true;
    $mail->Username = 'riteshhota.2008@gmail.com';//Smtp Username
    $mail->Password = 'pass';//Smtp Password
    $mail->SMTPSecure = 'tls';//ssl or tls
    $mail->Port = 2525;//465 or 587

    $mail->From = 'riteshhota.2008@gmail.com';//Your email Id
    $mail->FromName = 'Ritesh Hota';//Your Name
    $mail->addAddress($to);
    $mail->addReplyTo('riteshhota.2008@gmail.com', 'Reply');//Reply Address

    $mail->isHTML(true);

    $mail->Subject = 'Online Quiz: Password Recovery Instruction';
    $link = 'http://riteshhota.16mb.com/forget.php?email=' . $to . '&token=' . $token;
    $mail->Body = "Hello,<br><br>You have requested for your password recovery. <a href='$link' target='_blank'>Click here</a> to reset your password. If you are unable to click the link then copy the below link and paste in your browser to reset your password.<br><br><i>" . $link . "</i><br><br>Regards,<br>Ritesh";

    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    if (!$mail->send()) {
        return 'fail';
    } else {
        return 'success';
    }

}

function verifytoken($userID, $token)
{
    global $dbConn;

    $query = mysqli_query($dbConn, "SELECT valid FROM recovery_keys WHERE userID = $userID AND token = '$token'");
    $row = mysqli_fetch_assoc($query);

    if (mysqli_num_rows($query) > 0) {
        if ($row['valid'] == 1) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }

}

?>
