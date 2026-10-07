<?php
$message = '';
$status = '';
$user_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($user_message)) {
        $status = 'error';
        $message = 'Submission Failed! Please enter a message.';
    } else {
        $timestamp = date('Y-m-d H:i:s');

        $csv_file = __DIR__ . '/contacts.csv';
        $file_exists = file_exists($csv_file);
        $is_empty = !$file_exists || filesize($csv_file) === 0;

        $fp = fopen($csv_file, 'a');
        if ($fp !== false) {
            if ($is_empty) {
                fputcsv($fp, ['Message', 'Timestamp']);
            }
            fputcsv($fp, [$user_message, $timestamp]);
            fclose($fp);

            $status = 'success';
            $message = 'Message sent successfully!';
            $user_message = '';
        } else {
            $status = 'error';
            $message = 'Unable to write to contacts.csv file.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Contact Us</h1>
    <nav>
        <a href = "index.html">Home</a> &nbsp;  
        <a href = "login.php">Login</a> &nbsp;
        <a href = "register.php">Register</a> &nbsp;
        <a href = "contact.html">Contact</a> &nbsp;
        <a href = "about.html">About</a> 
    </nav>

    <?php if (!empty($message)): ?>
        <div class="php-message php-<?php echo $status; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <h3>Contact Information</h3>

        <p>Email: support@myhub.com</p>
        <p>Phone: +91 9876543210</p>
        <p>Address: University Campus</p>
        <p>Office Timing: 9:00 AM - 5:00 PM</p>

    <form action="contact.php" method="POST" novalidate>
        <label>Message:</label>
        <br>
        <textarea name="message" rows="5" cols="40"><?php echo htmlspecialchars($user_message); ?></textarea>

        <br><br>

        <button type="submit">Send Message</button>
    </form>
    <script src="script.js"></script>
</body>
</html>
