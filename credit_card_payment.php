<?php
include 'components/connect.php';
session_start();

if (!isset($_SESSION['order_data'])) {
    header('location:checkout.php');
    exit;
}

$order_data = $_SESSION['order_data'];

if (isset($_POST['pay'])) {
    $card_number = $_POST['card_number'];
    $expiry_month = $_POST['expiry_month'];
    $expiry_year = $_POST['expiry_year'];
    $card_holder = $_POST['card_holder'];

    $card_number = filter_var($card_number, FILTER_SANITIZE_STRING);
    $expiry_month = filter_var($expiry_month, FILTER_SANITIZE_STRING);
    $expiry_year = filter_var($expiry_year, FILTER_SANITIZE_STRING);
    $card_holder = filter_var($card_holder, FILTER_SANITIZE_STRING);

    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Card Payment</title>
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

        /* Pop-up styles */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .popup .btn-close {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .popup .btn-close:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

<div class="home-bg">

<section class="form-container">

    <form action="" method="POST">
        <h3>Credit Card Payment</h3>
        <div class="inputBox">
            <span>Card Number :</span>
            <input type="text" name="card_number" placeholder="Enter card number" class="box" maxlength="16" required>
        </div>
        <div class="inputBox">
            <span>Expiry Month :</span>
            <input type="text" name="expiry_month" placeholder="MM" class="box" maxlength="2" required>
        </div>
        <div class="inputBox">
            <span>Expiry Year :</span>
            <input type="text" name="expiry_year" placeholder="YYYY" class="box" maxlength="4" required>
        </div>
        <div class="inputBox">
            <span>Card Holder Name :</span>
            <input type="text" name="card_holder" placeholder="Card holder name" class="box" required>
        </div>
        <input type="submit" name="pay" class="btn" value="Pay Now">
    </form>

    <!-- Pop-up for OTP -->
    <div class="popup" id="otp-popup">
        <div class="popup-content">
            <h4>Your OTP is: <?php echo $_SESSION['otp']; ?></h4>
            <form action="otp_verification.php" method="POST">
                <div class="inputBox">
                    <span>Enter OTP :</span>
                    <input type="text" name="otp" placeholder="Enter OTP" class="box" maxlength="6" required>
                </div>
                <input type="submit" name="verify" class="btn" value="Verify OTP">
            </form>
            <button class="btn-close" onclick="closePopup()">Close</button>
        </div>
    </div>

    <script>
        // Display OTP pop-up after payment
        <?php if (isset($otp)) { ?>
            document.getElementById('otp-popup').style.display = 'flex';
        <?php } ?>

        // Close the OTP pop-up
        function closePopup() {
            document.getElementById('otp-popup').style.display = 'none';
        }
    </script>

    </section>

</div>

</body>
</html>
