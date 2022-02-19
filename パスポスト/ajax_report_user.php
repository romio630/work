<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$from_id = $post['userid'];
$user_id = $post['toid'];
$reason = $post['reason'];

$sql = "INSERT INTO report_user (from_id,user_id,reason) VALUES(?,?,?)";
$stmt = $dbh->prepare($sql);
$data[] = $from_id;
$data[] = $user_id;
$data[] = $reason;
$stmt->execute($data);

$stmt = null;
$dbh = null;

setcookie('ajax', 'report-user', time() + 1);
header('Content-type: application/json');
