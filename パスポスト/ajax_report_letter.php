<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$letter_id = $post['letterid'];
$user_id = $post['userid'];
$reason = $post['reason'];

$sql = 'SELECT user_id FROM letter WHERE id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "INSERT INTO report_letter (from_id,to_id,letter_id,reason) VALUES(?,?,?,?)";
$stmt = $dbh->prepare($sql);
$data[] = $user_id;
$data[] = $rec['user_id'];
$data[] = $letter_id;
$data[] = $reason;
$stmt->execute($data);

$stmt = null;
$dbh = null;

setcookie('ajax', 'report-letter', time() + 1);
header('Content-type: application/json');
