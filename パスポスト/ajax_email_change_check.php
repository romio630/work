<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$id = $post['id'];
$email = $post['email'];

$sql = 'SELECT nickname FROM pp_user WHERE email=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($email));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = null;
$dbh = null;

if ($rec == false) {
    $data = 0;
} else {
    $data = $email;

    $text = $rec['nickname'] . "さん\n\n";
    $text .= "パスポストをご利用いただきありがとうございます。\n\n";
    $text .= "以下のURLをクリックして、メールアドレスの変更手続きを完了してください。\n";
    $text .= "https://www.pas-post.com/email_change_done.php?email=" . $email . "&id=" . $id . "\n\n\n";
    $text .= "┌─┌─┌─┌─┌─┌─┌─┌─┌─\n\n";
    $text .= "パスポスト\n";
    $text .= "Webサイト：https://pas-post.com\n";
    $text .= "メールアドレス：info@pas-post.com\n\n";
    $text .= "─┘─┘─┘─┘─┘─┘─┘─┘─┘";
    $title = '【パスポスト】メールアドレス変更手続きを完了させてください';
    $header = 'From:' . mb_encode_mimeheader('パスポスト') . '<info@pas-post.com>';
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');
    mb_send_mail($email, $title, $text, $header);
}

header('Content-type: application/json');
echo json_encode($data);
