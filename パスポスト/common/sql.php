<?php
$sql = "SELECT sum(good) as good FROM letter WHERE user_id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
$total = $stmt->fetch(PDO::FETCH_ASSOC);
if ($total['good'] == null) {
    $total['good'] = 0;
}

$sql = "SELECT count(*) as cnt from notification WHERE to_id=? AND already_read=?";
$stmt = $dbh->prepare($sql);
$data = [];
$data[] = $id;
$data[] = 0;
$stmt->execute($data);
$unread = $stmt->fetch(PDO::FETCH_ASSOC);
