<?php
require_once('./common/dbconnect.php');
require_once('./common/function.php');
$letter_id = $_POST['letterid'];
$id = $_POST['userid'];

$sql = "UPDATE letter SET good=good-1 WHERE id=?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));

$sql = 'DELETE FROM good_list WHERE letter_id=? AND giver_id=?';
$stmt = $dbh->prepare($sql);
$data[] = $letter_id;
$data[] = $id;
$stmt->execute($data);

$sql = "SELECT user_id FROM letter WHERE id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($id != $row['user_id']) {
    $sql = 'DELETE FROM notification WHERE letter_id=? AND from_id=? AND to_id=?';
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $letter_id;
    $data[] = $id;
    $data[] = $row['user_id'];
    $stmt->execute($data);
}

$sql = "SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,U.icon 
FROM letter as L join pp_user as U on L.user_id=U.id WHERE L.id=? ORDER BY created_at DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT U.id,U.nickname,U.icon FROM pp_user as U join good_list as G on U.id=G.giver_id WHERE G.letter_id=? ORDER BY G.created_at DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
while ($good_user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $good_user_list[] = $good_user;
}
if (!isset($good_user_list)) {
    $good_user_list = [];
}

$stmt = null;
$dbh = null;

?>

<li class="comment-btn">
    <div class="heart-bg"></div>
    <div class="comment"></div>
    <span><?= number_unit($rec['comment']) ?></span>
</li>
<li class="letter-good-btn" data-id="<?= $rec['id'] ?>">
    <div class="heart-bg"></div>
    <div class="heart-before"></div>
    <div class="good-count">
        <span><?= number_unit($rec['good']) ?></span>
        <span><?= number_unit($rec['good'] + 1) ?></span>
    </div>
</li>
<?php if (count($good_user_list) > 0) : ?>
    <li class="good-user-btn">件のいいね</li>
<?php endif ?>