<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$nickname = $post['nickname'];
$user_id = $post['userid'];
$message = $post['message'];
$cur_age = $post['cur_age'];
$pos_age = $post['pos_age'];
$category = $post['category'];

$sql = "INSERT INTO draft (nickname,message,cur_age,pos_age,user_id,category) VALUES(?,?,?,?,?,?)";
$stmt = $dbh->prepare($sql);
$data[] = $nickname;
$data[] = $message;
$data[] = $cur_age;
$data[] = $pos_age;
$data[] = $user_id;
$data[] = $category;
$stmt->execute($data);

$stmt = null;
$dbh = null;

setcookie('ajax', 'draft-add', time() + 1);

header('Content-type: application/json');
