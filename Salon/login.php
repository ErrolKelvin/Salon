<?php
include('db.php');
session_start();

$invalidLogin = false; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        header('Location: home.php');
        exit();
    } else {
        $invalidLogin = true; // Set the flag to true if login is invalid
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leah's M Queue | Login</title>
    <link rel="stylesheet" href="styles/login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="icons/logo1.png" alt="Logo" class="logo">
        </div>
        <form action="login.php" method="POST" class="form">
            <h2>Login</h2>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="submit-button">Login</button>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showInvalidLoginAlert() {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Credentials',
                text: 'Please check your username and password.',
                confirmButtonText: 'Try Again'
            });
        }

        <?php if ($invalidLogin): ?>
            showInvalidLoginAlert();
        <?php endif; ?>
    </script>
</body>
</html>
