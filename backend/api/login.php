<?php
session_start();
include('header.php');
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userid']) && isset($_POST['password'])) {
    $userid = trim($_POST['userid']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    $userid_clean = $conn->real_escape_string($userid);
    $user_type_clean = $conn->real_escape_string($user_type);
    
    $query = "SELECT * FROM users WHERE username='$userid_clean' AND user_type='$user_type_clean'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if ($password === $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['valid_user'] = $row['username'];
            $_SESSION['user_type'] = strtolower($row['user_type']); 
            
            if ($_SESSION['user_type'] === 'admin') {
                header("Location: admin_home.php");
            } elseif ($_SESSION['user_type'] === 'instructor') {
                header("Location: instructor_home.php");
            } else {
                header("Location: student_home.php");
            }
            exit();
        } else {
            $message = "Incorrect password match sequence. Try again.";
            $message_type = "error";
        }
    } else {
        $message = "No active account profile matched those role filter parameters.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Brush & Canvas</title>
    
    <style type="text/css">
        .compact-form-card {
            max-width: 480px;
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
        .input-label { font-weight: 600; font-size: 13.5px; color: #1e293b; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 11px 14px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 15px; color: #334155; outline: none; box-sizing: border-box; }
        .form-control:focus { border-color: #d97706; box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15); }
        .submit-btn { width: 100%; padding: 12px; border-radius: 6px; border: none; font-weight: 600; font-size: 15px; background-color: #d97706; color: white; cursor: pointer; margin-top: 10px; transition: background 0.15s; }
        .submit-btn:hover { background-color: #b45309; }
    </style>
</head>
<body>

<table width="100%" height="75vh" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle">
            
            <div class="compact-form-card">
                <h2 class="form-heading">Account Access</h2>
                <p class="form-subheading">Log in to enter your creative studio control environment</p>

                <?php if (!empty($message)) : ?>
                    <div class="alert alert-<?= $message_type; ?>" style="margin-bottom: 20px; padding: 12px; border-radius: 5px;">
                        <?= htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="input-row">
                        <label for="user_type" class="input-label">Account Type</label>
                        <select id="user_type" name="user_type" class="form-control" required>
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                            <option value="admin">System Admin</option>
                        </select>
                    </div>

                    <div class="input-row">
                        <label for="userid" class="input-label">Username</label>
                        <input type="text" id="userid" name="userid" class="form-control" placeholder="e.g. artist_alpha" required>
                    </div>

                    <div class="input-row">
                        <label for="password" class="input-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="submit-btn">Sign In</button>
                </form>

                <div style="text-align: center; margin-top: 25px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <p style="font-size: 14px; color: #64748b;">Don't have an account yet? <a href="signup.php" style="color: #d97706; font-weight:600; text-decoration:none;">Create Profile</a></p>
                </div>
            </div>

        </td>
    </tr>
</table>

</body>
</html>