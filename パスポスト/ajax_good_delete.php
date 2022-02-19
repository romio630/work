<?php
require_once('./common/dbconnect.php');
require_once('./common/function.php');
$letter_id = $_POST['letterid'];
$user_id = $_POST['userid'];
$comment = $_POST['comment'];
$good = $_POST['good'];

$sql = "UPDATE letter SET good=good-1 WHERE id=?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));

$sql = 'DELETE FROM good_list WHERE letter_id=? AND giver_id=?';
$stmt = $dbh->prepare($sql);
$data[] = $letter_id;
$data[] = $user_id;
$stmt->execute($data);

$sql = "SELECT user_id FROM letter WHERE id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user_id != $row['user_id']) {
    $sql = 'DELETE FROM notification WHERE letter_id=? AND from_id=? AND to_id=?';
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $letter_id;
    $data[] = $user_id;
    $data[] = $row['user_id'];
    $stmt->execute($data);
}

$stmt = null;
$dbh = null;
?>

<li class="comment-btn">
    <div class="heart-bg"></div>
    <div class="comment"></div>
    <span><?= number_unit($comment) ?></span>
</li>
<li class="good-btn clicked" data-id="<?= $letter_id ?>" data-comment="<?= $comment ?>" data-good="<?= $good - 1 ?>">
    <div class="heart-bg"></div>
    <div class="heart-before"></div>
    <div class="good-count">
        <span><?= number_unit($good - 1) ?></span>
        <span><?= number_unit($good) ?></span>
    </div>
</li>