<?php
session_start();
session_regenerate_id(true);

if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/dbconnect.php');
    require_once('./common/function.php');
    $login = 1;
    $record_id = $_REQUEST['id'];
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];

    $sql = "SELECT login_time,ip_address,terminal FROM login WHERE id=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($record_id));
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);

    require_once('./common/sql.php');

    $stmt = null;
    $dbh = null;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/userpage.css">
    <title>ログイン履歴 -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div id="login-record" class="form-wrapper">
                <h2>Webサイト</h2>
                <dl>
                    <dt>ログイン日時<div class="bb"></div>
                    </dt>
                    <dd><?= date("Y.m.d H:i", strtotime($rec['login_time'])) ?><div class="bb"></div>
                    </dd>
                    <dt>IPアドレス<div class="bb"></div>
                    </dt>
                    <dd><?= $rec['ip_address'] ?><div class="bb"></div>
                    </dd>
                    <dt>端末情報<div class="bb"></div>
                    </dt>
                    <dd><?= $rec['terminal'] ?><div class="bb"></div>
                    </dd>
                    <div class="bt"></div>
                </dl>
                <?php include_once('./views/user-window.php') ?>
            </div>
        <?php } else { ?>
            <?php include_once('./views/notlogin.html') ?>
        <?php } ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script>
        const login = '<?= $login ?>';
    </script>
    <script src="./js/index.js"></script>
</body>

</html>