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
            font-family: 'Poppins', sans-serif;
            background-color: #eef2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
        }

        form {
            background-color: #ffffff;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 2rem;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
        }

        form h3 {
            font-size: 28px;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .inputBox {
            margin-bottom: 1.5rem;
        }

        .inputBox span {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .inputBox .box {
            width: 100%;
            padding: 12px 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
            line-height: 1.5;
        }

        .inputBox .box:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        .btn {
            background-color: #3498db;
            color: #ffffff;
            font-size: 16px;
            font-weight: 500;
            padding: 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Pop-up styles */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background: #ffffff;
            padding: 25px 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 350px;
            width: 100%;
        }

        .popup-content h4 {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .popup-content .btn-close {
            background-color: #e74c3c;
            color: white;
            font-size: 14px;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .popup-content .btn-close:hover {
            background-color: #c0392b;
        }
    </style>


</head>
<body>
    <form action="" method="POST">
        <h3>Credit Card Payment</h3>
        <div class="inputBox">
            <span>Card Number :</span>
            <input type="text" name="card_number" placeholder="Enter card number" class="box" maxlength="19" required>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cardNumberInput = document.querySelector('input[name="card_number"]');
            
            cardNumberInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/g, '');
                if (value.length > 16) value = value.slice(0, 16);
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || '';
                e.target.value = formattedValue;
            });
        });
    </script>

</body>
</html>
