<?php
session_start();
include('header.php'); 
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');

// Security check to ensure a student is logged in
if (!isset($_SESSION['valid_user'])) {
    header("Location: login.php");
    exit();
}

$student_email = $_SESSION['valid_user']; //

// Gather the classes this student is currently registered for using the mapping table
$query = "SELECT c.* FROM class c 
          JOIN student_registrations s ON c.cid = s.cid 
          WHERE s.email = '$student_email'"; //
          
$result = $conn->query($query); //

$my_classes = []; //
if ($result && $result->num_rows > 0) { //
    $my_classes = $result->fetch_all(MYSQLI_ASSOC); //
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Home - Brush & Canvas</title>
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
            min-width: 290px;
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

        .meta-tag-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        @media (max-width: 900px) { .flex-card-wrapper { flex: 1 1 calc(50% - 12px); max-width: calc(50% - 12px); } } /* */
        @media (max-width: 600px) { .flex-card-wrapper { flex: 1 1 100%; max-width: 100%; } } /* */
    </style>
</head>
<body>

<div class="container">
    <header class="page-header" style="text-align: center; margin-bottom: 25px; padding-bottom: 5px;">
        <h2 style="font-size: 32px;">Student Studio Workspace</h2>
    </header>

    <div style="margin-bottom: 15px; margin-top: 30px;">
        <h3 style="color: #1e293b; font-size: 22px; font-weight: 700;">Your Enrolled Studio Classes</h3>
    </div>

    <?php if (!empty($my_classes)) : ?>
        <div class="premium-flex-grid">
            <?php foreach ($my_classes as $class) : ?>
                <div class="flex-card-wrapper">
                    <div class="smooth-border-card">
                        
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <h3 style="margin: 0; font-size: 20px; color: #1e293b; line-height: 1.3; font-family: 'Inter', Arial, sans-serif; font-weight:700;">
                                <?= htmlspecialchars($class['cname']); ?>
                            </h3>
                        </div>
                        
                        <p class="class-desc" style="margin: 0 0 16px 0; font-size: 14px; color: #475569; line-height: 1.5; flex-grow: 1;">
                            <?= htmlspecialchars($class['description'] ?? 'No syllabus data provided.'); ?>
                        </p>
                        
                        <div class="class-meta" style="margin-bottom: 20px; font-size:13px; color: #334155; background:#f8fafc; padding:12px; border-radius:8px; border: 1px solid #e2e8f0; line-height: 1.7;">
                            <div><strong>Department:</strong> <?= htmlspecialchars($class['dept'] ?? 'General Art'); ?></div>
                            <div><strong>Instructor:</strong> Prof. <?= htmlspecialchars($class['instructor'] ?? 'TBD'); ?></div>
                            <div><strong>Format:</strong> <?= htmlspecialchars($class['modality'] ?? 'In-Person'); ?> • <?= htmlspecialchars($class['credit'] ?? '3'); ?> Credits</div>
                        </div>

                        <div class="card-footer" style="border:none; padding-top:0; margin-top: auto; width: 100%;">
                            <a href="class_details.php?cid=<?= $class['cid']; ?>" class="register-btn" style="background-color: #1e293b; color: #ffffff; text-align: center; display: block; width: 100%; text-decoration: none; box-sizing: border-box; font-size: 13px; padding: 10px 12px; border-radius: 6px; border: none; font-weight: 600;">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="no-data-card" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 40px; border-radius: 12px; text-align: center; margin-top: 20px; box-shadow: 0 4px 15px rgba(30, 41, 59, 0.02);">
            <p style="font-size: 16px; color: #475569; margin-bottom: 20px;">You aren't registered for any creative art workshops yet.</p>
            <a href="cregistration.php" class="register-btn" style="display: inline-block; text-decoration: none; background-color: #d97706; color: white; padding: 11px 24px; border-radius: 6px; font-weight: 600; font-size: 14px; transition: background 0.15s;">
                Explore Open Courses
            </a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>