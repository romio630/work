<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$id = $post['id'];
$pass = $post['pass'];

$sql = 'SELECT password FROM pp_user WHERE id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);
if (password_verify($pass, $rec['password'])) {
    $data = 1;
} else {
    $data = 0;
}

$stmt = null;
$dbh = null;

header('Content-type: application/json');
echo json_encode($data);
