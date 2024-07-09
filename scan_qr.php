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

// Check for existing QR code
$sql = "SELECT * FROM qr_codes WHERE studentnumber = '$studentnumber'";
$result = $conn->query($sql);
$current_date = date('Y-m-d');
$one_month_ago = date('Y-m-d', strtotime('-1 month'));

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $last_scan_date = $row['last_scan_date'];
    $scan_count = $row['scan_count'];
    
    if ($scan_count
>= 6 && $last_scan_date >= $one_month_ago) {
        echo "You have reached the maximum number of scans for this month. Please wait until next month to generate a new QR code.";
        exit;
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
        echo "Error: " . $sql . "<br>" . $conn->error;
        exit;
    }
}

// Generate QR code
$qr_data = "$studentnumber";
$qr_filename = "qr_codes/$studentnumber.png";
QRcode::png($qr_data, $qr_filename);

// Update the database
$update_sql = "UPDATE qr_codes SET qr_code_file='$qr_filename', scan_count='$scan_count', last_scan_date='$current_date' WHERE studentnumber='$studentnumber'";
if ($conn->query($update_sql) !== TRUE) {
    echo "Error updating record: " . $conn->error;
    exit;
}

echo "QR code generated successfully!";
echo "<br><img src='$qr_filename' alt='QR Code'>";
echo "<br><a href='dashboard.php'>Go back to profile</a>";

$conn->close();
?>


<?php
//session_start();

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
        echo "QR code successfully generated! This QR code has been generated" . $scan_count . " times.";
    } else {
        echo "Error: " . $update_sql . "<br>" . $conn->error;
    }
} else {
    echo "QR code not found.";
}

$conn->close();
?>
