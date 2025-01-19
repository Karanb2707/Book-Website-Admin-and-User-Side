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
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        form {
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
        }

        form h3 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .inputBox {
            margin-bottom: 1.5rem;
        }

        .inputBox span {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }

        .inputBox .box {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s;
        }

        .inputBox .box:focus {
            border-color: #007bff;
        }

        .btn {
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            padding: 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .error {
            color: #f44336;
            font-size: 14px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <form action="" method="POST">
        <h3>Verify OTP</h3>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <div class="inputBox">
            <span>Enter OTP :</span>
            <input type="text" name="otp" placeholder="Enter OTP" class="box" maxlength="6" required>
        </div>
        <input type="submit" name="verify" class="btn" value="Verify OTP">
    </form>
</body>
</html>
