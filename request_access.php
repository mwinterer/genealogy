<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Here you would implement the logic to email mike@winterer.com
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
        <textarea name="reason" placeholder="Please explain why you want to access the genealogy." required></textarea>
        <button type="submit">Request Access</button>
    </form>
</body>
</html>
