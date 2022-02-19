<?php
require_once('./common/dbconnect.php');
$follow_id = $_POST['followid'];
$user_id = $_POST['userid'];

$sql = "INSERT INTO hide_status (from_id,to_id,status) VALUES(?,?,?)";
$stmt = $dbh->prepare($sql);
$data[] = $user_id;
$data[] = $follow_id;
$data[] = 0;
$stmt->execute($data);

$sql = 'INSERT INTO notification(from_id,to_id,type) VALUES(?,?,?)';
$stmt = $dbh->prepare($sql);
$data = [];
$data[] = $user_id;
$data[] = $follow_id;
$data[] = 4;
$stmt->execute($data);

$stmt = null;
$dbh = null;
?>
<button class="unapproved-btn">
    <span class="btn-text">未承認</span>
    <span class="btn-text">キャンセル</span>
</button>