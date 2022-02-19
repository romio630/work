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
    $id_json = json_encode($id);

    require_once('./common/sql.php');

    $sql = "SELECT N.id,N.letter_id,N.from_id,N.created_at,N.type,N.already_read,U.icon,U.nickname,U.hide
    FROM notification as N JOIN pp_user as U ON N.from_id=U.id WHERE to_id=? ORDER BY created_at DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $notification_list[] = $rec;
    }

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
    <title>お知らせ -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div id="notifications" class="form-wrapper">
                <h2>お知らせ</h2>
                <?php if (isset($notification_list)) { ?>
                    <button class="all-read-btn">全て既読にする</button>
                    <ul class="notifications-list">
                        <?php foreach ($notification_list as $record) : ?>
                            <?php if ($record['from_id'] != $id) : ?>
                                <?php if ($record['type'] == 1) { ?>
                                    <li class="notifications-list-item">
                                        <a href="./letter.php?id=<?= $record['letter_id'] ?>&r=<?= $record['id'] ?>">
                                            <?php if ($record['already_read'] == 0) : ?>
                                                <span class="new-mark"></span>
                                            <?php endif ?>
                                            <div class="flex-item">
                                                <div class="icon"><img src="./icon/<?= $record['icon'] ?>" alt="<?= $record['nickname'] ?>のアイコン"></div>
                                                <div class="item-text">
                                                    <div class="text-content">
                                                        <?php if ($record['hide'] == 2) : ?>
                                                            <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                        <?php endif ?>
                                                        <?= $record['nickname'] ?>さんがあなたの手紙にいいね！しました。
                                                    </div>
                                                    <time datetime="<?= date("Y.m.d H:i", strtotime($record['created_at'])) ?>"><?= letter_time($record['created_at']) ?></time>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="br"></div>
                                        <div class="bl"></div>
                                        <div class="bb"></div>
                                        <div class="bt"></div>
                                    </li>
                                <?php } else if ($record['type'] == 2) { ?>
                                    <li class="notifications-list-item">
                                        <a href="./letter.php?id=<?= $record['letter_id'] ?>&r=<?= $record['id'] ?>" style="display:block">
                                            <?php if ($record['already_read'] == 0) : ?>
                                                <span class="new-mark"></span>
                                            <?php endif ?>
                                            <div class="flex-item">
                                                <div class="icon"><img src="./icon/<?= $record['icon'] ?>" alt="<?= $record['nickname'] ?>のアイコン"></div>
                                                <div class="item-text">
                                                    <div class="text-content">
                                                        <div class="nickname-outer">
                                                            <?php if ($record['hide'] == 2) : ?>
                                                                <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                            <?php endif ?>
                                                            <?= $record['nickname'] ?>さんがあなたの手紙にコメントしました。
                                                        </div>
                                                        <span></span>
                                                    </div>
                                                    <time datetime="<?= date("Y.m.d H:i", strtotime($record['created_at'])) ?>"><?= letter_time($record['created_at']) ?></time>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="br"></div>
                                        <div class="bl"></div>
                                        <div class="bb"></div>
                                        <div class="bt"></div>
                                    </li>
                                <?php } else if ($record['type'] == 3) { ?>
                                    <li class="notifications-list-item">
                                        <a href="./userpage_top.php?id=<?= $record['from_id'] ?>&r=<?= $record['id'] ?>" style="display:block">
                                            <?php if ($record['already_read'] == 0) : ?>
                                                <span class="new-mark"></span>
                                            <?php endif ?>
                                            <div class="flex-item">
                                                <div class="icon"><img src="./icon/<?= $record['icon'] ?>" alt="<?= $record['nickname'] ?>のアイコン"></div>
                                                <div class="item-text">
                                                    <div class="text-content">
                                                        <?php if ($record['hide'] == 2) : ?>
                                                            <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                        <?php endif ?>
                                                        <?= $record['nickname'] ?>さんがあなたをフォローしました。
                                                    </div>
                                                    <time datetime="<?= date("Y.m.d H:i", strtotime($record['created_at'])) ?>"><?= letter_time($record['created_at']) ?></time>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="br"></div>
                                        <div class="bl"></div>
                                        <div class="bb"></div>
                                        <div class="bt"></div>
                                    </li>
                                <?php } else { ?>
                                    <li class="notifications-list-item">
                                        <a href="./userpage_top.php?id=<?= $record['from_id'] ?>&r=<?= $record['id'] ?>" style="display:block">
                                            <?php if ($record['already_read'] == 0) : ?>
                                                <span class="new-mark"></span>
                                            <?php endif ?>
                                            <div class="flex-item" style="align-items: flex-start;">
                                                <div class="icon"><img src="./icon/<?= $record['icon'] ?>" alt="<?= $record['nickname'] ?>のアイコン"></div>
                                                <div class="item-text">
                                                    <div class="text-content">
                                                        <?php if ($record['hide'] == 2) : ?>
                                                            <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                        <?php endif ?>
                                                        <?= $record['nickname'] ?>さんからフォローリクエストがきています。
                                                    </div>
                                                    <time datetime="<?= date("Y.m.d H:i", strtotime($record['created_at'])) ?>" style="margin-bottom:20px;"><?= letter_time($record['created_at']) ?></time>
                                                    <ul>
                                                        <li class="not-approve-btn" data-id="<?= $record['from_id'] ?>" data-name="<?= $record['nickname'] ?>">承認しない</li>
                                                        <li class="approve-btn" data-id="<?= $record['from_id'] ?>" data-name="<?= $record['nickname'] ?>">承認する</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="br"></div>
                                        <div class="bl"></div>
                                        <div class="bb"></div>
                                        <div class="bt"></div>
                                    </li>
                                <?php } ?>
                            <?php endif ?>
                        <?php endforeach ?>
                    </ul>
                <?php } else { ?>
                    <div class="caution">
                        <div class="logo"><img src="./img/logo-glee.svg" alt="パスポストのアイコン"></div>
                        <p>現在、お知らせはありません。</p>
                    </div>
                <?php } ?>
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
                            <div class="sp-menu-icon"><img src="./img/bell-f.svg"></div>
                            <p>お知らせ</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="./posting_top.php">
                        <div class="btn-inner">
                            <div class="sp-menu-icon"><img src="./img/post.svg"></div>
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
    <script src="./js/common.js"></script>
    <script src="./js/click-toggle.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script>
        const login = '<?= $login ?>';
        var userid = JSON.parse('<?= $id_json ?>');
        $('.all-read-btn').click(function() {
            $.ajax({
                type: "POST",
                url: "ajax_all_already_read.php",
                datatype: "json",
                data: {
                    "id": userid,
                }
            }).fail((data) => {
                location.reload();
            });

            return false;
        });

        $('.approve-btn').on('click', function() {
            const followid = $(this).attr('data-id');
            const followName = $(this).attr('data-name');
            $.ajax({
                type: "POST",
                url: "ajax_follow_request_approve.php",
                datatype: "json",
                data: {
                    "followid": followid,
                    "userid": userid
                }
            }).done((data) => {
                $(this).parents('.notifications-list-item').remove();
                $('.notice').remove();
                if (data == 1) {
                    $('body').append(`<div class="notice nocheck">${followName}さんのフォローリクエストを承認しました。</div>`);
                } else {
                    $('body').append(`<div class="notice nocheck">${followName}さんのフォローリクエストを承認できませんでした。</div>`);
                }
            });
            return false;
        });

        $('.not-approve-btn').on('click', function() {
            const followid = $(this).attr('data-id');
            const followName = $(this).attr('data-name');
            $.ajax({
                type: "POST",
                url: "ajax_follow_request_notapprove.php",
                datatype: "json",
                data: {
                    "followid": followid,
                    "userid": userid
                }
            }).fail((data) => {
                $(this).parents('.notifications-list-item').remove();
                $('.notice').remove();
                $('body').append(`<div class="notice nocheck">${followName}さんのフォローリクエストを削除しました。</div>`);
            });
            return false;
        });
    </script>
    <script src="./js/index.js"></script>
</body>

</html>