<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$letter_id = $post['letterid'];

$sql = "DELETE FROM letter WHERE id=?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));

$stmt = null;
$dbh = null;

setcookie('ajax', 'letter-delete', time() + 1);

header('Content-type: application/json');
