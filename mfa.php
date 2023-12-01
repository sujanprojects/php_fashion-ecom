<?php

require "vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

include 'components/connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer autoload.php file
require 'vendor/autoload.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

function generateOTP() {
    // Declare a static variable
    static $otp;

    // Check if the OTP has already been generated
    if (!$otp) {
        // Generate a new OTP only if it hasn't been generated before
        $otp = rand(100000, 999999);
    }

    return $otp;
}

if(!isset($_SESSION['email_sent'])){
    $otp = generateOTP();
    $_SESSION['otp'] = $otp;
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'testingphase38@gmail.com'; // Your Gmail address
    $mail->Password   = "lcfl illi alsk xcsm"; // Your Gmail password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender and recipient settings
    $mail->setFrom('testing38@gmail.com', 'Fashion App');
    $mail->addAddress($_SESSION['email_to_verify'], 'Email');

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'OTP to login in to your application.';
    $mail->Body    = 'Your OTP to login to the application is '.$otp;

    try {
        // Send email
        $mail->send();
        echo 'Email has been sent.';
        $_SESSION['email_sent'] = 1;
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
else{
    unset($_SESSION['email_sent']);
}



if(isset($_POST['submit'])){

   if($_SESSION['otp'] == $_POST['otp']){
    $_SESSION['user_id'] = $_SESSION['user_id_to_verify'];
    $_SESSION['email'] = $_SESSION['email_to_verify'];
    header('location:home.php');
   }
   else{
    $message[] = 'OTP INVALID - EMAIL WILL BE SENT AGAIN!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Enter MFA</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Enter MFA</h3>
      <input type="otp" name="otp" required placeholder="enter OTP" maxlength="6"  class="box">
      <input type="submit" value="login now" class="btn" name="submit">
   </form>

</section>













<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>