<?php
session_start();
session_regenerate_id(true);

if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/dbconnect.php');
    require_once('./common/function.php');
    $login = 1;
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];

    require_once('./common/sql.php');

    $stmt = null;
    $dbh = null;
} else {
    $login = 0;
    $id_json = json_encode(0); //バグ対策
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/userpage.css">
    <title>パスポストに手紙を投函する -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div class="form-wrapper" id="posting-top">
                <h2>投函</h2>
                <ul class="posting-list">
                    <li>
                        <a href="./posting.php">
                            <img src="./img/posting.svg">
                            <p>手紙を書く</p>
                        </a>
                        <div class="br"></div>
                        <div class="bl"></div>
                        <div class="bb"></div>
                        <div class="bt"></div>
                    </li>
                    <li>
                        <a href="./draft_top.php">
                            <img src="./img/draft.svg">
                            <p>下書き一覧</p>
                        </a>
                        <div class="br"></div>
                        <div class="bl"></div>
                        <div class="bb"></div>
                        <div class="bt"></div>
                    </li>
                </ul>
                <!-- <section>
                <p class="section-title">どんな手紙を書けばいいの？？</p>
            </section> -->
                <?php include_once('./views/user-window.php') ?>
            </div>
        <?php } else { ?>
            <?php include_once('./views/notlogin.html') ?>
        <?php } ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <div class="fixed-menu">
        <div class="bt"></div>
        <nav>
            <ul>
                <li>
                    <a href="./index.php">
                        <div class="btn-inner">
                            <div class="sp-menu-icon"><img src="./img/home.svg"></div>
                            <p>ホーム</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="./notifications.php">
                        <div class="btn-inner">
                            <div class="sp-menu-icon"><img src="./img/bell.svg"></div>
                            <p>お知らせ</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="./posting_top.php">
                        <div class="btn-inner">
                            <div class="sp-menu-icon"><img src="./img/post-f.svg"></div>
                            <p>投函</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="./mypage_top.php">
                        <div class="btn-inner">
                            <div class="sp-menu-icon"><img src="./img/mypage.svg"></div>
                            <p>マイページ</p>
                        </div>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/click-toggle.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script>
        const login = '<?= $login ?>';
    </script>
    <script src="./js/index.js"></script>
</body>

</html>