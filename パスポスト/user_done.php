<?php
require_once('./common/sanitize.php');
require_once('./common/terminalinfo.php');
require_once('./common/dbconnect.php');
$terminal = terminalinfo();
$get = sanitize($_GET);
$key = $get['k'];
$replace_name = preg_replace("/\s/", "+", $get['n']);
$replace_email = preg_replace("/\s/", "+", $get['e']);
$dec_name = openssl_decrypt($replace_name, 'aes-256-cbc', $key);
$dec_email = openssl_decrypt($replace_email, 'aes-256-cbc', $key);
$pass = $get['p'];

$sql = 'SELECT * FROM pp_user WHERE email=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($dec_email));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rec == false) {
    $sql = 'LOCK TABLES pp_user WRITE';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $sql = 'INSERT INTO pp_user(nickname,email,password) VALUES(?,?,?)';
    $stmt = $dbh->prepare($sql);
    $data[] = $dec_name;
    $data[] = $dec_email;
    $data[] = $pass;
    $stmt->execute($data);

    $sql = 'SELECT LAST_INSERT_ID()';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    $lastmemberid = $rec['LAST_INSERT_ID()'];

    $sql = 'UNLOCK TABLES';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $sql = 'INSERT INTO login(user_id,ip_address,terminal) VALUES(?,?,?)';
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $lastmemberid;
    $data[] = $ip_address;
    $data[] = $terminal;
    $stmt->execute($data);

    session_start();
    $_SESSION['nickname'] = $dec_name;
    $_SESSION['id'] = $lastmemberid;
    $_SESSION['icon'] = 'initial.svg';
    setcookie('paspost_id', $lastmemberid, time() + 60 * 60 * 24 * 14);

    $text = $dec_name . "さん\n\n";
    $text .= "パスポストをご利用いただきありがとうございます。\n";
    $text .= "こちらはメールアドレスからユーザ登録が完了したことを通知するメールです。\n";
    $text .= "素敵なお手紙の投函をお待ちしております。\n\n";
    $text .= "┌─┌─┌─┌─┌─┌─┌─┌─┌─\n\n";
    $text .= "パスポスト\n";
    $text .= "Webサイト：https://pas-post.com\n";
    $text .= "メールアドレス：info@pas-post.com\n\n";
    $text .= "─┘─┘─┘─┘─┘─┘─┘─┘─┘";
    $title = '【パスポスト】ユーザ登録完了';
    $header = 'From:' . mb_encode_mimeheader('パスポスト') . '<pastpost.612@gmail.com>';
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');
    mb_send_mail($dec_email, $title, $text, $header);

    header('Location:./index.php');
    exit();
}

$stmt = null;
$dbh = null;

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php include_once('./views/head.html') ?>
    <title><?= $nickname ?>のフォロー中アカウント</title>
</head>

<body>

    <body>
        <?php include_once('./views/header.php') ?>
        <main>
            <?php include_once('./common/back-btn.php') ?>
            <div class="user-done">
                <div class="modal-logo"><img src="./img/logo.svg" alt="パスポストのロゴ"></div>
                <p>このメールアドレスはすでに確認済です</p>
                <div><a href="./login.php" id="login-btn">ログイン</a></div>
            </div>
        </main>
        <?php include_once('./views/footer.html') ?>
        <?php include_once('./views/fixed-menu.php') ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="./js/click-toggle.js"></script>
        <script src="./js/common.js"></script>
        <script src="./js/index.js"></script>
    </body>

</html>