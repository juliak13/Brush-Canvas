<?php
session_start();
include('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brush & Canvas - Art Studio Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="hero" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 60px 20px; text-align: center; margin-bottom: 20px;">
        <div class="container" style="max-width: 800px; margin: 0 auto;">
            <h1 style="color: white; font-size: 42px; font-weight: 700; margin-bottom: 15px; letter-spacing: -1px;">Explore Your Creativity</h1>
            <p style="color: #cbd5e1; font-size: 18px; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto; text-align: center;">
                Beginner-friendly studio art courses designed for aspiring artists, designers, and creative minds.
            </p>
            <?php if (isset($_SESSION['valid_user'])): ?>
                <a href="cregistration.php" class="save-btn" style="background-color: #d97706; text-decoration: none; display: inline-block; color: white;">Browse Our Studio Courses</a>
            <?php else: ?>
                <a href="login.php" class="save-btn" style="background-color: #d97706; text-decoration: none; display: inline-block; color: white;">Get Started Instantly</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="container">
        <section class="course-section" style="margin-bottom: 40px;">
            <h2 style="text-align: center; margin-bottom: 25px;">Featured Course Categories</h2>
            
            <table width="100%" bgcolor="#f8f9fa" cellpadding="0" cellspacing="16" border="0">
                <tr>
                    <td width="33.33%" valign="top" style="background: transparent;">
                        <div class="class-card" style="height: 100%; min-height: 220px; margin: 0; display: flex; flex-direction: column;">
                            <div class="card-badge" style="background-color:#fee2e2; color:#ef4444;">Studio</div>
                            <h3>Drawing</h3>
                            <p class="class-desc">Grasp classic sketching fundamentals, precise pencil shading lines, anatomy proportions, and linear perspectives.</p>
                        </div>
                    </td>
                    <td width="33.33%" valign="top" style="background: transparent;">
                        <div class="class-card" style="height: 100%; min-height: 220px; margin: 0; display: flex; flex-direction: column;">
                            <div class="card-badge" style="background-color:#e0f2fe; color:#0284c7;">Studio</div>
                            <h3>Painting</h3>
                            <p class="class-desc">Explore color theories, acrylic blending dynamics, composition building, and advanced restoration methodologies.</p>
                        </div>
                    </td>
                    <td width="33.33%" valign="top" style="background: transparent;">
                        <div class="class-card" style="height: 100%; min-height: 220px; margin: 0; display: flex; flex-direction: column;">
                            <div class="card-badge" style="background-color:#fef3c7; color:#d97706;">Studio</div>
                            <h3>Calligraphy</h3>
                            <p class="class-desc">Practice intricate lettering structures, traditional ink pen control mechanics, and beautiful historical typography.</p>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </main>

    <footer style="text-align: center; padding: 30px 20px; color: #94a3b8; border-top: 1px solid #e2e8f0; font-size: 13px;">
        Brush & Canvas • Database Management Systems Studio Architecture Project
    </footer>

</body>
</html>