<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$check1 = $post['check1'];
$data = 0;

$sql = 'SELECT word FROM ng_word';
$stmt = $dbh->query($sql);
while ($rec = $stmt->fetch()) {
    $word_list[] = $rec['word'];
}

foreach ($word_list as $word) {
    if (strpos($check1, $word) !== false) {
        $data++;
        break;
    }
}

$stmt = null;
$dbh = null;

header('Content-type: application/json');
echo json_encode($data);
