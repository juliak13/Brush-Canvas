<?php
session_start();
include('header.php');
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');

// Security Guard: Ensure only authenticated Admins or Instructors can view this master ledger
if (!isset($_SESSION['valid_user']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'instructor')) {
    header("Location: login.php");
    exit();
}

// Optimized query to fetch base student rows and aggregate their total class registration counts
$query = "SELECT u.fname, u.lname, u.email as student_email, COUNT(sr.cid) as total_classes 
          FROM users u
          LEFT JOIN student_registrations sr ON (u.username = sr.email OR u.email = sr.email)
          WHERE u.user_type = 'student'
          GROUP BY u.email, u.fname, u.lname
          ORDER BY u.lname ASC, u.fname ASC";

$result = $conn->query($query);

$students = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Master Student Index</title>
    <link rel="stylesheet" href="style.css">
    
    <style type="text/css">
        .directory-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
            font-family: 'Inter', Arial, sans-serif;
        }

        /* Streamlined horizontal row style card */
        .directory-row-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(30, 41, 59, 0.02);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-sizing: border-box;
            width: 100%;
        }
        .directory-row-card:hover {
            transform: translateX(4px); /* Gentle slide-right response to guide focus */
            box-shadow: 0 4px 12px rgba(30, 41, 59, 0.06);
            border-color: #d97706;
        }

        .student-identity-block {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 2;
        }

        .avatar-circle-sm {
            width: 40px;
            height: 40px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #d97706;
            font-size: 13px;
        }

        .info-meta-block {
            flex: 1;
            font-size: 14px;
            color: #64748b;
            text-align: left;
        }

        .profile-btn {
            padding: 8px 16px;
            border-radius: 6px;
            background-color: #1e293b;
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            transition: background 0.15s;
            text-align: center;
            border: none;
            cursor: pointer;
        }
        .profile-btn:hover {
            background-color: #d97706;
        }
    </style>
</head>
<body>

<div class="container" style="max-width: 850px; margin: 0 auto;">
    <header class="page-header" style="text-align: center; margin-bottom: 25px; padding-bottom: 5px;">
        <h2 style="font-size: 32px;">Master Student Directory</h2>
        <p class="subtitle">Search, inspect, and open deep diagnostic profiles for institutional art student accounts</p>
    </header>

    <div style="margin-bottom: 15px; margin-top: 30px;">
        <h3 style="color: #1e293b; font-size: 22px; font-weight: 700;">Active Roster Records</h3>
    </div>

    <?php if (!empty($students)) : ?>
        <div class="directory-container">
            <?php foreach ($students as $student) : ?>
                <?php 
                    $initials = strtoupper(substr($student['fname'], 0, 1) . substr($student['lname'], 0, 1));
                    $classes_enrolled = intval($student['total_classes']);
                ?>
                <div class="directory-row-card">
                    
                    <div class="student-identity-block">
                        <div class="avatar-circle-sm"><?= $initials; ?></div>
                        <div>
                            <h4 style="margin: 0 0 2px 0; font-size: 17px; color: #1e293b; font-weight: 700;">
                                <?= htmlspecialchars($student['fname'] . ' ' . $student['lname']); ?>
                            </h4>
                            <p style="margin: 0; font-size: 13px; color: #64748b;"><?= htmlspecialchars($student['student_email']); ?></p>
                        </div>
                    </div>

                    <div class="info-meta-block">
                        <span style="font-weight: 600; color: #334155;">Status:</span> 
                        <span style="font-size: 12px; font-weight: 700; background-color: <?= $classes_enrolled > 0 ? '#f0fdf4; color: #166534;' : '#f1f5f9; color: #475569;'; ?> padding: 3px 8px; border-radius: 12px; margin-left: 4px;">
                            <?= $classes_enrolled; ?> <?= $classes_enrolled === 1 ? 'Active Class' : 'Active Classes'; ?>
                        </span>
                    </div>

                    <div>
                        <a href="student_profile.php?email=<?= urlencode($student['student_email']); ?>" class="profile-btn">
                            Open Profile →
                        </a>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="no-data-card" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 40px; border-radius: 12px; text-align: center; margin-top: 20px;">
            <p style="font-size: 15px; color: #475569; margin: 0;">No student records currently discovered inside the platform database system ledger.</p>
        </div>
    <?php endif; ?>
    
    <div style="text-align: center; margin-top: 40px; margin-bottom: 50px;">
        <?php if ($_SESSION['user_type'] === 'admin'): ?>
            <a href="admin_home.php" style="color: #d97706; font-weight: 600; text-decoration: none; font-size: 14px;">← Return to Admin Hub Dashboard</a>
        <?php else: ?>
            <a href="instructor_home.php" style="color: #d97706; font-weight: 600; text-decoration: none; font-size: 14px;">← Return to Faculty Dashboard</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>