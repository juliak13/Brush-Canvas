<?php
// config.php - Master Local Database Configuration Channel
$host     = '127.0.0.1';      
$db_user  = 'root';           
$db_pass  = '';               // Leave empty if using XAMPP/Homebrew, or 'root' for MAMP
$db_name  = 'brush_canvas';   

// This creates the universal "$conn" variable
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database system sync failure: " . $conn->connect_error);
}
?>