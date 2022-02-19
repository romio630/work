<?php
date_default_timezone_set('Asia/Tokyo');
require_once('./common/sanitize.php');
require_once('./common/terminalinfo.php');
$terminal = terminalinfo();
$post = sanitize($_POST);
$email = $post['email'];
$pass = $post['pass'];

require_once('./common/dbconnect.php');
$sql = 'SELECT id,nickname,password,icon FROM pp_user WHERE email=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($email));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rec == false) {
    $j = 1;
} else {
    if (password_verify($pass, $rec['password'])) {
        session_start();
        $_SESSION['nickname'] = $rec['nickname'];
        $_SESSION['id'] = $rec['id'];
        $_SESSION['icon'] = $rec['icon'];
        setcookie('paspost_id', $rec['id'], time() + 60 * 60 * 24 * 14);
        $j = 3;

        $sql = 'INSERT INTO login(user_id,ip_address,terminal) VALUES(?,?,?)';
        $stmt = $dbh->prepare($sql);
        $data[] = $rec['id'];
        $data[] = $ip_address;
        $data[] = $terminal;
        $stmt->execute($data);

        // $text = $rec['nickname'] . "さん\n\n";
        // $text .= "パスポストをご利用いただきありがとうございます。\n\n";
        // $text .= "こちらはご登録いただいているアカウントを使ってパスポストへログインしたことを通知するメールです。\n\n";
        // $text .= "■詳細情報\n";
        // $text .= "ログイン日時：" . date_create()->format('Y/m/d H:i') . "\n\n";
        // $text .= "┌─┌─┌─┌─┌─┌─┌─┌─┌─\n\n";
        // $text .= "パスポスト\n";
        // $text .= "Webサイト：https://pas-post.com\n";
        // $text .= "メールアドレス：info@pas-post.com\n\n";
        // $text .= "─┘─┘─┘─┘─┘─┘─┘─┘─┘";
        // $title = '【パスポスト】ログイン通知';
        // $header = 'From:' . mb_encode_mimeheader('パスポスト') . '<info@pas-post.com>';
        // $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        // mb_language('Japanese');
        // mb_internal_encoding('UTF-8');
        // mb_send_mail($email, $title, $text, $header);
    } else {
        $j = 2;
    }
}

$stmt = null;
$dbh = null;

header('Content-type: application/json');
echo json_encode($j);
