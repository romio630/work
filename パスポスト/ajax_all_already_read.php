<?php
require_once('./common/dbconnect.php');
$user_id = $_POST['id'];

$sql = "UPDATE notification SET already_read=? WHERE to_id=?";
$stmt = $dbh->prepare($sql);
$data[] = 1;
$data[] = $user_id;
$stmt->execute($data);

$stmt = null;
$dbh = null;

header('Content-type: application/json');
