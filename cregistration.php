<?php
session_start();

// Replace this with your actual database connection logic
$db_conn = new mysqli('localhost', 'konopkj1_chicken', 'chickens123', 'konopkj1_project');

if ($db_conn->connect_error) {
    die("Connection failed: " . $db_conn->connect_error);
}

// Handle class registration if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["class_id"])) {
    $class_id = $_POST["class_id"];
    $student_id = $_SESSION['valid_user'];

    // Check if the student is already enrolled in the class
    $check_query = "SELECT * FROM STUDENTS WHERE cid = '$cid' AND sid = '$sid'";
    $check_result = $db_conn->query($check_query);

    if ($check_result->num_rows == 0) {
        // If not enrolled, register the student for the class
        $register_query = "INSERT INTO STUDENTS (cid, sid) VALUES ('$cid', '$sid')";
        $register_result = $db_conn->query($register_query);

        if ($register_result) {
            $registration_message = "Successfully registered for the class!";
        } else {
            $registration_message = "Registration failed. Please try again.";
        }
    } else {
        $registration_message = "You are already enrolled in this class.";
    }
}

// Retrieve only the classes available for registration
$query = "SELECT * FROM CLASS WHERE seats > 0"; // Assuming 'seats' is the column representing available seats
$result = $db_conn->query($query);

$classes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

$db_conn->close();
?>
