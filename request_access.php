<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $login_name = $_POST['login_name'];
    $reason = $_POST['reason'];

    // Here you would implement the logic to email mike@winterer.com
    // The email should include the following information:
    // Name: $name
    // Email: $email
    // Desired Login Name: $login_name
    // Reason: $reason

    // For now, we'll just redirect to the login page
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Request Access</title>
</head>
<body>
    <h1>Request Access</h1>
    <form method="POST">
        <input type="text" name="name" placeholder="Your Name" required><br>
        <input type="email" name="email" placeholder="Your Email Address" required><br>
        <input type="text" name="login_name" placeholder="Desired Login Name" required><br>
        <textarea name="reason" placeholder="Please explain why you want to access the genealogy." required></textarea><br>
        <button type="submit">Request Access</button>
    </form>
</body>
</html>
