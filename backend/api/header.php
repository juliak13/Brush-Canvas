<?php
// CRITICAL FIX: Make sure the session engine is running before checking user roles!
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brush & Canvas Art Classes</title>
    <link rel="stylesheet" href="style.css?v=99.9">
    
    <style type="text/css">
        .admin-header-bg { background-color: #1e293b; }
        .admin-title-text {
            color: #ffffff; 
            font-size: 26pt; 
            text-align: center;
            font-family: 'Inter', Arial, sans-serif;
            font-weight: 700;
            margin: 0;
            padding-left: 15px;
        }
        .menu-cell {
            background: #d97706; 
            text-align: center;
            padding: 12px;
            transition: background 0.2s ease;
        }
        .menu-cell:hover { background: #b45309; }
        .menu-text {
            color: #ffffff; 
            font-size: 11pt; 
            font-family: 'Inter', Arial, sans-serif; 
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .menu-cell a { display: block; width: 100%; text-decoration: none; }
        
        /* Premium active-tab indicator tracking system rules */
        .menu-cell.active-tab {
            background-color: #0f172a !important; 
        }
    </style>
</head>
<body>

<table width="100%" cellpadding="16" cellspacing="0" border="0" class="admin-header-bg">
    <tr>
        <td width="70" align="left" style="background: transparent; padding-right: 0;">
            <img src="/frontend/images/logo.png" height="65" width="65" alt="Logo" style="display: block; border-radius: 6px;">
        </td>
        <td align="center" style="background: transparent;">
            <h1 class="admin-title-text">Brush & Canvas</h1>
        </td>
    </tr>
</table>

<table width="100%" bgcolor="#ffffff" cellpadding="0" cellspacing="4" style="margin-bottom: 20px;">
    <tr>
        <td width="25%" class="menu-cell <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active-tab' : ''; ?>">
            <a href="index.php"><span class="menu-text">Home</span></a>
        </td>
        
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') : ?>
            <td width="25%" class="menu-cell <?= (basename($_SERVER['PHP_SELF']) == 'admin_home.php') ? 'active-tab' : ''; ?>">
                <a href="admin_home.php"><span class="menu-text">Dashboard</span></a>
            <td width="25%" class="menu-cell <?= (basename($_SERVER['PHP_SELF']) == 'enrolled_students.php') ? 'active-tab' : ''; ?>">
                <a href="enrolled_students.php"><span class="menu-text">Students Roster</span></a>
            </td>
            <td width="25%" class="menu-cell <?= (basename($_SERVER['PHP_SELF']) == 'course.php') ? 'active-tab' : ''; ?>">
                <a href="course.php"><span class="menu-text">New Course</span></a>
            </td>
            <td width="25%" class="menu-cell <?= (basename($_SERVER['PHP_SELF']) == 'hired_instructors.php') ? 'active-tab' : ''; ?>">
                <a href="hired_instructors.php"><span class="menu-text">Instructors</span></a>
            </td>
        <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'instructor') : ?>
            
            <td width="25%" class="menu-cell <?= (basename($_SERVER['PHP_SELF']) == 'instructor_home.php') ? 'active-tab' : ''; ?>">
                <a href="instructor_home.php"><span class="menu-text">My Classes</span></a>
            </td>
        <?php else : ?>
            <td width="25%" class="menu-cell <?= (basename($_SERVER['PHP_SELF']) == 'cregistration.php') ? 'active-tab' : ''; ?>">
                <a href="cregistration.php"><span class="menu-text">Course Catalog</span></a>
            </td>
            <td width="25%" class="menu-cell <?= (basename($_SERVER['PHP_SELF']) == 'student_home.php') ? 'active-tab' : ''; ?>">
                <a href="student_home.php"><span class="menu-text">My Classes</span></a>
            </td>
        <?php endif; ?>

        <?php if (isset($_SESSION['valid_user'])) : ?>
            <td width="25%" class="menu-cell <?= (basename($_SERVER['PHP_SELF']) == 'logout.php') ? 'active-tab' : ''; ?>">
                <a href="logout.php"><span class="menu-text" style="color: #fca5a5;">Logout (<?= htmlspecialchars($_SESSION['valid_user']); ?>)</span></a>
            </td>
        <?php else : ?>
            <td width="25%" class="menu-cell <?= (basename($_SERVER['PHP_SELF']) == 'login.php' || basename($_SERVER['PHP_SELF']) == 'signup.php') ? 'active-tab' : ''; ?>">
                <a href="login.php"><span class="menu-text">Login / Join</span></a>
            </td>
        <?php endif; ?>
    </tr>
</table>