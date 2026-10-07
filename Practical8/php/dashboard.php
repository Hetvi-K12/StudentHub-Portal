<?php
// =======================================================================
// STUDENTHUB PORTAL - PRACTICAL 8 STUDENT DASHBOARD (dashboard.php)
// =======================================================================

session_start();

// Protected page: require PHP session
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_email = $_SESSION['student_email'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StudentHub</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Student Dashboard</h1>
    <div style="text-align: center; font-weight: bold; margin-bottom: 15px; background: transparent; box-shadow: none;">
        Welcome, <?php echo htmlspecialchars($student_email); ?>!
    </div>
    <nav>
        <a href="index.html">Home</a> &nbsp;  
        <a href="dashboard.php">Dashboard</a> &nbsp;  
        <!-- <a href="events.php">Events</a> &nbsp;   -->
        <a href="about.html">About Us</a> &nbsp;
        <a href="contact.html">Contact Us</a> &nbsp;
        <a href="profile.html">Profile</a> &nbsp;
        <br>
    </nav>
    <main>

        <!-- <section>
            <h3>Campus Events</h3>
            <p>Explore & register for upcoming university events.</p>
            <a href="events.php">View Events</a>
        </section> -->

        <section>
            <h3>Time Table</h3>
            <p>View your Time Table.</p>
            <a href="timetable.html">View Time Table</a>
        </section>

        <section>
            <h3>Study Material & Courses</h3>
            <p>Access your course study materials.</p>
            <a href="courses.html">View Study Material</a>
        </section>

        <section>
            <h3>Assignments</h3>
            <p>View and manage your assignments.</p>
            <a href="assignment.html">View Assignments</a>
        </section>

        <section>
            <h3>Results</h3>
            <p>Check your academic results.</p>
            <a href="result.html">View Results</a>
        </section>

    </main>
    <script src="script.js"></script>
</body>
</html>
