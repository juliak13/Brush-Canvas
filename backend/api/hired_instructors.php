<?php
session_start();
include('header.php'); 
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');

// CRITICAL SECURITY GUARD: Restriced exclusively to authenticated Admin profiles only
if (!isset($_SESSION['valid_user']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch instructors securely from the unified users table
$query = "SELECT id, fname, lname, username, email FROM users WHERE user_type = 'instructor'"; //
$result = $conn->query($query); //

$users = []; //
if ($result && $result->num_rows > 0) { //
    $users = $result->fetch_all(MYSQLI_ASSOC); //
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Hub - Hired Instructors</title>
    <link rel="stylesheet" href="style.css"> 
    
    <style type="text/css">
        /* Flexible modern cards grid container */
        .premium-flex-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            margin-top: 20px;
        }
        .flex-card-wrapper {
            flex: 1 1 calc(33.333% - 16px);
            min-width: 280px;
            max-width: calc(33.333% - 16px);
            display: flex;
        }
        
        /* Smooth, beautifully rounded card structures with explicit structural borders */
        .smooth-border-card {
            width: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #cbd5e1; /* Defined crisp bounding edge */
            border-radius: 12px; /* Smooth card curves */
            padding: 24px;
            box-shadow: 0 4px 15px rgba(30, 41, 59, 0.03); /* */
            transition: transform 0.2s ease, box-shadow 0.2s ease; /* */
            position: relative;
            box-sizing: border-box;
        }
        .smooth-border-card:hover {
            transform: translateY(-2px); /* Subtle physical movement response on hover */
            box-shadow: 0 6px 20px rgba(30, 41, 59, 0.07); /* */
        }

        /* Artistic initials avatar placeholder frame */
        .avatar-circle {
            width: 50px;
            height: 50px;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #d97706; /* Art school amber accent branding color */
            font-size: 16px;
            margin-bottom: 15px;
        }

        .username-badge-custom {
            background-color: #eff6ff;
            color: #1d4ed8;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        @media (max-width: 900px) { .flex-card-wrapper { flex: 1 1 calc(50% - 12px); max-width: calc(50% - 12px); } } /* */
        @media (max-width: 600px) { .flex-card-wrapper { flex: 1 1 100%; max-width: 100%; } } /* */
    </style>
</head>
<body>

<div class="container">
    <header class="page-header" style="text-align: center; margin-bottom: 25px; padding-bottom: 5px;">
        <h2 style="font-size: 32px;">Faculty Directory Management</h2>
        <p class="subtitle">Review active institutional artist profiles, administrative access fields, and contact parameters</p>
    </header>

    <?php if (!empty($users)): ?>
        <div class="premium-flex-grid">
            <?php foreach ($users as $user): ?>
                <?php 
                    // Calculate visual initials avatar label dynamically
                    $initials = strtoupper(substr($user['fname'], 0, 1) . substr($user['lname'], 0, 1));
                ?>
                <div class="flex-card-wrapper">
                    <div class="smooth-border-card">
                        
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div class="avatar-circle"><?= $initials; ?></div>
                            <span style="font-size: 12px; font-weight: 700; color: #94a3b8;">RECORD #<?= htmlspecialchars($user['id']); ?></span>
                        </div>
                        
                        <h3 style="margin: 0 0 8px 0; font-size: 20px; color: #1e293b; font-family: 'Inter', Arial, sans-serif; font-weight:700;">
                            Prof. <?= htmlspecialchars($user['fname']) . ' ' . htmlspecialchars($user['lname']); ?>
                        </h3>
                        
                        <div style="margin-bottom: 20px; font-size: 13.5px; color: #475569; flex-grow: 1; line-height: 1.8;">
                            <div><strong>Username:</strong> <span class="username-badge-custom">@<?= htmlspecialchars($user['username']) ?></span></div>
                            <div style="margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <strong>Email Contact:</strong> <br>
                                <a href="mailto:<?= htmlspecialchars($user['email']) ?>" style="color: #d97706; font-weight: 600; text-decoration: none;"><?= htmlspecialchars($user['email']) ?></a>
                            </div>
                        </div>

                        <div class="card-footer" style="border: none; padding-top: 0; margin-top: auto; width: 100%;">
                            <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="register-btn" style="background-color: #1e293b; color: #ffffff; text-align: center; display: block; width: 100%; text-decoration: none; box-sizing: border-box; font-size: 13px; padding: 10px 12px; border-radius: 6px; border: none; font-weight: 600;">
                                Send Message
                            </a>
                        </div>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-data-card" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 40px; border-radius: 12px; text-align: center; margin-top: 20px;">
            <p style="font-size: 15px; color: #475569; margin: 0;">No hired instructors found matching directory matrix criteria fields.</p>
        </div>
    <?php endif; ?>
    
    <div style="text-align: center; margin-top: 40px; margin-bottom: 50px;">
        <a href="admin_home.php" style="color: #d97706; font-weight: 600; text-decoration: none; font-size: 14px;">← Return to Admin Hub Dashboard</a>
    </div>
</div>

</body>
</html>