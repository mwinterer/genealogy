<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$gedcom_files = glob('data/*.ged');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Genealogy</title>
</head>
<body>
    <h1>Available Genealogies</h1>
    <a href="logout.php">Logout</a>
    <hr>
    <h2>Upload a GEDCOM File</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        Select GEDCOM file to upload:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload File" name="submit">
    </form>
    <hr>
    <ul>
        <?php foreach ($gedcom_files as $file): ?>
            <li><a href="gedcom.php?file=<?php echo basename($file); ?>"><?php echo basename($file); ?></a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
