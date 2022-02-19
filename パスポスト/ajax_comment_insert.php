<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
require_once('./common/function.php');
$post = sanitize($_POST);
$letter_id = $post['letterid'];
$id = $post['userid'];
$comment = $post['comment'];
$word_check = $post['wordcheck'];

$sql = 'INSERT INTO comment(letter_id,user_id,comment,ng_word) VALUES(?,?,?,?)';
$stmt = $dbh->prepare($sql);
$data[] = $letter_id;
$data[] = $id;
$data[] = $comment;
$data[] = $word_check;
$stmt->execute($data);

$sql = "SELECT user_id FROM letter WHERE id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
$letter = $stmt->fetch(PDO::FETCH_ASSOC);

if ($id != $letter['user_id']) {
    $sql = 'INSERT INTO notification(letter_id,from_id,to_id,type) VALUES(?,?,?,?)';
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $letter_id;
    $data[] = $id;
    $data[] = $letter['user_id'];
    $data[] = 2;
    $stmt->execute($data);
}

$sql = 'UPDATE letter SET comment=comment+1 WHERE id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));

$stmt = null;
$dbh = null;
