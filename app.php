<?php
session_start();
if(isset($_SESSION["studentnumber"])){
    header("location:dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EARIST Student Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('earist.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }
        #header {
            background-color: black;
            color: white;
            padding: 10px;
            text-align: center;
        }
        form {
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        input[type="text"],
        input[type="password"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            margin-top: 10px;
            text-align: center;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>EARIST Student Login</h1>
    </div>
    <br>
    <form id="login-form" action="process_login.php" method="post">
        <div id="error-message" class="message error hidden">Invalid login credentials</div>

        <label for="studentnumber">Student Number:</label>
        <input type="text" id="studentnumber" name="studentnumber" required>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>
        <center><button type="button" required onclick="sendOTP()">Send OTP</button></center>

        <div id="otp-popup" class="hidden">
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" required>
        </div>

        <input type="submit" value="Login">
        <center>
        <p>No account?<a href="register.php">Register Here!</a>
    </center>
    </form>

    

         <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp'])) {
        if ($_POST['otp'] != $_SESSION['otp']) {
            echo '<script>
                var otpErrorMessage = document.getElementById("otp-error-message");
                otpErrorMessage.classList.remove("hidden");
                setTimeout(function() {
                    otpErrorMessage.classList.add("hidden");
                }, 3000);
            </script>';
            exit;
        }
    }
    ?>
    <script>
        <?php if(isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
            document.getElementById("error-message").classList.remove("hidden");
        <?php endif; ?>

         function validateForm() {
            if (!validateEmail()) {
                return false;
            }
            var otpPopup = document.getElementById('otp-popup');
            if (otpPopup.classList.contains('hidden')) {
                alert('Please verify your email with OTP.');
                return false;
            }
            return true;
        }

        function validateEmail() {
            var emailInput = document.getElementById('email').value;
            var errorMessage = document.getElementById('error-message');
            if (!emailInput.endsWith('.bscs@gmail.com')) {
                errorMessage.classList.remove('hidden');
                setTimeout(function() {
                    errorMessage.classList.add('hidden');
                }, 3000);
                return false;
            }
            return true;
        }

        function sendOTP() {
            var email = document.getElementById('email').value;
            if (!validateEmail()) {
                return;
            }
            var formData = new FormData();
            formData.append('email', email);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_otp.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('OTP sent to your email.');
                    document.getElementById('otp-popup').classList.remove('hidden');
                } else {
                    alert('Failed to send OTP. Please try again.');
                }
            };
            xhr.send(formData);
        }
    </script>
</body>
</html>