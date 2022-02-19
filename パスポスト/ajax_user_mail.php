<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$name = $post['name'];
$email = $post['email'];
$pass = $post['pass'];
$enc_pass = password_hash($pass, PASSWORD_DEFAULT);
$key = bin2hex(random_bytes(5));
$enc_name = openssl_encrypt($name, 'aes-256-cbc', $key);
$enc_email = openssl_encrypt($email, 'aes-256-cbc', $key);
$data = [];

$sql = 'SELECT * FROM pp_user WHERE email=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($email));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rec == false) {

    $text = $name . "さん\n\n";
    $text .= "パスポストをご利用いただきありがとうございます。\n";
    $text .= "メールアドレスを確認するには下記のURLをブラウザにコピーしてください。\n\n";
    $text .= "http://www.pas-post.com/user_done.php?e=" . $enc_email . "&n=" . $enc_name . "&p=" . $enc_pass . "&k=" . $key . "\n\n\n";
    $text .= "心当たりがない場合は、本メールは破棄してください。\n";
    $text .= "引き続きのご利用をよろしくお願いいたします。\n\n";
    $text .= "┌─┌─┌─┌─┌─┌─┌─┌─┌─\n\n";
    $text .= "パスポスト\n";
    $text .= "Webサイト：https://pas-post.com\n";
    $text .= "メールアドレス：info@pas-post.com\n\n";
    $text .= "─┘─┘─┘─┘─┘─┘─┘─┘─┘";
    $title = '[パスポスト]メールアドレスの確認';
    $header = 'From:' . mb_encode_mimeheader('パスポスト') . '<info@pas-post.com>';
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');
    mb_send_mail($email, $title, $text, $header);

    $data['check'] = 1;
    $data['email'] = $email;
} else {
    $data['check'] = 0;
}

$stmt = null;
$dbh = null;

header('Content-type: application/json');
echo json_encode($data);
