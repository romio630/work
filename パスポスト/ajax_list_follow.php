<?php
require_once('./common/dbconnect.php');
$follow_id = $_POST['followid'];
$user_id = $_POST['userid'];
$nickname = $_POST['nickname'];

$sql = "INSERT INTO follow (following_id,follower_id) VALUES(?,?)";
$stmt = $dbh->prepare($sql);
$data[] = $follow_id;
$data[] = $user_id;
$stmt->execute($data);

$sql = 'INSERT INTO notification(from_id,to_id,type) VALUES(?,?,?)';
$stmt = $dbh->prepare($sql);
$data = [];
$data[] = $user_id;
$data[] = $follow_id;
$data[] = 3;
$stmt->execute($data);

$stmt = null;
$dbh = null;
?>
<button class="list-unfollow-confirm" data-id="<?= $follow_id ?>" data-name="<?= $nickname ?>">
    <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
    <span class="btn-text">フォロー解除</span>
</button>