<?php
session_start();

if (isset($_POST['userid']) && isset($_POST['password'])) {
    // if the user has just tried to log in
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    $db_conn = new mysqli('localhost', 'webauth', 'webauth', 'auth');

    if (mysqli_connect_errno()) {
        echo 'Connection to database failed:' . mysqli_connect_error();
        exit();
    }

    $query = "SELECT * FROM authorised_users WHERE name='$userid' AND password=sha1('$password')";
    $result = $db_conn->query($query);

    if ($result->num_rows > 0) {
        // user exists in the database
        $row = $result->fetch_assoc();
        $_SESSION['valid_user'] = $userid;
        $_SESSION['user_role'] = $row['role']; // Assuming you have a 'role' column in your database
    }

    $db_conn->close();
}
?>
<html>

<body>
    <h1>Home page</h1>
    <?php
    if (isset($_SESSION['valid_user'])) {
        echo 'You are logged in as: ' . $_SESSION['valid_user'] . ' <br />';
        echo 'User role: ' . $_SESSION['user_role'] . '<br />';
        echo '<a href="logout.php">Log out</a><br />';
    } else {
        if (isset($userid)) {
            // if they've tried and failed to log in
            echo 'Could not log you in.<br />';
        } else {
            // they have not tried to log in yet or have logged out
            echo 'You are not logged in.<br />';
        }

        // provide form to log in
        echo '<form method="post" action="authmain.php">';
        echo '<table>';
        echo '<tr><td>Userid:</td>';
        echo '<td><input type="text" name="userid"></td></tr>';
        echo '<tr><td>Password:</td>';
        echo '<td><input type="password" name="password"></td></tr>';
        echo '<tr><td colspan="2" align="center">';
        echo '<input type="submit" value="Log in"></td></tr>';
        echo '</table></form>';
    }
    ?>
    <br />
    <?php
    // Display links based on user role
    if (isset($_SESSION['user_role'])) {
        $user_role = $_SESSION['user_role'];
        echo '<a href="members_only.php">Members section</a><br />';

        // Additional links based on user role
        if ($user_role === 'admin') {
            echo '<a href="admin_section.php">Admin section</a><br />';
        } elseif ($user_role === 'instructor') {
            echo '<a href="instructor_section.php">Instructor section</a><br />';
        } elseif ($user_role === 'student') {
            echo '<a href="student_section.php">Student section</a><br />';
        }
    }
    ?>
</body>

</html>
