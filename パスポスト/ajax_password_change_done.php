<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$id = $post['id'];
$pass = $post['pass'];
$new_pass = $post['newpass'];
$new_pass_hash = password_hash($new_pass, PASSWORD_DEFAULT);

$sql = 'SELECT password FROM pp_user WHERE id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);
if (password_verify($pass, $rec['password'])) {
    $sql = 'UPDATE pp_user SET password=? WHERE id=?';
    $stmt = $dbh->prepare($sql);
    $data[] = $new_pass_hash;
    $data[] = $id;
    $stmt->execute($data);
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    $data = 1;
} else {
    $data = 0;
}

$stmt = null;
$dbh = null;

setcookie('ajax', 'password-change', time() + 1);

header('Content-type: application/json');
echo json_encode($data);
