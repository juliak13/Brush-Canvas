<?php
// Function to register a new user
function registerUser($username, $password) {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Store $username and $hashed_password in the database
    // (Database interactions would typically involve SQL queries)
    // For simplicity, we'll just display the information here
    echo "User registered successfully:<br>";
    echo "Username: $username<br>";
    echo "Hashed Password: $hashed_password<br>";
}

// Function to log in a user
function loginUser($username, $entered_password) {
    // Retrieve hashed password from the database based on the provided username
    // (Database interactions would typically involve SQL queries)
    // For simplicity, we'll use a hardcoded value
    $stored_hashed_password = '$2y$10$HdOaW7e5DWe.Ev2oM5tR/.6lIqIDlNqONtzhcji38v9xFiNfJKuzO';

    // Verify the entered password against the stored hash
    if (password_verify($entered_password, $stored_hashed_password)) {
        // Passwords match, grant access
        echo "Login successful!";
    } else {
        // Passwords do not match, deny access
        echo "Login failed. Incorrect username or password.";
    }
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["register"])) {
        // Process user registration
        $username = $_POST["username"];
        $password = $_POST["password"];
        registerUser($username, $password);
    } elseif (isset($_POST["login"])) {
        // Process user login
        $username = $_POST["username"];
        $entered_password = $_POST["password"];
        loginUser($username, $entered_password);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authentication Example</title>
</head>
<body>
    <h1>User Authentication Example</h1>

    <!-- User Registration Form -->
    <h2>Register</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" name="register" value="Register">
    </form>

    <!-- User Login Form -->
    <h2>Login</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
</body>
</html>
