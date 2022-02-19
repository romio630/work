<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$nickname = $post['nickname'];
$id = $post['id'];
$contents = $post['contents'];
$star = $post['star'];

$sql = 'INSERT INTO questionnaire(user_id,nickname,contents,star) VALUES(?,?,?,?)';
$stmt = $dbh->prepare($sql);
$data[] = $id;
$data[] = $nickname;
$data[] = $contents;
$data[] = $star;
$stmt->execute($data);

$stmt = null;
$dbh = null;

$text = "■ニックネーム\n";
$text .= $nickname . "\n\n";
$text .= "■アンケート内容\n";
$text .= $contents . "\n\n";
$text .= "■５段階評価\n";
$text .= $star . "\n\n";
$title = 'アンケートにご協力いただきました';
$header = 'From:' . $email;
$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
mb_language('Japanese');
mb_internal_encoding('UTF-8');
mb_send_mail("info@pas-post.com", $title, $text, $header);

header('Content-type: application/json');
