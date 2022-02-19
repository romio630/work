<?php
require_once('./common/dbconnect.php');
require_once('./common/sanitize.php');
$post = sanitize($_GET);
$id = $post['id'];
$email = $post['email'];

$sql = 'UPDATE pp_user SET email=? WHERE id=?';
$stmt = $dbh->prepare($sql);
$data[] = $email;
$data[] = $id;
$stmt->execute($data);

$stmt = null;
$dbh = null;

header('Location:configuration.php');
exit;
