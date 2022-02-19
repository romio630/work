<?php
require_once('./common/dbconnect.php');
require_once('./common/function.php');
$letter_id = $_POST['letterid'];
$id = $_POST['userid'];

$sql = "UPDATE letter SET good=good+1 WHERE id=?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));

$sql = 'INSERT INTO good_list(letter_id,giver_id) VALUES(?,?)';
$stmt = $dbh->prepare($sql);
$data[] = $letter_id;
$data[] = $id;
$stmt->execute($data);

$sql = "SELECT user_id FROM letter WHERE id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($id != $row['user_id']) {
    $sql = 'INSERT INTO notification(letter_id,from_id,to_id,type) VALUES(?,?,?,?)';
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $letter_id;
    $data[] = $id;
    $data[] = $row['user_id'];
    $data[] = 1;
    $stmt->execute($data);
}

$sql = "SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,U.icon 
FROM letter as L join pp_user as U on L.user_id=U.id WHERE L.id=? ORDER BY created_at DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = null;
$dbh = null;

?>

<li class="comment-btn">
    <div class="heart-bg"></div>
    <div class="comment"></div>
    <span><?= number_unit($rec['comment']) ?></span>
</li>
<li class="letter-dl-good-btn" data-id="<?= $rec['id'] ?>">
    <div class="heart-bg"></div>
    <div class="heart-after"></div>
    <div class="good-count">
        <span><?= number_unit($rec['good'] - 1) ?></span>
        <span><?= number_unit($rec['good']) ?></span>
    </div>
</li>
<li class="good-user-btn">件のいいね</li>