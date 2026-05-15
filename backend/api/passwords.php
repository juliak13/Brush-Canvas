<?php
session_start();
// User registration
$password = "user_password";

// Hash and store password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Store $hashed_password in the database

// User login
$entered_password = "user_entered_password";

// Retrieve stored hashed password from the database
$stored_hashed_password = retrieved_from_database;

// Verify the entered password against the stored hash
if (password_verify($entered_password, $stored_hashed_password)) {
    // Passwords match, grant access
    echo "Access granted!";
} else {
    // Passwords do not match, deny access
    echo "Access denied!";
}
?>