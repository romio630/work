<?php
session_start();
session_regenerate_id(true);

if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/dbconnect.php');
    require_once('./common/category.php');
    require_once('./common/function.php');
    $login = 1;
    $id = $_SESSION['id'];
    $id_json = json_encode($id);
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];

    require_once('./common/sql.php');

    $sql = 'SELECT count(*) as lt_count FROM letter WHERE user_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    $rec1 = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = 'SELECT count(*) as follower_count FROM follow WHERE following_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    $rec2 = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = 'SELECT count(*) as following_count FROM follow WHERE follower_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    $rec3 = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = 'SELECT intro,icon,hide FROM pp_user WHERE id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    $rec4 = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = 'SELECT u.nickname,u.icon,u.id,u.intro,u.hide  FROM follow as f JOIN pp_user as u ON f.follower_id=u.id WHERE following_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row[] = $rec;
    }
    if (!isset($row)) {
        $row = null;
    }

    if ($row != null) {
        $row_status = [];
        for ($i = 0; $i < count($row); $i++) {
            $sql = "SELECT status FROM hide_status WHERE from_id=? and to_id=?";
            $stmt = $dbh->prepare($sql);
            $data = [];
            $data[] = $id;
            $data[] = $row[$i]['id'];
            $stmt->execute($data);
            $rec6 = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!isset($rec6['status'])) {
                $rec6['status'] = null;
            }
            $row_status[$i] = $rec6['status'];
        }
    }

    $sql = 'SELECT u.nickname,u.icon,u.id  FROM follow as f JOIN pp_user as u ON f.following_id=u.id WHERE follower_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mutual[] = $rec['id'];
    }
    if (!isset($mutual)) {
        $mutual = [];
    }

    $stmt = null;
    $dbh = null;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/userpage.css">
    <title><?= $nickname ?>のフォロワー -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div class="flex-userpage wrapper">
                <?php include_once('./views/mypage-profile.php') ?>
                <div class="content-right">
                    <h3>フォロワー</h3>
                    <?php if (isset($row)) { ?>
                        <div class="follower-list">
                            <?php for ($i = 0; $i < count($row); $i++) : ?>
                                <a href="./userpage_top.php?id=<?= $row[$i]['id'] ?>">
                                    <?php if ($row[$i]['intro'] == "") { ?>
                                        <div class="flex-item" style="align-items: center;">
                                            <div class="icon"><img src="./icon/<?= $row[$i]['icon'] ?>" alt="<?= $row[$i]['nickname'] ?>のアイコン"></div>
                                            <div class="user-info">
                                                <div class="user-info-top">
                                                    <div class="user-name">
                                                        <div class="user-name-inner">
                                                            <?php if ($row[$i]['hide'] == 2) : ?>
                                                                <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                            <?php endif ?>
                                                            <span class="nickname"><?= $row[$i]['nickname'] ?></span>
                                                        </div>
                                                        <span class="my-follower">フォローされています</span>
                                                    </div>
                                                    <?php if ($row[$i]['hide'] == 2) { ?>
                                                        <?php if (isset($row_status[$i])) { ?>
                                                            <?php if ($row_status[$i] == 0) { ?>
                                                                <button class="list-unapproved-btn" data-id="<?= $row[$i]['id'] ?>" data-name="<?= $row[$i]['nickname'] ?>">
                                                                    <span class="btn-text">未承認</span>
                                                                    <span class="btn-text">キャンセル</span>
                                                                </button>
                                                            <?php } else { ?>
                                                                <button class="list-hide-unfollow-confirm" data-id="<?= $row[$i]['id'] ?>" data-name="<?= $row[$i]['nickname'] ?>">
                                                                    <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                                    <span class="btn-text">フォロー解除</span>
                                                                </button>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <button class="list-follow-request-btn" data-id="<?= $row[$i]['id'] ?>" data-name="<?= $row[$i]['nickname'] ?>">
                                                                <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                            </button>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <?php if (in_array($row[$i]['id'], $mutual)) { ?>
                                                            <button class="list-unfollow-confirm" data-id="<?= $row[$i]['id'] ?>" data-name="<?= $row[$i]['nickname'] ?>">
                                                                <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                                <span class="btn-text">フォロー解除</span>
                                                            </button>
                                                        <?php } else { ?>
                                                            <button class="list-follow-btn" data-id="<?= $row[$i]['id'] ?>" data-name="<?= $row[$i]['nickname'] ?>">
                                                                <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                            </button>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="flex-item">
                                            <div class="icon"><img src="./icon/<?= $row[$i]['icon'] ?>" alt="<?= $row[$i]['nickname'] ?>のアイコン"></div>
                                            <div class="user-info">
                                                <div class="user-info-top" style="margin-bottom: 8px;">
                                                    <div class="user-name">
                                                        <div class="user-name-inner">
                                                            <?php if ($row[$i]['hide'] == 2) : ?>
                                                                <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                            <?php endif ?>
                                                            <span class="nickname"><?= $row[$i]['nickname'] ?></span>
                                                        </div>
                                                        <span class="my-follower">フォローされています</span>
                                                    </div>
                                                    <?php if ($row[$i]['hide'] == 2) { ?>
                                                        <?php if (isset($row_status[$i])) { ?>
                                                            <?php if ($row_status[$i] == 0) { ?>
                                                                <button class="list-unapproved-btn" data-id="<?= $row[$i]['id'] ?>" data-name="<?= $row[$i]['nickname'] ?>">
                                                                    <span class="btn-text">未承認</span>
                                                                    <span class="btn-text">キャンセル</span>
                                                                </button>
                                                            <?php } else { ?>
                                                                <button class="list-hide-unfollow-confirm" data-id="<?= $row[$i]['id'] ?>" data-name="<?= $row[$i]['nickname'] ?>">
                                                                    <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                                    <span class="btn-text">フォロー解除</span>
                                                                </button>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <button class="list-follow-request-btn" data-id="<?= $row[$i]['id'] ?>" data-name="<?= $row[$i]['nickname'] ?>">
                                                                <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                            </button>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <?php if (in_array($row[$i]['id'], $mutual)) { ?>
                                                            <button class="list-unfollow-confirm" data-id="<?= $row[$i]['id'] ?>" data-name="<?= $row[$i]['nickname'] ?>">
                                                                <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                                <span class="btn-text">フォロー解除</span>
                                                            </button>
                                                        <?php } else { ?>
                                                            <button class="list-follow-btn" data-id="<?= $row[$i]['id'] ?>" data-name="<?= $row[$i]['nickname'] ?>">
                                                                <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                            </button>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                                <p class="list-intro"><?= nl2br($row[$i]['intro']) ?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="br"></div>
                                    <div class="bl"></div>
                                    <div class="bb"></div>
                                    <div class="bt"></div>
                                </a>
                            <?php endfor ?>
                        </div>
                    <?php } else { ?>
                        <p class="no-user">フォロワーはいません</p>
                    <?php } ?>
                    <div class="bl"></div>
                </div>
                <!-- ０ -->
                <?php include_once('./views/user-window.php') ?>
            </div>
            <!-- １ -->
            <div class="sp-modal-bg modal-bg close">
                <div class="sp-modal">
                    <ul class="sp-report-list">
                        <li><button class="logout">ログアウトする</button></li>
                        <li><button class="modal-cancel">キャンセル</button></li>
                    </ul>
                </div>
            </div>
            <!-- ２リスト（鍵垢） -->
            <div class="modal-bg close">
                <div class="modal-confirm modal">
                    <p class="header">フォローリクエストを破棄</p>
                    <p class="modal-text"></p>
                    <div>
                        <button class="modal-cancel">キャンセル</button>
                        <button class="list-request-cancel-btn">破棄する</button>
                    </div>
                </div>
            </div>
            <!-- ３リスト（鍵垢） -->
            <div class="modal-bg close">
                <div class="modal-confirm modal">
                    <p class="header"></p>
                    <p>このユーザーが非公開になっている場合は、手紙などの情報が表示されなくなります。 </p>
                    <div>
                        <button class="modal-cancel">キャンセル</button>
                        <button class="list-hide-unfollow-btn">フォロー解除</button>
                    </div>
                </div>
            </div>
            <!-- ４リスト（通常） -->
            <div class="modal-bg close">
                <div class="modal-confirm modal">
                    <p class="header"></p>
                    <p>このユーザーが非公開になっている場合は、手紙などの情報が表示されなくなります。 </p>
                    <div>
                        <button class="modal-cancel">キャンセル</button>
                        <button class="list-unfollow-btn">フォロー解除</button>
                    </div>
                </div>
            </div>
            <div class="modal-bg close icon-modal">
                <div class="modal-cancel"></div>
                <div class="icon"><img src="./icon/<?= $icon ?>" alt="<?= $nickname ?>のアイコン"></div>
            </div>
        <?php } else { ?>
            <?php include_once('./views/notlogin.html') ?>
        <?php } ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/list-follow-btn.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script src="./js/intro.js"></script>
    <script>
        var userid = JSON.parse('<?= $id_json ?>');
        const login = '<?= $login ?>';
        if (window.innerWidth > 768) {
            $('.user-menu-btn').css('display', 'none');
        }
        $('.top-icon').on('click', function() {
            modalBg[5].classList.remove('close');
            $('body').css('overflow', 'hidden');
            document.addEventListener('touchmove', disableScroll, {
                passive: false
            });
        })
    </script>
    <script src="./js/index.js"></script>
</body>

</html>