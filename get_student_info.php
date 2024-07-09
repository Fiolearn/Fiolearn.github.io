<?php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['studentnumber'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "earist_database";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $studentnumber = $conn->real_escape_string($_GET['studentnumber']);
    $sql = "SELECT first_name, middle_name, last_name, course, studentnumber, image FROM students WHERE studentnumber = '$studentnumber'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();

        $timestamp = date('Y-m-d H:i:s');
        $log_sql = "SELECT * FROM attendance WHERE studentnumber = '$studentnumber' ORDER BY id DESC LIMIT 1";
        $log_result = $conn->query($log_sql);

        if ($log_result->num_rows > 0) {
            $last_entry = $log_result->fetch_assoc();
            if ($last_entry['action'] == 'in') {
                $log_action = 'out';
            } else {
                $log_action = 'in';
            }
        } else {
            $log_action = 'in';
        }

        $log_insert_sql = "INSERT INTO attendance (studentnumber, action, timestamp) VALUES ('$studentnumber', '$log_action', '$timestamp')";
        $conn->query($log_insert_sql);

        echo json_encode(['success' => true, 'student' => $student, 'action' => $log_action]);
    } else {
        echo json_encode(['success' => false]);
    }

    $conn->close();
}
?>
