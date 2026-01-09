<?php
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Search for the user in the database
    $sql = "SELECT * FROM Employees WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if ($password === $user['Password']) {
            session_start();
            $_SESSION['username'] = $user['Username'];
            $_SESSION['isAdmin'] = $user['IsAdmin'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('No user found with this username.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="container-fluid px-0 d-flex justify-content-center align-items-center vh-100 bg-color-e6e5e5">
        <div class="login-container d-flex justify-content-center flex-column align-items-center">
            <img src="img\logo.jpg" alt="" class="width-35per mt-2">
            <div class="font-size-20px font-weight-800 mt-4">Employee Login</div>
            <form action="login.php" method="POST" class="mt-4">
                <div class="mb-3">
                    <label for="username" class="form-label color-b5b5b5 font-size-13px">Username</label>
                    <input type="text" id="username" name="username" class="form-control font-size-12px" placeholder="Enter your username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label color-b5b5b5 font-size-13px">Password</label>
                    <input type="password" id="password" name="password" class="form-control font-size-13px" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn w-100 black-btn mt-2">Login</button>
            </form>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>
