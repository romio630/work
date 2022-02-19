<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$letter_id = $post['letterid'];
$message = $post['message'];
$cur_age = $post['cur_age'];
$pos_age = $post['pos_age'];
$category = $post['category'];
$word_check = $post['wordcheck'];

$sql = "UPDATE letter SET message=?,cur_age=?,pos_age=?,category=?,ng_word=? WHERE id=?";
$stmt = $dbh->prepare($sql);
$data[] = $message;
$data[] = $cur_age;
$data[] = $pos_age;
$data[] = $category;
$data[] = $word_check;
$data[] = $letter_id;
$stmt->execute($data);

$sql = "UPDATE letter SET edit=? WHERE id=?";
$data = [];
$data[] = 1;
$data[] = $letter_id;
$stmt = $dbh->prepare($sql);
$stmt->execute($data);

$stmt = null;
$dbh = null;

setcookie('ajax', 'letter-edit', time() + 1);

header('Content-type: application/json');
