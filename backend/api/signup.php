<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $user_type = $_POST["user_type"];
    $secret_code = $_POST["secret_code"];

    

   
    if (($user_type == 'admin' || $user_type == 'instructor') && $secret_code !== 'your_secret_code') {
        echo "Invalid secret code for admin or instructor registration";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $db_conn = new mysqli('localhost', 'konopkj1_chicken', 'chickens123', 'konopkj1_project');

    if ($db_conn->connect_error) {
        die("Connection failed: " . $db_conn->connect_error);
    }

    $query = "INSERT INTO users (username, password, user_type) VALUES ('$username', '$hashed_password', '$user_type')";

    if ($db_conn->query($query) === TRUE) {
        echo "User registered successfully";
    } else {
        echo "Error: " . $query . "<br>" . $db_conn->error;
    }

    $db_conn->close();
}
?>
