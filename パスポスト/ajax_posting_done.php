<?php
date_default_timezone_set('Asia/Tokyo');
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$word_check = $post['wordcheck'];
$nickname = $post['nickname'];
$user_id = $post['userid'];
$message = $post['message'];
$cur_age = $post['cur_age'];
$pos_age = $post['pos_age'];
$category = $post['category'];

$sql = "INSERT INTO letter (nickname,message,cur_age,pos_age,user_id,category,ng_word) VALUES(?,?,?,?,?,?,?)";
$stmt = $dbh->prepare($sql);
$data[] = $nickname;
$data[] = $message;
$data[] = $cur_age;
$data[] = $pos_age;
$data[] = $user_id;
$data[] = $category;
$data[] = $word_check;
$stmt->execute($data);

$stmt = null;
$dbh = null;

header('Content-type: application/json');
