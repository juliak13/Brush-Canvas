<?php
session_start();
include('header.php');
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cname       = !empty($_POST['cname']) ? trim($_POST['cname']) : null;
    $credit      = !empty($_POST['credit']) ? intval($_POST['credit']) : 3;
    $dept        = !empty($_POST['dept']) ? trim($_POST['dept']) : null;
    $instructor  = !empty($_POST['instructor']) ? trim($_POST['instructor']) : null;
    $description = !empty($_POST['description']) ? trim($_POST['description']) : null;
    $modality    = !empty($_POST['modality']) ? trim($_POST['modality']) : null;
    $seats       = !empty($_POST['seats']) ? intval($_POST['seats']) : 15;

    if (!$cname || !$dept || !$instructor || !$description || !$modality || !$seats) {
        $message = "⚠️ Missing required input fields parameters. Please fill out all segments.";
        $message_type = "error";
    } else {
                
        // Automatically determine the next sequential Class ID number
        $queryID = "SELECT COALESCE(MAX(cid), 99) as max_id FROM class";
        $resID = $conn->query($queryID);
        $rowID = $resID->fetch_assoc();
        $nextCID = $rowID['max_id'] + 1;

        // Secure prepared insert statement prevents layout escaping and syntax breaking
        $stmt = $conn->prepare("INSERT INTO class (cid, cname, credit, dept, instructor, description, modality, seats) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isissssi", $nextCID, $cname, $credit, $dept, $instructor, $description, $modality, $seats);

        if ($stmt->execute()) {
            $message = "✨ Success: Course catalog entry created! Allocated Course ID #<strong>{$nextCID}</strong>.";
            $message_type = "success";
        } else {
            $message = "Catalog Insert failure. Please review form structure data rules.";
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add New Class</title>
    
    <style type="text/css">
        .compact-form-card {
            max-width: 550px;
            width: 100%;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 10px 30px rgba(30, 41, 59, 0.08);
            border: 1px solid #e2e8f0;
            text-align: left;
            font-family: 'Inter', Arial, sans-serif;
        }
        .form-heading { font-weight: 700; font-size: 26px; color: #1e293b; text-align: center; margin-bottom: 8px; }
        .form-subheading { font-size: 14px; color: #64748b; text-align: center; margin-bottom: 25px; line-height: 1.5; }
        .input-row { display: flex; flex-direction: column; margin-bottom: 18px; }
        .input-row-split { display: flex; gap: 16px; margin-bottom: 18px; }
        .input-row-split .input-row { flex: 1; margin-bottom: 0; }
        .input-row-three { display: flex; gap: 12px; margin-bottom: 18px; }
        .input-row-three .input-row { flex: 1; margin-bottom: 0; }
        .input-label { font-weight: 600; font-size: 13.5px; color: #1e293b; margin-bottom: 8px; }
        .input-label span.required { color: #dc2626; }
        .form-control { width: 100%; padding: 11px 14px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 15px; color: #334155; outline: none; box-sizing: border-box; font-family: inherit; }
        .form-control:focus { border-color: #d97706; box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15); }
        .submit-btn { width: 100%; padding: 12px; border-radius: 6px; border: none; font-weight: 600; font-size: 15px; background-color: #d97706; color: white; cursor: pointer; margin-top: 10px; transition: background 0.15s; text-align: center; display: block; text-decoration: none; }
        .submit-btn:hover { background-color: #b45309; }
    </style>
</head>
<body>

<table width="100%" height="85vh" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle">
            
            <div class="compact-form-card" style="margin-top: 20px; margin-bottom: 40px;">
                <h2 class="form-heading">New Course Registration</h2>

                <?php if (!empty($message)) : ?>
                    <div class="alert alert-<?= $message_type; ?>" style="margin-bottom: 20px; padding: 12px; border-radius: 5px;">
                        <?= $message; ?>
                    </div>
                <?php endif; ?>

                <form action="course.php" method="POST">
                    
                    <div class="input-row">
                        <label for="cname" class="input-label">Course Title Name <span class="required">*</span></label>
                        <input type="text" id="cname" name="cname" class="form-control" placeholder="e.g. Masterclass Charcoal Texturing" required>
                    </div>

                    <div class="input-row-split">
                        <div class="input-row">
                            <label for="dept" class="input-label">Department Division <span class="required">*</span></label>
                            <input type="text" id="dept" name="dept" class="form-control" placeholder="e.g. Drawing" required>
                        </div>
                        <div class="input-row">
                            <label for="instructor" class="input-label">Primary Instructor <span class="required">*</span></label>
                            <input type="text" id="instructor" name="instructor" class="form-control" placeholder="e.g. Stevens" required>
                        </div>
                    </div>

                    <div class="input-row-three">
                        <div class="input-row">
                            <label for="credit" class="input-label">Credit Units</label>
                            <input type="number" id="credit" name="credit" class="form-control" min="1" max="6" value="3">
                        </div>
                        <div class="input-row">
                            <label for="seats" class="input-label">Open Seats <span class="required">*</span></label>
                            <input type="number" id="seats" name="seats" class="form-control" min="1" value="15" required>
                        </div>
                        <div class="input-row">
                            <label for="modality" class="input-label">Modality <span class="required">*</span></label>
                            <select id="modality" name="modality" class="form-control" required>
                                <option value="In-Person">In-Person</option>
                                <option value="Online">Online</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-row">
                        <label for="description" class="input-label">Syllabus Outline Context <span class="required">*</span></label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Detail course tooling criteria requirements..." required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Publish Studio Course</button>
                </form>

                <div style="text-align: center; margin-top: 20px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <p style="font-size: 14px; color: #64748b;"><a href="admin_home.php" style="color: #d97706; font-weight: 600; text-decoration: none;">← Return to Admin Dashboard</a></p>
                </div>
            </div>

        </td>
    </tr>
</table>

</body>
</html>