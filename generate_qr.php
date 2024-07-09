<?php
session_start();
require_once('phpqrcode/qrlib.php'); // Siguraduhing tama ang path papunta sa phpqrcode library

// Database connection
$servername = "localhost"; // o kung anuman ang iyong server address
$username = "root"; // I-update ang iyong username kung iba
$password = ""; // I-update ang iyong password kung iba
$dbname = "earist_database"; // I-update ang pangalan ng iyong database

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION["studentnumber"])) {
    header("location:app.php");
    exit;
}

// Get student number
$studentnumber = $_SESSION["studentnumber"];

// Initialize variables
$error_message = "";
$scan_count = 0;
$success_message = "";

// Check for existing QR code
$sql = "SELECT * FROM qr_codes WHERE studentnumber = '$studentnumber'";
$result = $conn->query($sql);
$current_date = date('Y-m-d');
$one_month_ago = date('Y-m-d', strtotime('-1 month'));

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $last_scan_date = $row['last_scan_date'];
    $scan_count = $row['scan_count'];
    
    if ($scan_count >= 6 && $last_scan_date >= $one_month_ago) {
        $error_message = "You have reached the maximum number of scans for this month. Please wait until next month to generate a new QR code.";
    } elseif ($scan_count < 6 || $last_scan_date < $one_month_ago) {
        // Reset scan count if the last scan was over a month ago
        if ($last_scan_date < $one_month_ago) {
            $scan_count = 0;
        }
    }
} else {
    // If no QR code exists for the user, insert a new record
    $sql = "INSERT INTO qr_codes (studentnumber, scan_count, last_scan_date) VALUES ('$studentnumber', 0, '$current_date')";
    if (!$conn->query($sql)) {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Generate QR code
if (empty($error_message)) {
    $qr_data = "$studentnumber";
    $qr_filename = "qr_codes/$studentnumber.png";
    QRcode::png($qr_data, $qr_filename);

    // Update the database
    $scan_count++;
    $update_sql = "UPDATE qr_codes SET qr_code_file='$qr_filename', scan_count='$scan_count', last_scan_date='$current_date' WHERE studentnumber='$studentnumber'";
    if ($conn->query($update_sql) !== TRUE) {
        $error_message = "Error updating record: " . $conn->error;
    } else {
        $success_message = "QR code successfully generated! This QR code has been generated $scan_count times.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Generated</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
        }
        .header {
            width: 100%;
            background-color: black;
            color: white;
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
        }
        .qr-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            color: black;
            margin-top: 100px;
            max-width: 90%;
            width: 400px;
        }
        .qr-container h1 {
            margin-bottom: 20px;
            color: black;
        }
        .qr-container img {
            margin-bottom: 20px;
            max-width: 100%;
            height: auto;
        }
        .qr-container a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            color: white;
            background-color: black;
            text-decoration: none;
            border-radius: 5px;
        }
        @media (max-width: 600px) {
            .header {
                padding: 10px;
                font-size: 18px;
            }
            .qr-container {
                margin-top: 80px;
                padding: 15px;
            }
            .qr-container h1 {
                font-size: 24px;
            }
            .qr-container a {
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>QR Generator</h1>
    </div>
    <div class="qr-container">
        <h1>QR Generated</h1>
        <?php
        if (!empty($error_message)) {
            echo "<p>$error_message</p>";
        } else {
            if (!empty($success_message)) {
                echo "<p>$success_message</p>";
            }
            echo "<img src='$qr_filename' alt='QR Code'>";
        }
        ?>
        <br>
        <a href="dashboard.php">Go back to profile</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>

<?php
// Database connection
$servername = "localhost";
    $username = "id21053760_earistgate";
    $password = "#EaristGate28";
    $dbname = "id21053760_earistgate";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION["studentnumber"])) {
    header("location:app.php");
    exit;
}

// Get student details from session
$studentnumber = $_SESSION["studentnumber"];

// Check if student number is set
if (empty($studentnumber)) {
    die("No student number provided.");
}

// Retrieve QR code details
$sql = "SELECT * FROM qr_codes WHERE studentnumber='$studentnumber'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $scan_count = $row['scan_count'];
    $last_scan_date = $row['last_scan_date'];
    $current_date = date("Y-m-d H:i:s");

    // Check if the QR code has been scanned 6 times
    if ($scan_count >= 6) {
        // Check if one month has passed since the last scan
        $one_month_later = date("Y-m-d H:i:s", strtotime($last_scan_date . " +1 month"));
        if ($current_date < $one_month_later) {
            echo "You have reached the generating limit. Please wait until " . $one_month_later . " to scan again.";
            exit;
        } else {
            // Reset the scan count and update the last scan date
            $scan_count = 0;
            $last_scan_date = $current_date;
        }
    }

    // Increment the scan count
    $scan_count++;
    $last_scan_date = $current_date;

    // Update the database
    $update_sql = "UPDATE qr_codes SET scan_count='$scan_count', last_scan_date='$last_scan_date' WHERE studentnumber='$studentnumber'";
    if ($conn->query($update_sql) === TRUE) {
        echo "QR code successfully generated! This QR code has been generated " . $scan_count . " times.";
    } else {
        echo "Error: " . $update_sql . "<br>" . $conn->error;
    }
} else {
    echo "QR code not found.";
}

$conn->close();
?>
