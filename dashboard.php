<?php
session_start(); // Siguraduhing naka-start na ang session sa simula ng code

// I-check kung ang session variable na "studentnumber" ay wala o hindi set, kung gayon, i-redirect sa app.php
if(!isset($_SESSION["studentnumber"])){
    header("location:app.php");
    exit;
}

// Logout process
if(isset($_POST['logout'])) {
    // Clear all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to app.php
    header("location: app.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('earist.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            color: white;
            text-align: center;
            padding-top: 50px;
        }
        header {
            background-color: black;
            color: white;
            padding: 10px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
        }
        header a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 0 auto;
            max-width: 400px;
            margin-top: 100px; /* Magdagdag ng space mula sa header */
        }
        p{
            color: black;
        }
        
        h1 {
            margin-bottom: 20px;
        }
        img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="submit"] {
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
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .logout-btn {
            position: absolute;
            top: 10px;
            right: 20px;
        }
        button[type="logout"] {
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
        button[type="logout"]:hover {
            background-color: #45a049;
    </style>
</head>
<body>
    <header>
        <h1>Student Profile</h1>
        <form method="post">
            <button type="logout" name="logout" class="logout-btn">Logout</button>

        </form>
    </header>
    <div class="container">
        <img src="uploads/<?php echo isset($_SESSION["image"]) ? $_SESSION["image"] : ''; ?>" alt="Profile Image">
        <form>
    <p><strong>Name:</strong> <?php echo isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : ''; ?> <?php echo isset($_SESSION["middle_name"]) ? $_SESSION["middle_name"] : ''; ?> <?php echo isset($_SESSION["last_name"]) ? $_SESSION["last_name"] : ''; ?></p>
    <p><strong>Course:</strong> <?php echo isset($_SESSION["course"]) ? $_SESSION["course"] : ''; ?></p>
    <p><strong>Student Number:</strong> <?php echo isset($_SESSION["studentnumber"]) ? $_SESSION["studentnumber"] : ''; ?></p>
    <input type="submit" value="Update Information" formaction="update_info.php">
    <input type="submit" value="Generate QR Code" formaction="generate_qr.php">
    
</form>

    </div>
</body>
</html>
