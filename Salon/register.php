<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Check for unique username
    $checkUsernameQuery = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($checkUsernameQuery);

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists.']);
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (full_name, username, email, phone, password) VALUES ('$full_name', '$username', '$email', '$phone', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
        // Send a success response
        echo json_encode(['status' => 'success', 'message' => 'Account successfully created.']);
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Could not register user. ' . $conn->error]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leah's M Queue | Register</title>
    <link rel="stylesheet" href="styles/register.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
    <div class="container">
        <img src="icons/logo1.png" alt="Logo" class="logo">

        <form id="registrationForm" method="POST" class="form">
            <h2>Register</h2>
            <div class="input-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="phone">Phone</label>
                <input type="text" name="phone" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            event.preventDefault();

            // Validate password length
            const password = document.getElementById('password').value;
            if (password.length < 8) {
                swal("Error!", "Password must be at least 8 characters long.", "error");
                return;
            }

            // Validate phone number format
            const phone = document.querySelector('input[name="phone"]').value;
            const phonePattern = /^(09\d{9})$/; // Pattern for a 11-digit number starting with 09
            if (!phonePattern.test(phone)) {
                swal("Error!", "Phone number must be in the format 09XXXXXXXXX (11 digits).", "error");
                return;
            }

            // Check username uniqueness via AJAX
            const formData = new FormData(this);

            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    swal("Error!", data.message, "error");
                } else if (data.status === 'success') {
                    swal("Success!", data.message, "success").then(() => {
                        // Redirect to login page or another page after successful registration
                        window.location.href = "login.php"; // Redirect to login page
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error!", "An unexpected error occurred. Please try again.", "error");
            });
        });
    </script>
</body>
</html>
