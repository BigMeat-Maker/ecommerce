<?php

$username = $_POST["username"];
$password = $_POST["password"];

session_start();

require_once(__DIR__."/../config/Directories.php");
include("../config/DatabaseConnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $db = new DatabaseConnect();
    $conn = $db->connectDB();

    try {
        $stmt = $conn->prepare('SELECT * FROM `users` WHERE username = :p_username');
        $stmt->bindParam(':p_username', $username);
        $stmt->execute();
        $users = $stmt->fetchAll();

        if ($users) {
            // Verify password
            if (password_verify($password, $users[0]["password"])) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = $users[0]['id'];
                $_SESSION['username'] = $users[0]['username'];
                $_SESSION['fullname'] = $users[0]['fullname'];
                $_SESSION['is_admin'] = $users[0]['is_admin'];

                // Redirect to homepage
                header("location: ".BASE_URL."index.php");
                exit;
            } else {
                // Incorrect password
                $_SESSION["error"] = "Incorrect Password";
                header("location: " .BASE_URL."login.php");
                exit;
            }
        } else {
            // User not found
            $_SESSION["error"] = "User does not exist";
            header("location: ".BASE_URL."login.php");
            exit;
        }
    } catch (Exception $e) {
        // Handle database connection errors
        $_SESSION["error"] = "Connection Failed: " . $e->getMessage();
        exit;
    }
}
?>