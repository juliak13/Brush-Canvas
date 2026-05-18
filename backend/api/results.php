<?php
session_start();
include('header.php');
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');

// Redirect to login if user isn't authenticated
if (!isset($_SESSION['valid_user'])) {
    header("Location: login.php");
    exit();
}

$searchtype = isset($_POST['searchtype']) ? $_POST['searchtype'] : null;
$searchterm = isset($_POST['searchterm']) ? trim($_POST['searchterm']) : '';

if (!$searchtype || $searchterm === '') {
    echo "<div class='container'><div class='alert alert-error'>Missing search parameters. Please return to the catalog and query again.</div></div>";
    exit;
}

$student_id = $_SESSION['valid_user'];
$is_student = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student');

$searchtype_clean = $conn->real_escape_string($searchtype);
$searchterm_clean = $conn->real_escape_string($searchterm);

// Query the class catalog using the dynamic keyword constraint filters
$query = "SELECT * FROM class WHERE $searchtype_clean LIKE '%$searchterm_clean%'";
$result = $conn->query($query);

if (!$result) {
    die("<div class='container'><div class='alert alert-error'>Query failure execution error: " . htmlspecialchars($conn->error) . "</div></div>");
}

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
$num_results = count($classes);

// Map out the logged-in student's current enrollments to render buttons correctly
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
    <title>Search Outcomes - Brush & Canvas</title>
    <link rel="stylesheet" href="style.css?v=2.0">
</head>
<body>

<div class="container">
    <header class="page-header" style="margin-bottom: 30px;">
        <h2>Catalog Search Outcomes</h2>
        <p class="subtitle">Discovered <strong><?= $num_results; ?></strong> studio matching workshop items in the database registry</p>
    </header>

    <?php if (!$is_student) : ?>
        <div class="alert alert-info" style="margin-bottom: 30px;">
            🔍 <strong>Viewing Mode:</strong> Administrative profiles can query layout structures here, but registration controls remain reserved for students.
        </div>
    <?php endif; ?>

    <?php if ($num_results > 0) : ?>
        <div class="class-grid">
            <?php foreach ($classes as $class) : ?>
                <?php 
                    $cid = intval($class['cid']);
                    $is_enrolled = in_array($cid, $my_enrollments);
                ?>
                <div class="class-card" style="<?= $is_enrolled ? 'border: 2px solid #059669; background-color: #f0fdf4;' : ''; ?>">
                    <div class="card-badge" style="<?= $is_enrolled ? 'background-color: #059669;' : ''; ?>">
                        <?= $is_enrolled ? '✓ Enrolled' : htmlspecialchars($class['dept'] ?? 'Art'); ?>
                    </div>
                    <h3><?= htmlspecialchars($class['cname']); ?></h3>
                    
                    <p class="class-desc"><?= htmlspecialchars($class['description'] ?? 'No description available.'); ?></p>
                    
                    <div class="class-meta">
                        <span><strong>Instructor:</strong> Prof. <?= htmlspecialchars($class['instructor'] ?? 'TBD'); ?></span>
                        <span><strong>Credits:</strong> <?= htmlspecialchars($class['credit'] ?? '3'); ?></span>
                        <span><strong>Format:</strong> <?= htmlspecialchars($class['modality'] ?? 'In-Person'); ?></span>
                    </div>

                    <div class="card-footer">
                        <div class="seats-counter">
                            <span class="seats-count" style="<?= $is_enrolled ? 'color: #059669;' : ''; ?>">
                                <?= htmlspecialchars($class['seats']); ?>
                            </span> seats left
                        </div>
                        
                        <?php if ($is_student) : ?>
                            <form method="POST" action="cregistration.php">
                                <input type="hidden" name="class_id" value="<?= $cid; ?>">
                                
                                <?php if ($is_enrolled) : ?>
                                    <input type="hidden" name="action" value="unenroll">
                                    <button type="submit" class="register-btn" style="background-color: #dc2626;">Unenroll</button>
                                <?php elseif (intval($class['seats']) <= 0) : ?>
                                    <button type="button" class="register-btn" style="background-color: #cbd5e1; color: #64748b; cursor: not-allowed;" disabled>Class Full</button>
                                <?php else : ?>
                                    <input type="hidden" name="action" value="register">
                                    <button type="submit" class="register-btn">Register</button>
                                <?php endif; ?>
                            </form>
                        <?php else : ?>
                            <button type="button" class="register-btn" style="background-color: #e2e8f0; color: #94a3b8; cursor: not-allowed;" disabled>Student Only</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="no-data-card">
            <p>No matching studio classes discovered in catalog registry parameters.</p>
            <a href="search.php" class="email-link" style="display:inline-block; margin-top:15px;">← Adjust Filters & Try Again</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>