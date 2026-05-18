<?php
session_start();
include('header.php');
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');

// Redirect to login if user isn't authenticated
if (!isset($_SESSION['valid_user'])) {
    header("Location: login.php");
    exit();
}

$registration_message = "";
$message_type = ""; 

$student_id = $_SESSION['valid_user'];
$is_student = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student');

// Handle class registration OR unenrollment if a form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["class_id"]) && isset($_POST["action"])) {
    
    // CRITICAL SECURITY CHECK: Prevent admins/instructors from submitting the post form spoofing a student
    if (!$is_student) {
        $registration_message = "Security Restriction: Only registered student profiles can modify course enrollment rosters.";
        $message_type = "error";
    } else {
        $class_id = intval($_POST["class_id"]);
        $action = $_POST["action"];

        // --- ACTION 1: REGISTER FOR A CLASS ---
        if ($action === "register") {
            $stmt_check = $conn->prepare("SELECT * FROM student_registrations WHERE cid = ? AND email = ?");
            $stmt_check->bind_param("is", $class_id, $student_id);
            $stmt_check->execute();
            $check_result = $stmt_check->get_result();

            if ($check_result && $check_result->num_rows == 0) {
                $stmt_reg = $conn->prepare("INSERT INTO student_registrations (cid, email) VALUES (?, ?)");
                $stmt_reg->bind_param("is", $class_id, $student_id);
                
                if ($stmt_reg->execute()) {
                    $stmt_update_seats = $conn->prepare("UPDATE class SET seats = seats - 1 WHERE cid = ? AND seats > 0");
                    $stmt_update_seats->bind_param("i", $class_id);
                    $stmt_update_seats->execute();
                    $stmt_update_seats->close();

                    $registration_message = "Successfully registered for the class!";
                    $message_type = "success";
                } else {
                    $registration_message = "Registration failed. Please try again.";
                    $message_type = "error";
                }
                $stmt_reg->close();
            } else {
                $registration_message = "You are already enrolled in this class.";
                $message_type = "info";
            }
            $stmt_check->close();
        }
        
        // --- ACTION 2: UNENROLL FROM A CLASS ---
        elseif ($action === "unenroll") {
            $stmt_unreg = $conn->prepare("DELETE FROM student_registrations WHERE cid = ? AND email = ?");
            $stmt_unreg->bind_param("is", $class_id, $student_id);
            
            if ($stmt_unreg->execute() && $conn->affected_rows > 0) {
                $stmt_add_seat = $conn->prepare("UPDATE class SET seats = seats + 1 WHERE cid = ?");
                $stmt_add_seat->bind_param("i", $class_id);
                $stmt_add_seat->execute();
                $stmt_add_seat->close();

                $registration_message = "Successfully unenrolled from the class. Your seat has been released.";
                $message_type = "success";
            } else {
                $registration_message = "Unenrollment failed. You might not be registered for this course.";
                $message_type = "error";
            }
            $stmt_unreg->close();
        }
    }
}

// --- LIVE FILTER SEARCH LOGIC ---
$searchtype = isset($_POST['searchtype']) ? $_POST['searchtype'] : null;
$searchterm = isset($_POST['searchterm']) ? trim($_POST['searchterm']) : '';

if (!empty($searchtype) && $searchterm !== '') {
    $searchtype_clean = $conn->real_escape_string($searchtype);
    $searchterm_clean = $conn->real_escape_string($searchterm);
    $query = "SELECT * FROM class WHERE $searchtype_clean LIKE '%$searchterm_clean%'";
} else {
    $query = "SELECT * FROM class"; 
}

