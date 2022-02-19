<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
date_default_timezone_set('Asia/Tokyo');
$post = sanitize($_POST);
$name = $post['name'];
$email = $post['email'];
$contents = $post['contents'];

$sql = 'INSERT INTO contact(email,name,contents) VALUES(?,?,?)';
$stmt = $dbh->prepare($sql);
$data[] = $email;
$data[] = $name;
$data[] = $contents;
$stmt->execute($data);

$stmt = null;
$dbh = null;

$text = $name . "さん\n\n";
$text .= "お問い合わせいただきありがとうございました。\n\n";
$text .= "ご記入いただいた内容は確認させていただき、パスポストのサービス向上に役立たせていただきます。\n\n";
$text .= "引き続きのご利用をよろしくお願いいたします。\n\n";
$text .= "┌─┌─┌─┌─┌─┌─┌─┌─┌─\n\n";
$text .= "パスポスト\n";
$text .= "Webサイト：https://pas-post.com\n";
$text .= "メールアドレス：info@pas-post.com\n\n";
$text .= "─┘─┘─┘─┘─┘─┘─┘─┘─┘";
$title = '【パスポスト】お問い合わせ通知';
$header = 'From:' . mb_encode_mimeheader('パスポスト') . '<info@pas-post.com>';
$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
mb_language('Japanese');
mb_internal_encoding('UTF-8');
mb_send_mail($email, $title, $text, $header);

$text = "■名前\n";
$text .= $name . "\n\n";
$text .= "■メールアドレス\n";
$text .= $email . "\n\n";
$text .= "■お問い合わせ内容\n";
$text .= $contents . "\n\n";
$title = 'お問い合わせがありました';
$header = 'From:' . $email;
$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
mb_language('Japanese');
mb_internal_encoding('UTF-8');
mb_send_mail("info@pas-post.com", $title, $text, $header);

header('Content-type: application/json');
echo json_encode($email);
