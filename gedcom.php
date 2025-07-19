<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'gedcom_parser.php';

$file = 'data/' . $_GET['file'];

$parser = new GedcomParser();
$gedcom = $parser->parse($file);

?>
<!DOCTYPE html>
<html>
<head>
    <title>People in <?php echo basename($file); ?></title>
</head>
<body>
    <h1>People in <?php echo basename($file); ?></h1>
    <a href="index.php">Back to Genealogy List</a>
    <hr>
    <input type="text" id="searchInput" onkeyup="search()" placeholder="Search for names..">
    <ul id="peopleList">
        <?php foreach ($gedcom['individuals'] as $id => $individual): ?>
            <li><a href="person.php?file=<?php echo basename($file); ?>&id=<?php echo urlencode($id); ?>"><?php echo $individual['name']; ?></a></li>
        <?php endforeach; ?>
    </ul>

    <script>
    function search() {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById('searchInput');
        filter = input.value.toUpperCase();
        ul = document.getElementById("peopleList");
        li = ul.getElementsByTagName('li');

        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
    </script>
</body>
</html>
