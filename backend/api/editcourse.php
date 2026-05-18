<?php
session_start();
include('header.php');
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');  

$message = "";
$message_type = ""; 

// Form processing happens when the admin submits the changes
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Snatch data safely from the form post fields
    $cid         = !empty($_POST['cid']) ? intval($_POST['cid']) : null;
    $cname       = !empty($_POST['cname']) ? trim($_POST['cname']) : null;
    $credit      = !empty($_POST['credit']) ? intval($_POST['credit']) : null;
    $dept        = !empty($_POST['dept']) ? trim($_POST['dept']) : null;
    $instructor  = !empty($_POST['instructor']) ? trim($_POST['instructor']) : null;
    $description = !empty($_POST['description']) ? trim($_POST['description']) : null;
    $modality    = !empty($_POST['modality']) ? trim($_POST['modality']) : null;
    $seats        = !empty($_POST['seats']) ? intval($_POST['seats']) : null;

    // Check that primary details aren't left blank
    if (!$cid || !$cname || !$instructor || !$description || !$seats) {
        $message = "⚠️ You have not entered all the required details. Please check all fields.";
        $message_type = "error";
    } else {
        // Fixed: Changed "$db->prepare" to use your real local variable "$conn->prepare"
        $check_stmt = $conn->prepare("SELECT cid FROM class WHERE cid = ?");
        $check_stmt->bind_param("i", $cid);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result && $check_result->num_rows > 0) {
            // Course exists! Run a secure, modern SQL UPDATE statement matching your schema
            $update_stmt = $conn->prepare("UPDATE class SET cname=?, credit=?, dept=?, instructor=?, description=?, modality=?, seats=? WHERE cid=?");
            $update_stmt->bind_param("sissssii", $cname, $credit, $dept, $instructor, $description, $modality, $seats, $cid);
            
            if ($update_stmt->execute()) {
                $message = "✨ Success: Course ID #{$cid} has been updated in the catalog database.";
                $message_type = "success";
            } else {
                $message = "❌ An error occurred. The course details could not be updated.";
                $message_type = "error";
            }
            $update_stmt->close();
        } else {
            $message = "🔍 The specified Course ID does not exist. Please check the ID and try again.";
            $message_type = "info";
        }
        
        $check_stmt->close();
        // Fixed: Removed the premature close statement from here to keep the environment stable!
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Catalog Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container container-small">
    <header class="page-header">
        <h2>Edit Course Catalog</h2>
        <p class="subtitle">Modify live course parameters, structure, or availability</p>
    </header>

    <?php if (!empty($message)) : ?>
        <div class="alert alert-<?= $message_type; ?>">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <form action="editcourse.php" method="POST">
            
            <div class="form-group-row">
                <div class="form-group">
                    <label for="cid">Course ID <span class="required">*</span></label>
                    <input type="number" id="cid" name="cid" placeholder="e.g. 100" value="<?= isset($_POST['cid']) ? htmlspecialchars($_POST['cid']) : ''; ?>" required>
                    <small>Must match an existing course ID number.</small>
                </div>

                <div class="form-group">
                    <label for="cname">Course Title <span class="required">*</span></label>
                    <input type="text" id="cname" name="cname" placeholder="e.g. Fundamentals of Drawing" value="<?= isset($_POST['cname']) ? htmlspecialchars($_POST['cname']) : ''; ?>" required>
                </div>
            </div>

            <div class="form-group-row">
                <div class="form-group">
                    <label for="instructor">Instructor Name <span class="required">*</span></label>
                    <input type="text" id="instructor" name="instructor" placeholder="e.g. Smith" value="<?= isset($_POST['instructor']) ? htmlspecialchars($_POST['instructor']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="dept">Department</label>
                    <input type="text" id="dept" name="dept" placeholder="e.g. Drawing" value="<?= isset($_POST['dept']) ? htmlspecialchars($_POST['dept']) : ''; ?>">
                </div>
            </div>

            <div class="form-group-row">
                <div class="form-group">
                    <label for="credit">Course Credits</label>
                    <input type="number" id="credit" name="credit" min="1" max="5" placeholder="3" value="<?= isset($_POST['credit']) ? htmlspecialchars($_POST['credit']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="modality">Modality</label>
                    <select id="modality" name="modality">
                        <option value="In-Person" <?= (isset($_POST['modality']) && $_POST['modality'] == 'In-Person') ? 'selected' : ''; ?>>In-Person</option>
                        <option value="Online" <?= (isset($_POST['modality']) && $_POST['modality'] == 'Online') ? 'selected' : ''; ?>>Online</option>
                        <option value="Hybrid" <?= (isset($_POST['modality']) && $_POST['modality'] == 'Hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="seats">Total Capacity (Seats) <span class="required">*</span></label>
                    <input type="number" id="seats" name="seats" min="1" placeholder="15" value="<?= isset($_POST['seats']) ? htmlspecialchars($_POST['seats']) : ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Course Description <span class="required">*</span></label>
                <textarea id="description" name="description" rows="4" placeholder="Describe the course context, constraints, required tool sets..." required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-btn">Apply Changes</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>