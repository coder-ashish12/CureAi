<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration
$servername = "localhost";
$db_user = "root"; // Default username for XAMPP
$db_password = ""; // Default password for XAMPP (empty by default)
$dbname = "cureai-user";

// Create connection
$conn = new mysqli($servername, $db_user, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $user = trim($conn->real_escape_string($_POST['username']));
    $pass = $_POST['password'];

    // Prepare and execute query
    $sql = "SELECT * FROM `cure-user-info` WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and verify password
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            // Start a session and redirect to home page
            $_SESSION['username'] = $user;
            header("Location: home.html");
            exit(); // Ensure the script exits immediately
        } else {
            echo "<p>Invalid password.</p>";
        }
    } else {
        echo "<p>No user found with that username.</p>";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
