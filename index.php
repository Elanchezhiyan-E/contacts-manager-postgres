<?php
require 'functions.php';

// Start session (if not already started)
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = 'Please enter both username and password.';
    } else {
        // Authenticate user
        if (authenticateUser($username, $password)) {
            // Login successful, redirect to contacts.php
            $_SESSION['username'] = $username; // Store username in session
            header('Location: contacts.php');
            exit;
        } else {
            // Login failed
            $message = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contacts</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <h2>Login</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="index.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register for new user</a></p>
</body>
</html>
