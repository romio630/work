<?php
require_once('./common/sanitize.php');
$post = sanitize($_POST);
$id = $post['id'];
$pass = $post['pass'];
$enc_pass = password_hash($pass, PASSWORD_DEFAULT);

require_once('./common/dbconnect.php');
$sql = 'UPDATE pp_user SET password=? WHERE id=?';
$stmt = $dbh->prepare($sql);
$data[] = $enc_pass;
$data[] = $id;
$stmt->execute($data);

$sql = 'SELECT id,nickname,icon FROM pp_user WHERE id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

session_start();
$_SESSION['nickname'] = $rec['nickname'];
$_SESSION['id'] = $id;
$_SESSION['icon'] = $rec['icon'];
setcookie('paspost_id', $rec['id'], time() + 60 * 60 * 24);

$stmt = null;
$dbh = null;

header('Location:index.php');
exit;
