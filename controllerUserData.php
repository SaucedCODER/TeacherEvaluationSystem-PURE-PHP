<?php
session_start();
// ob_start();
date_default_timezone_set("Asia/Manila");

require "connection.php";
require "db_connect.php";

require('includes/PHPMailer.php');
require('includes/Exception.php');
require('includes/SMTP.php');

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPmailer;

$email = "";
$name = "";
$errors = array();


//if user click verification code submit button
if (isset($_POST['check'])) {
    $_SESSION['info'] = "";
    $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
    $check_code = "SELECT * FROM all_users WHERE code = $otp_code ";
    $code_res = mysqli_query($con, $check_code);
    if (mysqli_num_rows($code_res) > 0) {
        $fetch_data = mysqli_fetch_assoc($code_res);
        $fetch_code = $fetch_data['code'];
        $email = $fetch_data['email'];
        $code = 0;
        $update_otp = "UPDATE all_users SET code = $code WHERE code = $fetch_code";
        $update_res = mysqli_query($con, $update_otp);
        if ($update_res) {
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            header('location: home.php');
            exit();
        } else {
            $errors['otp-error'] = "Failed while updating code!";
        }
    } else {
        $errors['otp-error'] = "You've entered incorrect code!";
    }
}

//if user click continue button in forgot password form
if (isset($_POST['check-email'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $check_email = "SELECT * FROM all_users WHERE email='$email'";
    $run_sql = mysqli_query($con, $check_email);
    if (mysqli_num_rows($run_sql) > 0) {
        $code = rand(999999, 111111);
        $insert_code = "UPDATE all_users SET code = $code WHERE email = '$email'";
        $run_query =  mysqli_query($con, $insert_code);

        if ($run_query) {
            $subject = "Password Reset Code";
            $message = "  <h2 style='font-family:sans-serif;'>Institute of Business Science and Medical Arts</h2><hr> 
            <h3>Teacher Evaluation System</h3>
            <br>Your password reset code is <b style='color:green; font-size:17px; '> $code .</b>";
            $sender = "From: ctesibsma@gmail.com -- @noreply this is a test";

            $mail = new PHPmailer();
            $mail->isSMTP();
            $mail->Host = 'tls://smtp.gmail.com:587';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;
            $mail->Username = 'ctesibsma@gmail.com';                         // Enable SMTP authentication
            $mail->Password = 'tggkcoebnruaoesk';
            $mail->SMTPSecure = 'tls';
            // $mail->port = "587";
            $mail->setFrom("ctesibsma@gmail.com");
            $mail->addAddress($email);               // Name is optional                              // Set word wrap to 50 characters
            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = 'Password Reset Code';
            $mail->Body    = $message;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            //newly added codes below
            $count = 1;
            $settime = date('Y-m-d H:i:s');
            $qry = $conn->query("Select * from all_users WHERE email = '$email' limit 1");

            if ($qry->num_rows > 0) {

                $row = $qry->fetch_assoc();
                $count += $row['count'];
                // DATE CHECK REMAINING TIME 

                $seconds = strtotime($row['datetimeSL']) - time();

                $days = floor($seconds / 86400);
                $seconds %= 86400;
                $hours = floor($seconds / 3600);
                $seconds %= 3600;
                $minutes = floor($seconds / 60);
                $seconds %= 60;
                $flags = true;
                if ($minutes <= 0) {
                    //false if no time remain in datetimesl
                    $flags = false;
                }

                if ($row['count'] == 2 && $flags == true) {
                    $errors['email'] = "This email exceed maximum OTP-code to be sent, try again next " . $minutes . " minutes ";
                } else {

                    if (!$mail->send()) {
                        $errors['otp-error'] = "Failed while sending code! , Mailer Error: " . $mail->ErrorInfo;
                    } else {
                        $info = "We've sent a passwrod reset otp to your email - $email";

                        if ($row['count'] == 2) 
                         $count = 1;
                        if ($count == 2) $settime = date('Y-m-d H:i:s', strtotime('59 minute'));

                        $add_count = "UPDATE all_users SET `count` = $count ,datetimeSL = '$settime' WHERE email = '$email'";
                        $run_query22 =  mysqli_query($con, $add_count);
                        //end
                        $_SESSION['info'] = $info;
                        $_SESSION['email'] = $email;
                        header('location: reset-code.php');
                    }
                    $mail->smtpClose();
                }
            }
        } else {
            $errors['db-error'] = "Something went wrong!";
        }
    } else {
        $errors['email'] = "This email address does not exist!";
    }
}

//if user click check reset otp button
if (isset($_POST['check-reset-otp'])) {
    $_SESSION['info'] = "";
    if ($otp_code) {
        # code...
    }
    $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
    $check_code = "SELECT * FROM all_users WHERE code = $otp_code";
    $code_res = mysqli_query($con, $check_code);
    if (mysqli_num_rows($code_res) > 0) {
        $fetch_data = mysqli_fetch_assoc($code_res);
        $email = $fetch_data['email'];
        $_SESSION['email'] = $email;
        $info = "Please create a new password that you don't use on any other site.";
        $_SESSION['info'] = $info;
        header('location: new-password.php');
        exit();
    } else {
        $errors['otp-error'] = "You've entered incorrect code!";
    }
}

//if user click change password button
if (isset($_POST['change-password'])) {
    $_SESSION['info'] = "";
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
    if ($password !== $cpassword) {
        $errors['password'] = "Confirm password not matched!";
    } else {
        $code = 0;
        $email = $_SESSION['email']; //getting this email using session
        $encpass = md5($password);
        $update_pass = "UPDATE all_users SET code = $code, password = '$encpass' WHERE email = '$email'";
        $run_query = mysqli_query($con, $update_pass);
        if ($run_query) {
            $info = "Your password changed. Now you can login with your new password.";
            $_SESSION['info'] = $info;
            header('Location: password-changed.php');
        } else {
            $errors['db-error'] = "Failed to change your password!";
        }
    }
}

// ob_end_flush()
