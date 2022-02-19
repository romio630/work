<?php
require_once('./common/dbconnect.php');
$follow_id = $_POST['followid'];
$user_id = $_POST['userid'];

$sql = 'SELECT id from notification where from_id=? and to_id=? and type=4';
$stmt = $dbh->prepare($sql);
$data[] = $follow_id;
$data[] = $user_id;
$stmt->execute($data);
$rec = $stmt->fetch(PDO::FETCH_ASSOC);
if (!isset($rec)) {
    $rec = null;
}

if ($rec != null) {
    $sql = "DELETE FROM hide_status WHERE from_id=? and to_id=? and status=0";
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $follow_id;
    $data[] = $user_id;
    $stmt->execute($data);

    $sql = 'DELETE FROM notification WHERE from_id=? and to_id=? and type=4';
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $follow_id;
    $data[] = $user_id;
    $stmt->execute($data);
}

$stmt = null;
$dbh = null;
header('Content-type: application/json');
