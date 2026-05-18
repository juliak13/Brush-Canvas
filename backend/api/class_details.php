<?php
session_start();
include('header.php'); 
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');

// Security check to ensure a user is authenticated
if (!isset($_SESSION['valid_user'])) {
    header("Location: login.php");
    exit();
}

$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0; //
$classinfo = null; //
$instructor = null; //
$students = []; //

// Establish role parameters for roster security
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'student';
$can_view_roster = ($user_type === 'admin' || $user_type === 'instructor');

if ($cid > 0) { //
    $stmt1 = $conn->prepare("SELECT * FROM class WHERE cid = ?"); //
    $stmt1->bind_param("i", $cid); //
    $stmt1->execute(); //
    $result = $stmt1->get_result(); //

    if ($result && $result->num_rows > 0) { //
        $classinfo = $result->fetch_assoc(); //

        // Locate Assigned Faculty Profile Info
        $stmt2 = $conn->prepare("SELECT u.* FROM users u JOIN class c ON u.username = c.instructor WHERE c.cid = ?"); //
        $stmt2->bind_param("i", $cid); //
        $stmt2->execute(); //
        $result2 = $stmt2->get_result(); //
        if ($result2 && $result2->num_rows > 0) { //
            $instructor = $result2->fetch_assoc(); //
        }
        $stmt2->close(); //
        
        // Gather student records
        $stmt3 = $conn->prepare("SELECT email FROM student_registrations WHERE cid = ?"); //
        $stmt3->bind_param("i", $cid); //
        $stmt3->execute(); //
        $result3 = $stmt3->get_result(); //
        if ($result3 && $result3->num_rows > 0) { //
            $students = $result3->fetch_all(MYSQLI_ASSOC); //
        }
        $stmt3->close(); //
    }
    $stmt1->close(); //
}
$conn->close(); //
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Class Details - Canvas Portal</title>
    <link rel="stylesheet" href="style.css">
    
    <style type="text/css">
        /* Beautifully rounded layout structures matching registration themes */
        .smooth-border-card {
            background: #ffffff;
            border: 1px solid #cbd5e1; /* Clear crisp boundary edge */
            border-radius: 12px; /* Smooth card curves */
            padding: 30px;
            box-shadow: 0 4px 15px rgba(30, 41, 59, 0.03); /* */
            font-family: 'Inter', Arial, sans-serif;
            box-sizing: border-box;
            margin-bottom: 30px;
        }
        
        .roster-dot {
            height: 8px;
            width: 8px;
            background-color: #d97706; /* Core studio amber brand color */
            border-radius: 50%;
            display: inline-block;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0; /* */
            margin-bottom: 20px;
        }

        .meta-item { font-size: 14px; color: #334155; line-height: 1.5; }
        .meta-item strong { color: #1e293b; }

        .roster-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .roster-table th {
            text-align: left;
            background-color: #1e293b;
            color: #ffffff;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px 6px 0 0;
        }
        .roster-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .roster-table tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>

<div class="container" style="max-width: 750px; margin: 0 auto;">
    <?php if ($classinfo): ?>
        <header class="page-header" style="text-align: center; margin-bottom: 25px; padding-bottom: 5px;">
            <h2 style="font-size: 32px;">Course Specification</h2>
            <p class="subtitle">Detailed overview for creative curriculum reference</p>
        </header>

        <div class="smooth-border-card">
            <h3 style="color: #1e293b; margin: 0 0 16px 0; font-size: 24px; font-weight: 700; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                <?= htmlspecialchars($classinfo['cname']) ?>
            </h3>
            
            <div class="meta-grid">
                <div class="meta-item"><strong>Course Unit ID:</strong> #<?= htmlspecialchars($classinfo['cid']) ?></div>
                <div class="meta-item"><strong>Academic Credits:</strong> <?= htmlspecialchars($classinfo['credit'] ?? '3') ?> Units</div>
                <div class="meta-item"><strong>Faculty Division:</strong> <?= htmlspecialchars($classinfo['dept']) ?> Studio</div>
                <div class="meta-item"><strong>Active Modality:</strong> <?= htmlspecialchars($classinfo['modality']) ?> Format</div>
                <div class="meta-item"><strong>Primary Instructor:</strong> Prof. <?= htmlspecialchars($instructor['fname'] ?? '') ?> <?= htmlspecialchars($instructor['lname'] ?? ($classinfo['instructor'] ?? 'Unassigned')) ?></div>
                <div class="meta-item"><strong>Remaining Capacity:</strong> <span style="color: #059669; font-weight: 700;"><?= htmlspecialchars($classinfo['seats']) ?> Seats Left</span></div>
            </div>
            
            <?php if (!empty($classinfo['description'])): ?> 
                <div style="margin-top: 20px;">
                    <h4 style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 8px;">Syllabus Description</h4>
                    <p style="color: #475569; text-align: left; font-size: 14.5px; background: #ffffff; padding: 14px; border-radius: 8px; border: 1px solid #cbd5e1; border-left: 4px solid #d97706; margin: 0; line-height: 1.6;">
                        <?= htmlspecialchars($classinfo['description']) ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 15px; margin-top: 20px;">
            <h3 style="color: #1e293b; font-size: 22px; font-weight: 700;">Enrolled Roster Profile</h3>
        </div>

        <?php if ($can_view_roster): ?>
            <?php if(!empty($students)): ?>
                <div class="smooth-border-card" style="padding: 0; overflow: hidden;">
                    <table class="roster-table">
                        <thead>
                            <tr>
                                <th>Student Contact Identifiers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $st): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <span class="roster-dot"></span>
                                            <a href="mailto:<?= htmlspecialchars($st['email']) ?>" class="email-link" style="color: #d97706; font-weight: 600; text-decoration: none;"><?= htmlspecialchars($st['email']) ?></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data-card" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 30px; border-radius: 12px; text-align: center;"><p style="color: #64748b; margin: 0;">No active student records found enrolled inside this roster course container.</p></div>
            <?php endif; ?>

        <?php else: ?>
            <div class="no-data-card" style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 30px; border-radius: 12px; text-align: center;">
                <p style="color: #64748b; margin: 0; font-size: 14px;">
                    <strong>Privacy Restriction:</strong> Access to the complete student email roster ledger remains strictly reserved for authorized Faculty and System Administrators.
                </p>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="no-data-card" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 40px; border-radius: 12px; text-align: center; margin-top: 40px;">
            <h3 style="color: #dc2626; margin: 0 0 10px 0;">🔍 Resource Not Located</h3>
            <p style="color: #64748b; margin: 0;">The specified course collection details parameter profile was not valid or missing.</p>
        </div>
    <?php endif; ?>
    
    <div style="text-align: center; margin-top: 30px; margin-bottom: 50px;">
        <?php if ($user_type === 'admin'): ?>
            <a href="admin_home.php" style="color: #d97706; font-weight: 600; text-decoration: none; font-size: 14px;">← Return to Admin Dashboard</a>
        <?php elseif ($user_type === 'instructor'): ?>
            <a href="instructor_home.php" style="color: #d97706; font-weight: 600; text-decoration: none; font-size: 14px;">← Return to Faculty Dashboard</a>
        <?php else: ?>
            <a href="student_home.php" style="color: #d97706; font-weight: 600; text-decoration: none; font-size: 14px;">← Return to My Classes</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>