<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$draft_id = $post['draftid'];

$sql = "DELETE FROM draft WHERE id=?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($draft_id));

$stmt = null;
$dbh = null;

setcookie('ajax', 'draft-delete', time() + 1);

header('Content-type: application/json');
