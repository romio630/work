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

    $sql = 'SELECT id,cur_age,pos_age,category  FROM draft WHERE user_id=? ORDER BY created_at DESC';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row[] = $rec;
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
    <title><?= $nickname ?>の下書き一覧 -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div id="draft-top" class="form-wrapper">
                <h2>下書き一覧</h2>
                <?php if (isset($row)) { ?>
                    <ul class="draft-list">
                        <?php foreach ($row as $value) : ?>
                            <li>
                                <a href="./draft.php?id=<?= $value['id'] ?>">
                                    <div class="flex-item">
                                        <div class="icon"><img src="./icon/<?= $icon ?>" alt="<?= $nickname ?>のアイコン"></div>
                                        <p class="title">
                                            <?php if ($value['cur_age'] == 0) { ?>
                                                <span class="cur-age">未選択</span>
                                            <?php } elseif ($value['cur_age'] == 200) { ?>
                                                <span class="cur-age">非公表</span>
                                            <?php } else { ?>
                                                <span class="cur-age"><?= $value['cur_age'] ?>歳</span>
                                            <?php } ?>
                                            <span class="send"><img src="./img/send.svg"></span>
                                            <?php if ($value['pos_age'] == 0) { ?>
                                                <span class="pos-age">未選択</span>
                                            <?php } else { ?>
                                                <span class="pos-age"><?= $value['pos_age'] ?>歳の自分へ</span>
                                            <?php } ?>
                                        </p>
                                    </div>
                                </a>
                                <div class="br"></div>
                                <div class="bl"></div>
                                <div class="bb"></div>
                                <div class="bt"></div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php } else { ?>
                    <div class="caution">
                        <div class="logo"><img src="./img/logo-glee.svg" alt="パスポストのアイコン"></div>
                        <p>下書き中の手紙はありません</p>
                    </div>
                <?php } ?>
                <?php include_once('./views/user-window.php') ?>
            </div>
        <?php } else { ?>
            <?php include_once('./views/notlogin.html') ?>
        <?php } ?>
        <?php if (isset($_COOKIE['ajax'])) : ?>
            <?php if ($_COOKIE['ajax'] == 'draft-add') : ?>
                <div class="notice"><img src="./img/check-red.svg">下書きを保存しました</div>
            <?php endif ?>
            <?php if ($_COOKIE['ajax'] == 'draft-update') : ?>
                <div class="notice"><img src="./img/check-red.svg">上書き保存しました</div>
            <?php endif ?>
            <?php if ($_COOKIE['ajax'] == 'draft-delete') : ?>
                <div class="notice"><img src="./img/check-red.svg">下書きを削除しました</div>
            <?php endif ?>
        <?php endif ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/click-toggle.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script>
        const login = '<?= $login ?>';
    </script>
    <script src="./js/index.js"></script>
</body>

</html>