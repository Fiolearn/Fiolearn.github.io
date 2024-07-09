<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EARIST Student Database</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('earist.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }
        #header {
            background-color: black;
            color: white;
            padding: 20px;
            text-align: center;
        }
        #search-box {
            margin-top: 20px;
            text-align: center;
        }
        #student-info {
            text-align: left;
            margin-top: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: none;
            max-width: 400px;
            margin: 20px auto;
        }
        #student-info.show {
            display: flex;
        }
        #student-info img {
            width: 200px;
            height: auto;
            border: 2px solid black;
            border-radius: 5px;
            margin-right: 20px;
        }
        #student-info h2 {
            font-size: 24px;
            margin-top: 0;
        }
        #student-info p {
            font-size: 18px;
            margin: 5px 0;
        }
        button[type="submit"] {
            display: inline-block;
            margin: 5px;
            color: white;
            background-color: #4CAF50;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>EARIST Student Verification</h1>
        <div id="search-box">
            <input type="text" id="student-number" placeholder="Enter Student Number">
        </div>

    </div>
    <div id="student-info"></div>
    <center>
<button type="submit"><a href = "attendance_log.php">Student Records</a>
</button>
    </center>
    <script>
        document.getElementById('student-number').addEventListener('input', function() {
            var studentNumber = this.value.trim();
            if (studentNumber !== '') {
                fetchStudentInfo(studentNumber);
            } else {
                document.getElementById('student-info').classList.remove('show');
            }
        });

        function fetchStudentInfo(studentNumber) {
            fetch('get_student_info.php?studentnumber=' + studentNumber)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayStudentInfo(data.student);
                } else {
                    document.getElementById('student-info').innerHTML = '<p>No student found with that student number.</p>';
                    document.getElementById('student-info').classList.add('show');
                }
                setTimeout(hideStudentInfo, 5000);
            })
            .catch(error => console.error('Error:', error));
        }

        function displayStudentInfo(student) {
            var studentInfoDiv = document.getElementById('student-info');
            studentInfoDiv.innerHTML = `
                <img src="uploads/${student.image}" alt="Student Image">
                <div>
                    <h2>Student Profile</h2>
                    <p><strong>${student.first_name} ${student.last_name}</strong></p>
                    <p><strong>${student.course}</strong></p>
                    <p><strong>${student.studentnumber}</strong></p>
                </div>
            `;
            studentInfoDiv.classList.add('show');
        }

        function hideStudentInfo() {
            var studentInfoDiv = document.getElementById('student-info');
            studentInfoDiv.classList.remove('show');
            document.getElementById('student-number').value = '';
        }
    </script>
</body>
</html>

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

    $studentnumber = $_GET['studentnumber'];
    $sql = "SELECT first_name, last_name, course, studentnumber, image FROM students WHERE studentnumber = '$studentnumber'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        echo json_encode(['success' => true, 'student' => $student]);
    } else {
        echo json_encode(['success' => false]);
    }

    $conn->close();
}
?>
