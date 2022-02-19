<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$draft_id = $post['draftid'];
$message = $post['message'];
$cur_age = $post['cur_age'];
$pos_age = $post['pos_age'];
$category = $post['category'];

$sql = "UPDATE draft SET message=?,cur_age=?,pos_age=?,category=? WHERE id=?";
$stmt = $dbh->prepare($sql);
$data[] = $message;
$data[] = $cur_age;
$data[] = $pos_age;
$data[] = $category;
$data[] = $draft_id;
$stmt->execute($data);

$stmt = null;
$dbh = null;

setcookie('ajax', 'draft-update', time() + 1);

header('Content-type: application/json');
