<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "earist_database";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
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
                        $otp = rand(100000, 999999);

                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'your_email@gmail.com'; // I-update ang iyong email
                            $mail->Password = 'your_email_password'; // I-update ang iyong email password
                            $mail->SMTPSecure = 'tls';
                            $mail->Port = 587;

                            $mail->setFrom('your_email@gmail.com', 'EARIST Registration');
                            $mail->addAddress($email);

                            $mail->isHTML(true);
                            $mail->Subject = 'OTP for EARIST Registration';
                            $mail->Body    = "Your OTP for registration is <b>$otp</b>";

                            $mail->send();
                            echo 'OTP has been sent to your email.';

                            $sql = "INSERT INTO students (first_name, last_name, course, studentnumber, email, image, otp)
                            VALUES ('$first_name', '$last_name', '$course', '$studentnumber', '$email', '$image_name', '$otp')";

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
                        } catch (Exception $e) {
                            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
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
