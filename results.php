<html>
    <head>
        <title>Search Results</title>
    </head>
    <body>
        <h1>Search Results</h1>
        <?php
            $searchtype = $_POST['searchtype'];
            $searchterm = trim($_POST['searchterm']);

            if (!$searchtype || !$searchterm) {
                echo 'You have not entered search details. Please go back and try again.';
                exit;
            }

            if(!get_magic_quotes_gpc()) {
                $searchtype = addslashes($searchtype);
                $searchterm = addslashes($searchterm);
            }

            @ $db = new mysqli('localhost', 'konopkj1_chicken', 'chickens123', 'konopkj1_project');

            if (mysqli_connect_errno()) {
                echo 'Error: Could not connect to the database. Please try again later.';
                exit;
            }

            $query = "SELECT * FROM CLASS WHERE $searchtype LIKE '%searchterm%'";
            $result = $db->query($query);

            if (!$result) 
            {
                dir("Query failed: " . $de->error);
            }

            $num_results = $results->num_rows;
            echo "<p>Number of classes found: $num_results</p>";

            if($num_results > 0) {
                for ($i = 0; $i < $num_results; $i++) {
                    $row = $result->fetch_Assoc();
                    echo"<p><strong>" . ($i+1) . ". Class ID: ";
                    echo htmlspecialchars($row['cid']);
                    echo "</strong><br />Class Name: ";
                    echo stripslashes($row['cname']);
                    echo "<br />Credits: ";
                    echo stripslashes($row['credit']);
                    echo "<br />Department: ";
                    echo stripslashes($row['dept']);
                    echo "<br />Instructor: ";
                    echo stripslashes($row['instructor']);
                    echo "<br />Description: ";
                    echo stripslashes($row['description']);
                    echo "<br />Modality: ";
                    echo stripslashes($row['modality']);
                    echo "<br />Available Seats: ";
                    echo stripslashes($row['seats']);
                    echo "</p>";
                }
                $result->free();
            } else {
                "<p>No class found.</p>";
            }
            $db->close();
        ?>
    </body>
</html>