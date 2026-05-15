<html>
<head>
  <title>Art Class Entry Results</title>
</head>
<body>
<h1>Art Class Entry Results</h1>

<?php
  // create short variable names
  $instructor=$_POST['instructor'];
  $cname=$_POST['cname'];
  $cid=$_POST['cid'];
  $description=$_POST['description'];
  $seat=$_POST['seat'];

  if (!$cid || !$instructor || !$cname || !$description || !$seat) {
     echo "You have not entered all the required details.<br />"
          ."Please go back and try again.";
     exit;
  }

  if (!get_magic_quotes_gpc()) {
    $cid = addslashes($cid);
    $instructor = addslashes($instructor);
    $cname = addslashes($cname);
    $description = addslashes($description);
    $seat = doubleval($seat);
  }

  @ $db = new mysqli('localhost', 'konopkj1_chickens', 'chickens123', 'konopkj1_project');

  if (mysqli_connect_errno()) {
     echo "Error: Could not connect to the database. Please try again later.";
     exit;
  }
  
  // Check if the course with the specified ID already exists
  $check_query = "SELECT * FROM class WHERE cid = '".$cid."'";
  $check_result = $db->query($check_query);

  if ($check_result->num_rows > 0) {
    // If the course exists, update its details
    $update_query = "UPDATE class SET instructor='".$instructor."', cname='".$cname."', description='".$description."', seat='".$seat."' WHERE cid='".$cid."'";
    $update_result = $db->query($update_query);

    if ($update_result) {
        echo  $db->affected_rows." class updated in the database.";
    } else {
        echo "An error has occurred. The class was not updated.";
    }
  } else {
    echo "The specified course ID does not exist. Please check the details and try again.";
  }

  $db->close();
?>
</body>
</html>
