<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$comment_id = $post['commentid'];
$letter_id = $post['letterid'];

$sql = 'UPDATE comment SET is_delete=1 WHERE id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($comment_id));

$sql = 'UPDATE letter SET comment=comment-1 WHERE id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));

$stmt = null;
$dbh = null;

header('Content-type: application/json');
