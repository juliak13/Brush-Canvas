<?php
    require('instructor_header.php');
    require_once ('config.php');
    
    $query="SELECT c.* FROM class JOIN instructor_classes ON class.cid = instructor_classes.cid JOIN users ON lname = instructor_classes.lname WHERE users.username = '$username'";

    $result=$conn->query($query);

    if ($result->num_rows > 0) {
        $class = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $class=[];
    }
    $db->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf=8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Instructor Home</title>
    </head>
    <body>
        <h2>Instructor Home</h2>
        <?php
            if (!empty($classes)):
        ?>
        <ul>
            <?php
                foreach ($clases as $class):
            ?>
            <li>
                <a href="class_details.php?cid=<?=$class['cid']?>">
                <?+$class['cname']?>
            </a>
            </li>
            <?php   
                endforeach;
            ?>
        </ul>
        <?php   
            else:
        ?>
        <p>No classes found for this instructor.</p>
        <?php
            endif;
        ?>
    </body>
</html>