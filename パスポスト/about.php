<?php
if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/dbconnect.php');
    require_once('./common/function.php');
    session_start();
    session_regenerate_id(true);
    $login = 1;
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];

    require_once('./common/sql.php');

    $stmt = null;
    $dbh = null;
} else {
    $login = 0;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <title>お問い合わせ</title>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/top.css">
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <div id="about" class="form-wrapper">
            <h2>パスポストとは</h2>
            <p class="text">「パスポスト」は「さんまのご長寿グランプリ」というテレビ番組の「過去の自分へビデオレター」というコーナーを大好きな私が、沢山の人の人生を覗いてみたいと思ったことがきっかけで、はじまりました。</p>
            <p class="text"> このサイトでは、年齢問わず沢山の方に過去のご自身に伝えたい思いを込めたお手紙を書いていただきたいと思っております。</p>
            <p class="text">そこで一つ、私からのお願いです。<br>
                ぜひ、あなたのご両親やおじいさま・おばあさまに人生の岐路に立った時の話や今だから言えるあの日の話など、尋ねてみてください。<br>
                そして、あなたやあなたの大切な方々が、パスポストにお手紙を投函していただければ嬉しく思います。
            </p>
            <p class="text">パスポストに投函される一つ一つの人生が、少しでも沢山の人の心に届きますように。</p>
            <p class="from"><time datetime="2022-02">2022年2月</time>パスポスト</p>
        </div>
        <?php if (isset($_COOKIE['paspost_id'])) : ?>
            <?php include_once('./views/user-window.php') ?>
        <?php endif ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script>
        const login = '<?= $login ?>';
    </script>
    <script src="./js/index.js"></script>
</body>

</html>