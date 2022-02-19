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

    $sql = "SELECT id,user_id,login_time FROM login WHERE user_id=? ORDER BY login_time DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $login_history[] = $rec;
    }

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
            <div id="login-history" class="form-wrapper">
                <h2>ログイン履歴</h2>
                <ul class="history-list">
                    <?php foreach ($login_history as $record) : ?>
                        <li>
                            <a href="./login_record.php?id=<?= $record['id'] ?>">
                                <div class="flex-item">
                                    <p>Webサイト</p>
                                    <time datetime="<?= date("Y.m.d H:i", strtotime($record['login_time'])) ?>"><?= date("Y.m.d H:i", strtotime($record['login_time'])) ?></time>
                                </div>
                            </a>
                            <div class="br"></div>
                            <div class="bl"></div>
                            <div class="bb"></div>
                            <div class="bt"></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
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