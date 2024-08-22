<?php
// Start the session
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "localhost";
$username = "root";  // Default username for XAMPP
$password = "";      // Default password for XAMPP is empty
$dbname = "cureai-user";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Validate form data
    if (empty($full_name) || empty($contact) || empty($username) || empty($password)) {
        echo "Please fill in all the fields.";
    } elseif (!preg_match("/^[A-Za-z\s]+$/", $full_name)) {
        echo "Invalid full name. Only letters and spaces are allowed.";
    } elseif (!preg_match("/^[0-9]{10}$/", $contact)) {
        echo "Invalid contact number. Only 10 digits are allowed.";
    } else {
        // Encrypt the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if username already exists
        $check_sql = "SELECT * FROM `cure-user-info` WHERE username = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Username already exists. Please choose a different username.";
        } else {
            // Insert data into the database
            $sql = "INSERT INTO `cure-user-info` (full_name, contact, username, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $full_name, $contact, $username, $hashed_password);

            if ($stmt->execute()) {
                // Display success message and redirect to login page
                echo "<script>
                    alert('Account created successfully');
                    window.location.href = 'login.html';
                </script>";
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    }
}

// Close the database connection
$conn->close();
?>
