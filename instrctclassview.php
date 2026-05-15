<?php
session_start();

// Replace this with your actual database connection logic
$db_conn = new mysqli('localhost', 'your_db_username', 'your_db_password', 'your_db_name');

if ($db_conn->connect_error) {
    die("Connection failed: " . $db_conn->connect_error);
}

// Replace 'your_table_name' with your actual table name for classes
$query = "SELECT * FROM classes WHERE instructor_id = '{$_SESSION['valid_user']}'";
$result = $db_conn->query($query);

$classes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

$db_conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard</title>
    <style>
        /* Add your CSS styles here */
        .class-card {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
            width: 300px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>Instructor Dashboard</h1>

    <p>Welcome, <?php echo $_SESSION['valid_user']; ?>!</p>

    <h2>Your Classes</h2>

    <?php if (!empty($classes)) : ?>
        <?php foreach ($classes as $class) : ?>
            <div class="class-card">
                <h3><?php echo $class['class_name']; ?></h3>
                <p>Class ID: <?php echo $class['class_id']; ?></p>
                
                <!-- Retrieve and display the list of students in this class -->
                <?php
                $students_query = "SELECT * FROM enrolled_students WHERE class_id = '{$class['class_id']}'";
                $students_result = $db_conn->query($students_query);

                if ($students_result->num_rows > 0) {
                    echo '<p>Students in this class:</p>';
                    echo '<ul>';
                    while ($student_row = $students_result->fetch_assoc()) {
                        echo '<li>' . $student_row['student_name'] . '</li>';
                        // Add more student details as needed
                    }
                    echo '</ul>';
                } else {
                    echo '<p>No students in this class.</p>';
                }
                ?>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No classes found.</p>
    <?php endif; ?>

    <p><a href="logout.php">Log out</a></p>
</body>
</html>
