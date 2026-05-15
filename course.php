<html>
   <head>
      <title>New Class Registration</title>
   </head>
   <body>
      <h1>New Class Registration</h1>
      <?php
         
         $cname=($_POST['cname']);
         $credit=($_POST['credit']);
         $dept=($_POST['dept']);
         $instructor=($_POST['instructor']);
         $description=($_POST['description']);
         $modality=($_POST['modality']);
         $seats=($_POST['seats']);

         if (!$cname || !$dept || !$instructor || !$description || !$modality || !$seats) {
            echo "You have not entered all the required details.<br/>"
            ."Please go back and try again.";
            exit;
         }

         if (!get_magic_quotes_gpc()) {
            $cname = addslashes($cname);
            $credit= doubleval($credit);
            $dept = addslashes($dept);
            $instructor = addslashes($instructor);
            $description = addslashes($description);
            $modality = addslashes($modality);
            $seats = doubleval($seats);
         }

         @ $db = new mysqli('localhost', 'konopkj1_chicken', 'chickens123', 'konopkj1_project');

         if (mysqli_connect_errno()) {
            echo "Error: Could not connect to database. Please try again later.";
            exit;
         }

         $queryID = "SELECT MAX(cid) as max_id FROM CLASS WHERE dept = ?";
         $stmtID - $db->prepare($queryID);

         if($stmtID) {
            $stmtID->bind_param('s', $dept);
            $stmtID->execute();
            $stmtID->bind_result($ID);
            $stmtID->fetch();
            $stmtID->close();

            $nextCID = $ID + 1;
         } else {
            echo "An error has occured while determining the next class ID.";
            $db->close();
            exit;
         }

         $query = "INSERT into CLASS values ('".$cname"', '".$credit"', '".$dept"', '".$instructor"', '".$description"', '".$modality"', '".$seats"')";

         $result = $db->query($query);

         if ($result) {
            echo $db->affected_rows." class instered into database.";
         } else {
            echo "An error has occured. The item was not added.";
         }

         $db->close();
?>
</body>
</html>
