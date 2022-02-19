<?php
require_once('./common/dbconnect.php');
require_once('./common/function.php');
$letter_id = $_POST['letterid'];
$user_id = $_POST['userid'];
$comment = $_POST['comment'];
$good = $_POST['good'];

$sql = "UPDATE letter SET good=good+1 WHERE id=?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));

$sql = 'INSERT INTO good_list(letter_id,giver_id) VALUES(?,?)';
$stmt = $dbh->prepare($sql);
$data[] = $letter_id;
$data[] = $user_id;
$stmt->execute($data);

$sql = "SELECT user_id FROM letter WHERE id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user_id != $row['user_id']) {
    $sql = 'INSERT INTO notification(letter_id,from_id,to_id,type) VALUES(?,?,?,?)';
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $letter_id;
    $data[] = $user_id;
    $data[] = $row['user_id'];
    $data[] = 1;
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
<li class="dl-good-btn clicked" data-id="<?= $letter_id ?>" data-comment="<?= $comment ?>" data-good="<?= $good + 1 ?>">
    <div class="heart-bg"></div>
    <div class="heart-after"></div>
    <div class="good-count">
        <span><?= number_unit($good) ?></span>
        <span><?= number_unit($good + 1) ?></span>
    </div>
</li>