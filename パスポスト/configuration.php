<?php
session_start();
session_regenerate_id(true);

if (isset($_COOKIE['paspost_id'])) {
    $login = 1;
    require_once('./common/dbconnect.php');
    require_once('./common/function.php');
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];

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
    <title>設定 -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <div id="configuration" class="wrapper">
            <?php if (isset($_COOKIE['paspost_id'])) { ?>
                <h2>設定</h2>
                <ul class="configuration-list">
                    <li>
                        <a href=".//profile.php">
                            <img src="./img/edit.svg" alt="プロフィールを編集">
                            <p>プロフィールを編集</p>
                        </a>
                        <div class="br"></div>
                        <div class="bl"></div>
                        <div class="bb"></div>
                        <div class="bt"></div>
                    </li>
                    <li>
                        <a href="./mailpass_change.php">
                            <img src="./img/password.svg" alt="メールアドレス・パスワードを変更">
                            <p>メール・パスワードを変更</p>
                        </a>
                        <div class="br"></div>
                        <div class="bl"></div>
                        <div class="bb"></div>
                        <div class="bt"></div>
                    </li>
                    <li>
                        <a href="./login_history.php">
                            <img src="./img/history.svg" alt="ログイン履歴">
                            <p>ログイン履歴</p>
                        </a>
                        <div class="br"></div>
                        <div class="bl"></div>
                        <div class="bb"></div>
                        <div class="bt"></div>
                    </li>
                    <li>
                        <a href="./withdrawal.php">
                            <img src="./img/withdrawal.svg" alt="退会">
                            <p>退会する</p>
                        </a>
                        <div class="br"></div>
                        <div class="bl"></div>
                        <div class="bb"></div>
                        <div class="bt"></div>
                    </li>
                </ul>
                <?php include_once('./views/user-window.php') ?>
            <?php } else { ?>
                <?php include_once('./views/notlogin.html') ?>
            <?php } ?>
        </div>
        <?php if (isset($_COOKIE['ajax'])) : ?>
            <?php if ($_COOKIE['ajax'] == 'profile-check') : ?>
                <div class="notice"><img src="./img/check-red.svg">プロフィールを更新しました</div>
            <?php endif ?>
            <?php if ($_COOKIE['ajax'] == 'password-change') : ?>
                <div class="notice"><img src="./img/check-red.svg">パスワードを更新しました</div>
            <?php endif ?>
        <?php endif ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/click-toggle.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script src="./js/common.js"></script>
    <script>
        const login = '<?= $login ?>';
    </script>
    <script src="./js/index.js"></script>
</body>

</html>