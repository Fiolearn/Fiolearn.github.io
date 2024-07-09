<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Information</title>
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
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
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
        .message {
            margin-top: 10px;
            text-align: center;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>Update Information</h1>
    </div>
    <br>
    <form id="update-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div id="success-message" class="message success hidden"></div>

        <label for="studentnumber">Student Number:</label>
        <input type="text" id="studentnumber" name="studentnumber" required>

        <label for="course">Course:</label>
        <select id="course" name="course" required>
            <option value="BSIT">Information Technology</option>
            <option value="BSCS">Computer Science</option>
        </select>

        <label for="image">Upload New Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <input type="submit" value="Update">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $servername = "localhost"; // o kung anuman ang iyong server address
        $username = "root"; // I-update ang iyong username kung iba
        $password = ""; // I-update ang iyong password kung iba
        $dbname = "earist_database"; // I-update ang pangalan ng iyong database

        // Lumikha ng koneksyon sa database
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Suriin kung may error sa koneksyon
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Kunin ang impormasyon mula sa form
        if (isset($_POST['studentnumber']) && isset($_POST['course'])) {
            $studentnumber = $_POST['studentnumber'];
            $course = $_POST['course'];
            
            // Default SQL update query
            $sql = "UPDATE students SET course='$course' WHERE studentnumber='$studentnumber'";

            // I-upload ang imahe kung mayroon
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $image_name = $_FILES['image']['name'];
                $image_tmp_name = $_FILES['image']['tmp_name'];
                $image_size = $_FILES['image']['size'];
                
                $image_destination = 'uploads/' . $image_name; // Tiyakin na ang "uploads" ay may tamang pahintulot sa pagsulat (write permission)
                
                // Check file size
                if ($image_size > 500000) {
                    echo "Sorry, your file is too large.";
                } else {
                    // Allow only certain file formats
                    $allowed_formats = array('jpg', 'jpeg', 'png');
                    $image_file_extension = strtolower(pathinfo($image_destination, PATHINFO_EXTENSION));
                    if (in_array($image_file_extension, $allowed_formats)) {
                        if (move_uploaded_file($image_tmp_name, $image_destination)) {
                            // Update SQL query with image
                            $sql = "UPDATE students SET course='$course', image='$image_name' WHERE studentnumber='$studentnumber'";
                        } else {
                            echo "Error uploading image.";
                        }
                    } else {
                        echo "Invalid file format. Only JPG, JPEG, and PNG files are allowed.";
                    }
                }
            }

            // Execute the SQL query
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
        }

        // Isara ang koneksyon sa database
        $conn->close();
    }
    ?>
</body>
</html>
