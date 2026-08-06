<?php
// Always start session to manage login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$servername = "localhost"; // generally localhost
$username = "root";        // XAMPP/WAMP default
$password = "";            // default blank
$dbname = "enewsdb";       // DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
