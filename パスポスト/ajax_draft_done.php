<?php
date_default_timezone_set('Asia/Tokyo');
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$nickname = $post['nickname'];
$user_id = $post['userid'];
$message = $post['message'];
$cur_age = $post['cur_age'];
$pos_age = $post['pos_age'];
$category = $post['category'];
$draft_id = $post['draftid'];

$sql = "INSERT INTO letter (nickname,message,cur_age,pos_age,user_id,category) VALUES(?,?,?,?,?,?)";
$stmt = $dbh->prepare($sql);
$data[] = $nickname;
$data[] = $message;
$data[] = $cur_age;
$data[] = $pos_age;
$data[] = $user_id;
$data[] = $category;
$stmt->execute($data);

$sql = "DELETE FROM draft WHERE id=?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($draft_id));

$stmt = null;
$dbh = null;

header('Content-type: application/json');
