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
    $sql = "UPDATE hide_status SET status=1 WHERE from_id=? and to_id=?";
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $follow_id;
    $data[] = $user_id;
    $stmt->execute($data);

    $sql = "INSERT INTO follow(following_id,follower_id) VALUES(?,?)";
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $user_id;
    $data[] = $follow_id;
    $stmt->execute($data);

    $sql = 'DELETE FROM notification WHERE from_id=? and to_id=? and type=4';
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $follow_id;
    $data[] = $user_id;
    $stmt->execute($data);
    $data = 1;
} else {
    $data = 0;
}

$stmt = null;
$dbh = null;
header('Content-type: application/json');
echo json_encode($data);
