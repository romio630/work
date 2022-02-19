<?php
require_once('./common/dbconnect.php');
$follow_id = $_POST['followid'];
$user_id = $_POST['userid'];
$nickname = $_POST['nickname'];

$sql = "DELETE FROM hide_status WHERE from_id=? and to_id=? and status=0";
$stmt = $dbh->prepare($sql);
$data[] = $user_id;
$data[] = $follow_id;
$stmt->execute($data);

$sql = 'DELETE FROM notification WHERE from_id=? and to_id=? and type=4';
$stmt = $dbh->prepare($sql);
$data = [];
$data[] = $user_id;
$data[] = $follow_id;
$stmt->execute($data);

$stmt = null;
$dbh = null;
?>
<button class="list-follow-request-btn" data-id="<?= $follow_id ?>" data-name="<?= $nickname ?>">
    <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
</button>