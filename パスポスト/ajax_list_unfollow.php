<?php
require_once('./common/dbconnect.php');
$unfollow_id = $_POST['unfollowid'];
$user_id = $_POST['userid'];
$nickname = $_POST['nickname'];

$sql = "DELETE FROM follow WHERE following_id=? AND follower_id=?";
$stmt = $dbh->prepare($sql);
$data[] = $unfollow_id;
$data[] = $user_id;
$stmt->execute($data);

$sql = 'DELETE FROM notification WHERE from_id=? AND to_id=?';
$stmt = $dbh->prepare($sql);
$data = [];
$data[] = $user_id;
$data[] = $unfollow_id;
$stmt->execute($data);

$stmt = null;
$dbh = null;
?>
<button class="list-follow-btn" data-id="<?= $unfollow_id ?>" data-name="<?= $nickname ?>">
    <span class=" btn-text"><img src="./img/plus.svg">フォロー</span>
</button>