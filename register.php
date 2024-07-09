<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
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
        select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        input[type="file"] {
            margin-bottom: 10px;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .hidden {
            display: none;
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
        .name-container {
            display: flex;
            justify-content: space-between;
        }
        .name-container input {
            width: 48%;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>EARIST Student Registration</h1>
    </div>
    <br>
    <form id="registration-form" action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <div id="error-message" class="message error hidden">Please input your school email ending with .bscs@gmail.com</div>
        <div id="success-message" class="message success hidden">New record created successfully</div>
        <div id="student-exist-message" class="message error hidden">Student number already exists.</div>
        <div id="otp-error-message" class="message error hidden">Invalid OTP. Please try again.</div>

        <div class="name-container">
            <input type="text" id="first-name" name="first-name" placeholder="First Name" required>
            
            <input type="text" id="middle-name" name="middle-name" placeholder="Middle Name" required>

            <input type="text" id="last-name" name="last-name" placeholder="Last Name" required>
        </div>

        <label for="course">Course:</label>
        <select id="course" name="course" required>
            <option value="BSIT">Information Technology</option>
            <option value="BSCS">Computer Science</option>
        </select>

        <label for="studentnumber">Student Number:</label>
        <input type="text" id="studentnumber" name="studentnumber" required>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>

        <center><button type="button" onclick="sendOTP()">Send OTP</button></center>

        <div id="otp-popup" class="hidden">
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp">
        </div>
        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <input type="submit" value="Register">
        <br>
        <center>
        <p>Already have an account?<a href="app.php">Login Here!</a>
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

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "earist_database";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $firstName = $_POST['first-name'];
        $middleName = $_POST['middle-name'];
        $lastName = $_POST['last-name'];
        $course = $_POST['course'];
        $studentnumber = $_POST['studentnumber'];
        $email = $_POST['email'];

        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_error = $_FILES['image']['error'];

        if ($image_error === 0) {
            $image_destination = 'uploads/' . $image_name;

            if ($image_size > 500000) {
                echo "Sorry, your file is too large.";
            } else {
                $allowed_formats = array('jpg', 'jpeg', 'png');
                $image_file_extension = strtolower(pathinfo($image_destination, PATHINFO_EXTENSION));
                if (in_array($image_file_extension, $allowed_formats)) {
                    if (move_uploaded_file($image_tmp_name, $image_destination)) {
                        $check_query = "SELECT * FROM students WHERE studentnumber='$studentnumber'";
                        $result = $conn->query($check_query);

                        if ($result->num_rows == 0) {
                            $sql = "INSERT INTO students (first_name, last_name, middle_name, course, studentnumber, email, image)
                            VALUES ('$firstName', '$lastName', '$middleName', '$course', '$studentnumber', '$email', '$image_name')";

                            if ($conn->query($sql) === TRUE) {
                                echo '<script>
                                    var successMessage = document.getElementById("success-message");
                                    successMessage.classList.remove("hidden");
                                    setTimeout(function() {
                                        successMessage.classList.add("hidden");
                                    }, 3000);
                                </script>';
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        } else {
                            echo '<script>
                                var studentExistMessage = document.getElementById("student-exist-message");
                                studentExistMessage.classList.remove("hidden");
                                setTimeout(function() {
                                    studentExistMessage.classList.add("hidden");
                                }, 3000);
                            </script>';
                        }
                    } else {
                        echo "Error uploading image.";
                    }
                } else {
                    echo "Invalid file format. Only JPG, JPEG, and PNG files are allowed.";
                }
            }
        } else {
            echo "Error uploading image.";
        }

        $conn->close();
    }
    ?>

    <script>
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
