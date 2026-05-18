<?php
session_start();
include('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Hub - Brush & Canvas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <header class="page-header" style="text-align: center; margin-bottom: 10px; padding-bottom: 5px;">
        <h2 style="font-size: 32px;">Central Workspace Management Hub</h2>
    </header>

    <table width="100%" bgcolor="#f8f9fa" cellpadding="0" cellspacing="16" border="0" style="margin-top: 20px;">
        <tr>
            
            <td width="33.33%" valign="top" style="background: transparent;">
                <div class="class-card" style="height: 100%; min-height: 200px; margin: 0; display: flex; flex-direction: column;">
                    <div class="card-badge" style="background-color: #fef3c7; color: #d97706;">Students</div>
                    <h3>Roster Management</h3>
                    <p class="class-desc">Review information profiles, edit details, and analyze active registrations channels.</p>
                    <div class="card-footer" style="border:none; padding-top:0; margin-top: auto; width: 100%;">
                        <a href="enrolled_students.php" class="register-btn" style="background-color: #d97706; color: #ffffff; text-align:center; display: block; width:100%; text-decoration:none; box-sizing: border-box;">Open Directory</a>
                    </div>
                </div>
            </td>

            <td width="33.33%" valign="top" style="background: transparent;">
                <div class="class-card" style="height: 100%; min-height: 200px; margin: 0; display: flex; flex-direction: column;">
                    <div class="card-badge" style="background-color: #e0f2fe; color: #0369a1;">Faculty</div>
                    <h3>Faculty Directory</h3>
                    <p class="class-desc">Manage instructor accounts, structural department rows, and update roster assignments.</p>
                    <div class="card-footer" style="border:none; padding-top:0; margin-top: auto; width: 100%;">
                        <a href="hired_instructors.php" class="register-btn" style="background-color: #d97706; color: #ffffff; text-align:center; display: block; width:100%; text-decoration:none; box-sizing: border-box;">Open Faculty</a>
                    </div>
                </div>
            </td>

            <td width="33.33%" valign="top" style="background: transparent;">
                <div class="class-card" style="height: 100%; min-height: 226px; margin: 0; display: flex; flex-direction: column;">
                    <div class="card-badge" style="background-color: #dcfce7; color: #15803d;">Catalog</div>
                    <h3>Curriculum Studio</h3>
                    <p class="class-desc">Append new classes or edit active parameters, open sizes, and syllabi outlines.</p>
                    
                    <div class="card-footer" style="border:none; padding-top:0; margin-top: auto; width: 100(); display: flex; flex-direction: column; gap: 8px;">
                        <a href="course.php" class="register-btn" style="background-color: #d97706; color: #ffffff; text-align: center; display: block; width: 100%; text-decoration: none; box-sizing: border-box;">Add New Class</a>
                        <a href="editcourse.php" class="register-btn" style="background-color: #d97706; color: #ffffff; text-align: center; display: block; width: 100%; text-decoration: none; box-sizing: border-box;">Edit Existing Class</a>
                    </div>
                </div>
            </td>

        </tr>
    </table>
</div>

</body>
</html>