$result = $conn->query($query);
$classes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Map out exactly what the logged-in user is currently enrolled in
$my_enrollments = [];
if ($is_student) {
    $enroll_query = "SELECT cid FROM student_registrations WHERE email = '$student_id'";
    $enroll_res = $conn->query($enroll_query);
    if ($enroll_res && $enroll_res->num_rows > 0) {
        while ($e_row = $enroll_res->fetch_assoc()) {
            $my_enrollments[] = intval($e_row['cid']);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Class Registration</title>
    <link rel="stylesheet" href="style.css">
    
    <style type="text/css">
        .search-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(30, 41, 59, 0.04);
            font-family: 'Inter', Arial, sans-serif;
        }
        .search-form { display: flex; gap: 12px; align-items: center; }
        .search-select { padding: 11px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 14px; color: #334155; background: #ffffff; outline: none; min-width: 150px; }
        .search-input { flex: 1; padding: 11px 14px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 14px; color: #334155; outline: none; }
        .search-select:focus, .search-input:focus { border-color: #d97706; box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1); }
        .search-btn { padding: 11px 24px; border-radius: 6px; border: none; font-weight: 600; font-size: 14px; background-color: #d97706; color: white; cursor: pointer; transition: background 0.15s; }
        .search-btn:hover { background-color: #b45309; }
        .clear-btn { padding: 11px 16px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; font-size: 14px; background-color: #f8fafc; color: #64748b; text-decoration: none; text-align: center; }
        .clear-btn:hover { background-color: #f1f5f9; color: #334155; }

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
        
        .smooth-border-card {
            width: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border: 1px solid #cbd5e1; 
            border-radius: 12px; 
            padding: 24px;
            box-shadow: 0 4px 15px rgba(30, 41, 59, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            box-sizing: border-box;
        }
        .smooth-border-card:hover {
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(30, 41, 59, 0.07);
        }


        @media (max-width: 900px) { .flex-card-wrapper { flex: 1 1 calc(50% - 12px); max-width: calc(50% - 12px); } }
        @media (max-width: 600px) { .flex-card-wrapper { flex: 1 1 100%; max-width: 100%; } }
    </style>
</head>
<body>

<div class="container">
    <header class="page-header" style="text-align: center; margin-bottom: 10px; padding-bottom: 5px;">
        <h2 style="font-size: 32px;">Course Catalog Enrollment</h2>
    </header>

    <div class="search-container">
        <form method="POST" action="cregistration.php" class="search-form">
            <select name="searchtype" class="search-select" required>
                <option value="cname" <?= $searchtype === 'cname' ? 'selected' : ''; ?>>Course Name</option>
                <option value="dept" <?= $searchtype === 'dept' ? 'selected' : ''; ?>>Department</option>
                <option value="instructor" <?= $searchtype === 'instructor' ? 'selected' : ''; ?>>Instructor</option>
            </select>
            <input type="text" name="searchterm" class="search-input" value="<?= htmlspecialchars($searchterm); ?>" placeholder="Type keyword filter parameter here..." required>
            <button type="submit" class="search-btn">Filter Catalog</button>
            <?php if (!empty($searchterm)): ?>
                <a href="cregistration.php" class="clear-btn">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (!empty($registration_message)) : ?>
        <div class="alert alert-<?= $message_type; ?>" style="margin-bottom: 20px; padding: 12px; border-radius: 5px;">
            <?= htmlspecialchars($registration_message); ?>
        </div>
    <?php endif; ?>

    <?php if (!$is_student) : ?>
        <div class="alert alert-info" style="margin-bottom: 20px;">
            🔍 <strong>Viewing Mode:</strong> You are currently logged in as an <strong><?= htmlspecialchars($_SESSION['user_type']); ?></strong>. Faculty and administrative profiles can look at course layouts here, but enrollment features are restricted to student accounts.
        </div>
    <?php endif; ?>

    <?php if (!empty($classes)) : ?>
        <div class="premium-flex-grid">
            <?php foreach ($classes as $class) : ?>
                <?php 
                    $cid = intval($class['cid']);
                    $is_enrolled = in_array($cid, $my_enrollments);
                ?>
                <div class="flex-card-wrapper">
                    <div class="smooth-border-card" style="<?= $is_enrolled ? 'border: 2px solid #059669; background-color: #f0fdf4;' : ''; ?>">
                        
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <h3 style="margin: 0; font-size: 20px; color: #1e293b; line-height: 1.3; font-family: 'Inter', Arial, sans-serif; font-weight:700;">
                                <?= htmlspecialchars($class['cname']); ?>
                            </h3>
                        </div>
                        
                        <p class="class-desc" style="margin: 0 0 16px 0; font-size: 14px; color: #475569; line-height: 1.5; flex-grow: 1;">
                            <?= htmlspecialchars($class['description'] ?? 'No description available.'); ?>
                        </p>
                        
                        <div class="class-meta" style="margin-bottom: 20px; font-size:13px; color: #334155; background:#f8fafc; padding:12px; border-radius:8px; border: 1px solid #e2e8f0; line-height: 1.7;">
                            <div><strong>Department:</strong> <?= htmlspecialchars($class['dept'] ?? 'General Art'); ?></div>
                            <div><strong>Instructor:</strong> Prof. <?= htmlspecialchars($class['instructor'] ?? 'TBD'); ?></div>
                            <div><strong>Format:</strong> <?= htmlspecialchars($class['modality'] ?? 'In-Person'); ?> • <?= htmlspecialchars($class['credit'] ?? '3'); ?> Credits</div>
                        </div>

                        <div class="card-footer" style="border:none; padding-top:0; margin-top: auto; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                            <div class="seats-counter" style="font-size: 14px; color: #64748b;">
                                <span class="seats-count" style="font-weight: 700; <?= $is_enrolled ? 'color: #059669;' : 'color: #1e293b;'; ?>">
                                    <?= htmlspecialchars($class['seats']); ?>
                                </span> seats left
                            </div>
                            
                            <?php if ($is_student) : ?>
                                <form method="POST" action="cregistration.php" style="margin: 0; flex: 1; max-width: 120px;">
                                    <input type="hidden" name="class_id" value="<?= $cid; ?>">
                                    
                                    <?php if ($is_enrolled) : ?>
                                        <input type="hidden" name="action" value="unenroll">
                                        <button type="submit" class="register-btn" style="background-color: #dc2626; color: #ffffff; text-align:center; display: block; width:100%; text-decoration:none; box-sizing: border-box; font-size: 13px; padding: 9px 12px; border-radius: 6px; border: none; font-weight:600; cursor:pointer;">Unenroll</button>
                                    <?php elseif (intval($class['seats']) <= 0) : ?>
                                        <button type="button" class="register-btn" style="background-color: #cbd5e1; color: #64748b; cursor: not-allowed; text-align:center; display: block; width:100%; text-decoration:none; box-sizing: border-box; font-size: 13px; padding: 9px 12px; border-radius: 6px; border: none; font-weight:600;" disabled>Full</button>
                                    <?php else : ?>
                                        <input type="hidden" name="action" value="register">
                                        <button type="submit" class="register-btn" style="background-color: #d97706; color: #ffffff; text-align:center; display: block; width:100%; text-decoration:none; box-sizing: border-box; font-size: 13px; padding: 9px 12px; border-radius: 6px; border: none; font-weight:600; cursor:pointer;">Register</button>
                                    <?php endif; ?>
                                </form>
                            <?php else : ?>
                                <button type="button" class="register-btn" style="background-color: #e2e8f0; color: #94a3b8; cursor: not-allowed; text-align:center; max-width: 120px; flex: 1; display: block; text-decoration:none; box-sizing: border-box; font-size: 12px; padding: 9px 4px; border-radius: 6px; border: none;" disabled>Student Only</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="no-data-card" style="margin-top: 20px;">
            <p>No matching studio classes found for those layout criteria parameters.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>