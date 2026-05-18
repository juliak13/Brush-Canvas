<?php
session_start();
include('header.php');
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');

// Security Guard: Restrict access strictly to Admins and Instructors
if (!isset($_SESSION['valid_user']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'instructor')) {
    header("Location: login.php");
    exit();
}

// Retrieve and sanitize the target student's email from the URL vector string
$student_email = isset($_GET['email']) ? $conn->real_escape_string(trim($_GET['email'])) : '';

$student_info = null;
$enrolled_classes = [];

if (!empty($student_email)) {
    // 1. Fetch base user details for the student
    $user_query = "SELECT id, fname, lname, username, email FROM users WHERE (email = '$student_email' OR username = '$student_email') AND user_type = 'student' LIMIT 1";
    $user_result = $conn->query($user_query);
    
    if ($user_result && $user_result->num_rows > 0) {
        $student_info = $user_result->fetch_assoc();
        
        // Re-assign exact email matching data string for subsequent roster queries
        $resolved_email = $student_info['email'];
        $resolved_username = $student_info['username'];
        
        // 2. Fetch all studio classes this specific student is currently registered for
        $class_query = "SELECT c.* FROM class c 
                        JOIN student_registrations sr ON c.cid = sr.cid 
                        WHERE sr.email = '$resolved_email' OR sr.email = '$resolved_username'
                        ORDER BY c.cname ASC";
        
        $class_result = $conn->query($class_query);
        if ($class_result && $class_result->num_rows > 0) {
            $enrolled_classes = $class_result->fetch_all(MYSQLI_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile Overview - Brush & Canvas</title>
    <link rel="stylesheet" href="style.css">
    
    <style type="text/css">
        .profile-wrapper {
            max-width: 800px;
            margin: 0 auto;
            font-family: 'Inter', Arial, sans-serif;
        }
        
        /* Unified smooth rounded layout components */
        .smooth-border-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(30, 41, 59, 0.03);
            box-sizing: border-box;
            margin-bottom: 24px;
        }

        .profile-header-block {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .avatar-circle-lg {
            width: 65px;
            height: 65px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #d97706; /* Art school amber brand color */
            font-size: 22px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .meta-item { font-size: 14px; color: #475569; line-height: 1.5; }
        .meta-item strong { color: #1e293b; }

        /* Course listing row tags */
        .class-row-tag {
            padding: 12px 16px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.15s ease;
        }
        .class-row-tag:hover {
            transform: translateX(3px);
            border-color: #d97706;
        }

        .meta-tag-badge {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 4px;
            background-color: #f1f5f9;
            color: #475569;
        }
    </style>
</head>
<body>

<div class="container profile-wrapper">

    <?php if ($student_info): ?>
        <?php 
            $initials = strtoupper(substr($student_info['fname'], 0, 1) . substr($student_info['lname'], 0, 1)); //
            $total_workshops = count($enrolled_classes); //
        ?>
        <header class="page-header" style="text-align: center; margin-bottom: 25px; padding-bottom: 5px;">
            <h2 style="font-size: 32px;">Student Account Dossier</h2>
            <p class="subtitle">Detailed academic schedule and workspace registration telemetry</p>
        </header>

        <div class="smooth-border-card">
            <div class="profile-header-block">
                <div class="avatar-circle-lg"><?= $initials; ?></div>
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 24px; color: #1e293b; font-weight: 700;">
                        <?= htmlspecialchars($student_info['fname'] . ' ' . $student_info['lname']); ?>
                    </h3>
                    <span style="font-size: 12px; font-weight: 700; background-color: #eff6ff; color: #1d4ed8; padding: 3px 8px; border-radius: 4px;">
                        @<?= htmlspecialchars($student_info['username']); ?>
                    </span>
                </div>
            </div>

            <div class="meta-grid">
                <div class="meta-item"><strong>Account Database ID:</strong> #<?= htmlspecialchars($student_info['id']); ?></div>
                <div class="meta-item"><strong>Primary Contact Email:</strong> <br><a href="mailto:<?= htmlspecialchars($student_info['email']); ?>" style="color: #d97706; text-decoration: none; font-weight: 600;"><?= htmlspecialchars($student_info['email']); ?></a></div>
                <div class="meta-item"><strong>Institutional Status:</strong> active student</div>
                <div class="meta-item"><strong>Active Registrations:</strong> <span style="font-weight: 700; color: #166534;"><?= $total_workshops; ?> Workshops Linked</span></div>
            </div>
        </div>

        <div style="margin-bottom: 15px; margin-top: 30px;">
            <h3 style="color: #1e293b; font-size: 22px; font-weight: 700;">Current Course Schedule Registry</h3>
        </div>

        <?php if (!empty($enrolled_classes)): ?>
            <div style="display: flex; flex-direction: column;">
                <?php foreach ($enrolled_classes as $workshop): ?>
                    <div class="class-row-tag">
                        <div>
                            <span style="font-weight: 700; color: #1e293b; font-size: 16px; display: block; margin-bottom: 2px;">
                                <?= htmlspecialchars($workshop['cname']); ?>
                            </span>
                            <span style="font-size: 12px; color: #64748b;">
                                Prof. <?= htmlspecialchars($workshop['instructor']); ?> • <?= htmlspecialchars($workshop['modality']); ?> (<?= htmlspecialchars($workshop['credit']); ?> Credits)
                            </span>
                        </div>
                        <span class="meta-tag-badge"><?= htmlspecialchars($workshop['dept']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data-card" style="background: #ffffff; border: 1px dashed #cbd5e1; padding: 30px; border-radius: 12px; text-align: center;">
                <p style="color: #64748b; margin: 0;">This student is not currently registered for any studio art workshops.</p>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="no-data-card" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 40px; border-radius: 12px; text-align: center; margin-top: 40px;">
            <h3 style="color: #dc2626; margin: 0 0 10px 0;">🔍 Account Dossier Missing</h3>
            <p style="color: #64748b; margin: 0;">The specified user parameter profile was not valid, belongs to a non-student account, or was missing entirely.</p>
        </div>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 40px; margin-bottom: 50px;">
        <a href="enrolled_students.php" style="color: #d97706; font-weight: 600; text-decoration: none; font-size: 14px;">← Back to Master Student Directory</a>
    </div>
</div>

</body>
</html>