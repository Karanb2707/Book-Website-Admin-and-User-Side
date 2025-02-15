<?php
include 'components/connect.php';
session_start();

if (!isset($_SESSION['order_data']) || !isset($_SESSION['otp'])) {
    header('location:checkout.php');
    exit;
}

$order_data = $_SESSION['order_data'];
$otp_sent = $_SESSION['otp'];

if (isset($_POST['verify'])) {
    $otp_entered = $_POST['otp'];

    if ($otp_entered == $otp_sent) {
        $placed_on = date('Y-m-d H:i:s');
        $payment_status = 'completed'; 

        // Insert order into the database
        $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on, payment_status) VALUES(?,?,?,?,?,?,?,?,?,?)");
        $insert_order->execute([
            $order_data['user_id'],
            $order_data['name'],
            $order_data['number'],
            $order_data['email'],
            $order_data['method'],
            $order_data['address'],
            $order_data['total_products'],
            $order_data['total_price'],
            $placed_on,
            $payment_status
        ]);

        // Clear the cart
        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart->execute([$order_data['user_id']]);

        unset($_SESSION['order_data']);
        unset($_SESSION['otp']);

        header('location:orders.php');
        exit;
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        form {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s;
        }

        form:hover {
            transform: scale(1.02);
        }

        form h3 {
            font-size: 28px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .inputBox {
            margin-bottom: 1.5rem;
        }

        .inputBox span {
            display: block;
            font-size: 16px;
            color: #555;
            margin-bottom: 5px;
        }

        .inputBox .box {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .inputBox .box:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        .btn {
            background-color: #6a11cb;
            color: #ffffff;
            font-size: 18px;
            padding: 12px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn:hover {
            background-color: #2575fc;
            transform: translateY(-2px);
        }

        .error {
            color:rgb(154, 38, 255);
            font-size: 14px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="home-bg">

<section class="form-container">

    <form action="" method="POST">
        <h3>Verify OTP</h3>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <div class="inputBox">
            <span>Enter OTP :</span>
            <input type="text" name="otp" placeholder="Enter OTP" class="box" maxlength="6" required>
        </div>
        <input type="submit" name="verify" class="btn" value="Verify OTP">
    </form>

    </section>

</div>

</body>
</html>
