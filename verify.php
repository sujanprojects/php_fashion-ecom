<?php

require('config.php');
include 'components/connect.php';

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

session_start();


if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
 }else{
    $user_id = '';
    header('location:user_login.php');
 };

$success = true;

$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false)
{
    $api = new Api($keyId, $keySecret);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true)
{
    $userData = $_SESSION['user_data'];
    $name = $userData['name'];
    $number = $userData['number'];
    $email = $userData['email'];
    $method = $userData['method'];
    $address = $userData['address'];
    $total_products = $userData['total_products'];
    $total_price = $userData['total_price'];

    

    $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){

      $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'order placed successfully!';
      unset($_SESSION['userData']);
   }else{
      $message[] = 'your cart is empty';
   }

    
    $html = "<p>Your payment was successful. Redirecting in 5 seconds.</p>
             <p>Payment ID: {$_POST['razorpay_payment_id']}</p>";
    sleep(5);
    header("Location : home.php");
}
else
{
    $html = "<p>Your payment failed</p>
             <p>{$error}</p>";
}

echo $html;
