<?php
    require('admin_header.php');
    require_once "config.php";

    $query = "SELECT id, fname, lname, username, email FROM users WHERE user_type ='$student'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $users = [];
    }

    $conn->close();
    ?>

