<?php
session_start();
include('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog Filter Engine - Brush & Canvas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container container-small" style="margin-top: 40px;">
    <div class="form-card" style="max-width: 500px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2>Course Catalog Search</h2>
            <p class="subtitle">Query our live database parameters to discover specialized studio classes</p>
        </div>

        <form action="results.php" method="POST">
            
            <div class="form-group">
                <label for="searchtype">Filter Matrix Column</label>
                <select id="searchtype" name="searchtype" required>
                    <option value="">Choose Filter Metric...</option>
                    <option value="cname">Class Name / Title</option>
                    <option value="dept">Studio Department</option>
                    <option value="instructor">Instructor Surname</option>
                    <option value="modality">Modality Format</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="searchterm">Enter Search Terms Keyword</label>
                <input type="text" id="searchterm" name="searchterm" placeholder="e.g. Drawing, Smith, Online..." required>
            </div>

            <button type="submit" class="save-btn" style="width: 100%; padding: 14px; background-color: #d97706;">
                Execute Database Search
            </button>
        </form>
    </div>
</div>

</body>
</html>