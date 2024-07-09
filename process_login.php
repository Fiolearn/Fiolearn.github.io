<?php
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database configuration
    $servername = "localhost"; // Update with your server address
    $username = "root"; // Update with your username
    $password = ""; // Update with your password
    $dbname = "earist_database"; // Update with your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve submitted data
    $studentnumber = $_POST['studentnumber'];
    $email = $_POST['email'];

    // Query to check user credentials
    $sql = "SELECT * FROM students WHERE studentnumber='$studentnumber' AND email='$email'";
    $result = $conn->query($sql);

    if($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Store user data in session variables
        $_SESSION["studentnumber"] = $row['studentnumber'];
        $_SESSION["first_name"] = $row['first_name'];
        $_SESSION["last_name"] = $row['last_name'];
        $_SESSION["course"] = $row['course'];
        $_SESSION["image"] = $row['image'];

        // Redirect to dashboard
        header("location: dashboard.php");
    } else {
        // Redirect back to login page with error message
        header("location: app.php?error=invalid");
    }

    // Close connection
    $conn->close();
}
?>
