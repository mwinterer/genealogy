<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'gedcom_parser.php';

$file = 'data/' . $_GET['file'];
$person_id = $_GET['id'];

$parser = new GedcomParser();
$gedcom = $parser->parse($file);

$person = $gedcom['individuals'][$person_id];

$parents = [];
if ($person['famc']) {
    $family = $gedcom['families'][$person['famc']];
    if (isset($family['husband'])) {
        $parents[] = $gedcom['individuals'][$family['husband']];
    }
    if (isset($family['wife'])) {
        $parents[] = $gedcom['individuals'][$family['wife']];
    }
}

$children = [];
foreach ($person['fams'] as $fam_id) {
    $family = $gedcom['families'][$fam_id];
    foreach ($family['children'] as $child_id) {
        $children[] = $gedcom['individuals'][$child_id];
    }
}

$siblings = [];
if ($person['famc']) {
    $family = $gedcom['families'][$person['famc']];
    foreach ($family['children'] as $child_id) {
        if ($child_id !== $person_id) {
            $siblings[] = $gedcom['individuals'][$child_id];
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $person['name']; ?></title>
</head>
<body>
    <h1><?php echo $person['name']; ?></h1>
    <a href="gedcom.php?file=<?php echo basename($file); ?>">Back to Index</a>
    <hr>
    <p><strong>Sex:</strong> <?php echo $person['sex']; ?></p>

    <h2>Parents</h2>
    <ul>
        <?php foreach ($parents as $parent): ?>
            <li><a href="person.php?file=<?php echo basename($file); ?>&id=<?php echo urlencode($parent['id']); ?>"><?php echo $parent['name']; ?></a></li>
        <?php endforeach; ?>
    </ul>

    <h2>Children</h2>
    <ul>
        <?php foreach ($children as $child): ?>
            <li><a href="person.php?file=<?php echo basename($file); ?>&id=<?php echo urlencode($child['id']); ?>"><?php echo $child['name']; ?></a></li>
        <?php endforeach; ?>
    </ul>

    <h2>Siblings</h2>
    <ul>
        <?php foreach ($siblings as $sibling): ?>
            <li><a href="person.php?file=<?php echo basename($file); ?>&id=<?php echo urlencode($sibling['id']); ?>"><?php echo $sibling['name']; ?></a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
