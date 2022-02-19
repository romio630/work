<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$email = $post['email'];

$sql = 'SELECT id,nickname FROM pp_user WHERE email=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($email));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$text = $rec['name'] . "さん\n\n";
$text .= "パスポストをご利用いただきありがとうございます。\n";
$text .= "パスワードの再設定は以下のURLからお願いします。\n\n";
$text .= "http://www.pas-post.com/password_reset.php?id=" . $rec['id'] . "\n\n\n";
$text .= "┌─┌─┌─┌─┌─┌─┌─┌─┌─\n\n";
$text .= "パスポスト\n";
$text .= "Webサイト：https://pas-post.com\n";
$text .= "メールアドレス：info@pas-post.com\n\n";
$text .= "─┘─┘─┘─┘─┘─┘─┘─┘─┘";
$title = '[パスポスト]パスワードの再設定';
$header = 'From:' . mb_encode_mimeheader('パスポスト') . '<info@pas-post.com>';
$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
mb_language('Japanese');
mb_internal_encoding('UTF-8');
mb_send_mail($email, $title, $text, $header);

$stmt = null;
$dbh = null;

header('Content-type: application/json');
echo json_encode($email);
