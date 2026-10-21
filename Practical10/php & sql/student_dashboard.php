<?php

$required_role = 'student';           
require_once __DIR__ . '/auth.php';  

$student_email = htmlspecialchars($_SESSION['p10_email'] ?? 'Student');
$student_role  = htmlspecialchars($_SESSION['p10_role']  ?? 'student');
$idle_since    = time() - ($_SESSION['p10_last_activity'] ?? time());
$mins_remaining = max(0, (int) ceil((900 - $idle_since) / 60));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Student Dashboard — StudentHub Portal Practical 10">
    <title>Student Dashboard — StudentHub Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Session timeout warning */
        .p10-timeout-bar {
            background: #fff3cd;
            color: #856404;
            border-bottom: 2px solid #ffc107;
            padding: 10px 20px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }
        /* Session info badge */
        .p10-session-info {
            max-width: 700px;
            margin: 0 auto 20px;
            background: #e8f4f8;
            border: 1px solid #bee5eb;
            border-radius: 12px;
            padding: 14px 20px;
            color: #0c5460;
            font-size: 14px;
        }
        .p10-session-info code {
            background: #c5e8ef;
            padding: 2px 6px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <!-- Session timeout warning bar (shows when less than 3 minutes remain) -->
    <?php if ($mins_remaining <= 3): ?>
    <div class="p10-timeout-bar" id="timeoutWarning">
        &#9888; Your session will expire in approximately <strong><?php echo $mins_remaining; ?> minute(s)</strong>.
        Any page action will reset the timer.
    </div>
    <?php endif; ?>

    <h1>Student Dashboard</h1>

    <nav>
        <a href="../mysql/index.html">Home</a> &nbsp;
        <a href="login.php">Login</a> &nbsp;
        <a href="register.php">Register</a> &nbsp;
        <a href="student_dashboard.php">Dashboard</a> &nbsp;
        <a href="logout.php">Logout</a> &nbsp;
    </nav>

    <!-- Session information panel (useful for demonstration) -->
    <div class="p10-session-info">
        &#128100; Logged in as: <code><?php echo $student_email; ?></code> &nbsp;|&nbsp;
        Role: <code><?php echo $student_role; ?></code> &nbsp;|&nbsp;
        Session timeout in: <code><?php echo $mins_remaining; ?> min</code>
    </div>

    <div style="text-align:center; font-weight:bold; margin-bottom:15px; background:transparent; box-shadow:none;">
        Welcome, <?php echo $student_email; ?>! You are logged in as a <strong>Student</strong>.
    </div>

    <main>

        <section>
            <h3>&#128197; Time Table</h3>
            <p>View your class schedule and upcoming sessions.</p>
            <a href="../mysql/timetable.html">View Time Table</a>
        </section>

        <section>
            <h3>&#128218; Study Material &amp; Courses</h3>
            <p>Access your course study materials and resources.</p>
            <a href="../mysql/courses.html">View Study Material</a>
        </section>

        <section>
            <h3>&#128203; Assignments</h3>
            <p>View and manage your pending assignments.</p>
            <a href="../mysql/assignment.html">View Assignments</a>
        </section>

        <section>
            <h3>&#128200; Results</h3>
            <p>Check your academic performance and grades.</p>
            <a href="../mysql/result.html">View Results</a>
        </section>

        <section>
            <h3>&#128101; About Us</h3>
            <p>Learn about the university and its facilities.</p>
            <a href="../mysql/about.html">About StudentHub</a>
        </section>

        <section>
            <h3>&#128222; Contact Us</h3>
            <p>Get in touch with the university or support team.</p>
            <a href="../mysql/contact.html">Contact</a>
        </section>

    </main>

    <script>
    
    (function () {
        var timeoutSeconds = 900; 
        var warningSeconds = 180; 
        var bar = document.getElementById('timeoutWarning');
        var secondsLeft = timeoutSeconds - <?php echo $idle_since; ?>;

        if (secondsLeft <= 0) return;

        // Update every 30 seconds
        setInterval(function () {
            secondsLeft -= 30;
            if (secondsLeft <= 0) {
                // Redirect to login; server will also catch this
                window.location.href = 'login.php?reason=timeout';
            } else if (secondsLeft <= warningSeconds) {
                var minsLeft = Math.ceil(secondsLeft / 60);
                if (bar) {
                    bar.style.display = 'block';
                    bar.innerHTML = '&#9888; Your session will expire in approximately <strong>' +
                        minsLeft + ' minute(s)</strong>. Any page action will reset the timer.';
                }
            }
        }, 30000);
    })();
    </script>

</body>
</html>
