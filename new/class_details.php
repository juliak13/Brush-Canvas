<?php
    require_once('config.php');

    $cid=$_GET['cid'] ?? 0;

    $query = "SELECT * FROM class WHERE cid = $cid";
    $result=$conn->query($query);

    if($result->num_rows > 0) {
        $classinfo = $result->fetch_assoc();

        $query2 = "SELECT u.* FROM users JOIN instructor_classes ON users.lname = instructor_classes.lname WHERE instructor_classes.cid = $cid";
        $result2 = $conn->query($query2);

        if($result2->num_rows > 0) {
            $instructor = $result2->fetch_assoc();
        } else {
            $instructor = null;
        }
        
        $query3 = "SELECT s.* FROM users WHERE user_type = '$student'";
        $result=$conn->query($query3);

        if ($result3->num_rows > 0) {
            $students=$result3->fetch_all(MYSQLI_ASSOC);
        } else {
            $students =[];
        }
    } else {
        $classinfo =null;
    }
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, intial-scale=1.0'>
        <title>Class Details</title>
    </head>
    <body>
        <h2>Class Details</h2>
        <?php
            if ($classinfo):
        ?>
        <h3><?=$classinfo['cname']?></h3>
        <p>Credits: <?=$classinfo['credits']?></p>
        <p>Department: <?=$classinfo['dept']?></p>
        <p>Instructor:<?=$instructor['fname']?><?=$instructor['lname']?></p>
        <p>Modality:<?=$classinfo['modality']?></p>
        <p>Available Seats:<?=$classinfo['seats']?></p>

        <?php
            if(!empty($students)):
        ?>
        <h4>Enrolled Students</h4>
        <ul>
            <?php
                foreach($students as $student):
            ?>
            <li>
                <?=$student['id']?>
                <?=$student['fname']?> <?=$student['lname']?> - <?=$student['email']?>]
            </li>
            <?php
                endforeach;
            ?>
        </ul>
        <?php
            else:
        ?>
        <p>No students enrolled in this class.</p>
        <?php
            endif;
        ?>
    </body>
</html>