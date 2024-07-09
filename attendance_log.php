<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "earist_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT a.studentnumber, s.first_name, s.last_name, a.action, a.timestamp 
        FROM attendance a 
        JOIN students s ON a.studentnumber = s.studentnumber
        WHERE s.first_name LIKE '%$search%' 
        OR s.last_name LIKE '%$search%' 
        OR a.studentnumber LIKE '%$search%'
        ORDER BY a.timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Log</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('earist.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        #header {
            background-color: black;
            color: white;
            padding: 20px;
            text-align: center;
        }
        #attendance-log {
            margin: 20px auto;
            max-width: 800px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        #search-box {
            text-align: center;
            margin-bottom: 20px;
        }
        #search-box input {
            padding: 10px;
            width: 80%;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>Attendance Log</h1>
    </div>
    <div id="attendance-log">
        <div id="search-box">
            <input type="text" id="search" placeholder="Search by Name or Student Number" oninput="searchAttendance()">
        </div>
        <table id="attendance-table">
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['studentnumber']}</td>
                                <td>{$row['first_name']}</td>
                                <td>{$row['last_name']}</td>
                                <td>{$row['action']}</td>
                                <td>{$row['timestamp']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No attendance records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchAttendance() {
            var search = document.getElementById('search').value;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'attendance_log.php?search=' + search, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');
                    var table = doc.querySelector('#attendance-table tbody').innerHTML;
                    document.querySelector('#attendance-table tbody').innerHTML = table;
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
