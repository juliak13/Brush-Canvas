<?php
session_start();
include('header.php');     
require_once('/Users/juliakonopka/Desktop/Brush&Canvas/backend/database/config.php');

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname     = trim($_POST["fname"]);
    $lname     = trim($_POST["lname"]);
    $email     = trim($_POST["email"]);
    $username  = trim($_POST["username"]);
    $password  = $_POST["password"];
    $user_type = $_POST["user_type"];
    $code      = isset($_POST["code"]) ? trim($_POST["code"]) : '';

    if (($user_type == 'admin' || $user_type == 'instructor') && $code !== 'brush_canvas_secret') {
        $message = "⚠️ Invalid administrative access verification passcode clearance.";
        $message_type = "error";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $u_clean = $conn->real_escape_string($username);
        $e_clean = $conn->real_escape_string($email);
        $f_clean = $conn->real_escape_string($fname);
        $l_clean = $conn->real_escape_string($lname);
        $ut_clean = $conn->real_escape_string($user_type); 

        $check = $conn->query("SELECT username FROM users WHERE username = '$u_clean' OR email = '$e_clean'");
        
        if ($check && $check->num_rows > 0) {
            $message = "ℹ️ That username or email identity is already registered to an account.";
            $message_type = "info";
        } else {
            $query = "INSERT INTO users (fname, lname, email, username, password, user_type) 
                      VALUES ('$f_clean', '$l_clean', '$e_clean', '$u_clean', '$hashed_password', '$ut_clean')";
            
            if ($conn->query($query) === TRUE) {
                $message = "✨ Success! Your account profile has been created. You can now log in!";
                $message_type = "success";
            } else {
                $message = "❌ Error processing account registration: " . $conn->error;
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Brush & Canvas</title>
    
    <style type="text/css">
        .compact-form-card {
            max-width: 500px;
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
        .input-label { font-weight: 600; font-size: 13.5px; color: #1e293b; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 11px 14px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 15px; color: #334155; outline: none; box-sizing: border-box; }
        .form-control:focus { border-color: #d97706; box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15); }
        .submit-btn { width: 100%; padding: 12px; border-radius: 6px; border: none; font-weight: 600; font-size: 15px; background-color: #d97706; color: white; cursor: pointer; margin-top: 10px; transition: background 0.15s; }
        .submit-btn:hover { background-color: #b45309; }
    </style>
    
    <script>
        function toggleSecretCode() {
            var selectedRole = document.getElementById("user_type").value;
            var inputWrapper = document.getElementById("code-field-wrapper");
            if (selectedRole === "admin" || selectedRole === "instructor") {
                inputWrapper.style.display = "block";
                document.getElementById("code").setAttribute("required", "required");
            } else {
                inputWrapper.style.display = "none";
                document.getElementById("code").removeAttribute("required");
            }
        }
    </script>
</head>
<body>

<table width="100%" height="85vh" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle">

            <div class="compact-form-card" style="margin-top: 20px; margin-bottom: 40px;">
                <h2 class="form-heading">Register New Account</h2>

                <?php if (!empty($message)) : ?>
                    <div class="alert alert-<?= $message_type; ?>" style="margin-bottom: 20px; padding: 12px; border-radius: 5px;">
                        <?= htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="signup.php">
                    <div class="input-row">
                        <label for="user_type" class="input-label">Select Your Account Role</label>
                        <select id="user_type" name="user_type" class="form-control" onchange="toggleSecretCode()" required>
                            <option value="student">Student Account</option>
                            <option value="instructor">Faculty Instructor Account</option>
                            <option value="admin">System Administrator Account</option>
                        </select>
                    </div>

                    <div class="input-row-split">
                        <div class="input-row">
                            <label for="fname" class="input-label">First Name</label>
                            <input type="text" id="fname" name="fname" class="form-control" placeholder="John" required>
                        </div>
                        <div class="input-row">
                            <label wholesalers for="lname" class="input-label">Last Name</label>
                            <input type="text" id="lname" name="lname" class="form-control" placeholder="Smith" required>
                        </div>
                    </div>

                    <div class="input-row">
                        <label for="email" class="input-label">E-Mail Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="jsmith@domain.com" required>
                    </div>

                    <div class="input-row">
                        <label for="username" class="input-label">Choose a Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="e.g. jsmith24" required>
                    </div>

                    <div class="input-row">
                        <label for="password" class="input-label">Create Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="input-row" id="code-field-wrapper" style="display: none;">
                        <label for="code" class="input-label">Faculty / Admin Verification Code</label>
                        <input type="password" id="code" name="code" class="form-control" placeholder="Enter verification code">
                    </div>

                    <button type="submit" class="submit-btn">Create Account</button>
                </form>

                <div style="text-align: center; margin-top: 20px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <p style="font-size: 14px; color: #64748b;">Already have an account? <a href="login.php" style="color: #d97706; font-weight: 600; text-decoration: none;">Sign In</a></p>
                </div>
            </div>

        </td>
    </tr>
</table>

</body>
</html>