<?php
session_start();
session_regenerate_id(true);
if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/sanitize.php');
    require_once('./common/dbconnect.php');
    require_once('./common/function.php');
    $login = 1;
    $request = sanitize($_REQUEST);
    $user_id = $request['id'];
    $user_id_json = json_encode($user_id);
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];
    $id_json = json_encode($id);

    require_once('./common/sql.php');

    $sql = 'SELECT count(*) as lt_count FROM letter WHERE user_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($user_id));
    $rec1 = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = 'SELECT count(*) as follower_count FROM follow WHERE following_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($user_id));
    $rec2 = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = 'SELECT count(*) as following_count FROM follow WHERE follower_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($user_id));
    $rec3 = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = 'SELECT nickname,intro,icon,hide FROM pp_user WHERE id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($user_id));
    $rec4 = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_nickname = $rec4['nickname'];

    $sql = 'SELECT id FROM follow WHERE follower_id=? and following_id=?';
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $user_id;
    $data[] = $id;
    $stmt->execute($data);
    $rec5 = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!isset($rec5['id'])) {
        $rec5['id'] = 0;
    }

    if ($rec4['hide'] == 2) {
        $sql = "SELECT status FROM hide_status WHERE from_id=? and to_id=?";
        $stmt = $dbh->prepare($sql);
        $data = [];
        $data[] = $id;
        $data[] = $user_id;
        $stmt->execute($data);
        $hide = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!isset($hide['status'])) {
            $hide['status'] = null;
        }
    } else {
        $hide['status'] = 1;
    }

    $sql = 'SELECT u.nickname,u.icon,u.id,u.intro,u.hide  FROM follow as f JOIN pp_user as u ON f.following_id=u.id WHERE follower_id=? ORDER BY created_at DESC';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($user_id));
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

        $is_follower = [];
        for ($i = 0; $i < count($row); $i++) {
            $sql = "SELECT id FROM follow WHERE follower_id=? and following_id=?";
            $stmt = $dbh->prepare($sql);
            $data = [];
            $data[] = $row[$i]['id'];
            $data[] = $id;
            $stmt->execute($data);
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($rec['id'])) {
                $x = 1;
            } else {
                $x = 0;
            }
            $is_follower[$i] = $x;
        }
    }

    $sql = 'SELECT u.id  FROM follow as f JOIN pp_user as u ON f.following_id=u.id WHERE follower_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mutual[] = $rec['id'];
    }
    if (!isset($mutual)) {
        $mutual = [];
    }

    $sql = "SELECT sum(good) as good FROM letter WHERE user_id = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($user_id));
    $user_total = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user_total['good'] == null) {
        $user_total['good'] = 0;
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
    <title><?= $rec4['nickname'] ?>のフォロー中のアカウント -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div class="flex-userpage wrapper">
                <?php include_once('./views/userpage-profile.php') ?>
                <?php if ($hide['status'] == 1) { ?>
                    <div class="content-right">
                        <h3>フォロー中</h3>
                        <?php if (isset($row)) { ?>
                            <div class="following-list">
                                <?php for ($i = 0; $i < count($row); $i++) : ?>
                                    <?php if ($row[$i]['id'] == $id) { ?>
                                        <a href="./mypage_top.php">
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
                                                            </div>
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
                                                            </div>
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
                                    <?php } else { ?>
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
                                                                <?php if ($is_follower[$i] == 1) : ?>
                                                                    <span class="my-follower">フォローされています</span>
                                                                <?php endif ?>
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
                                                                <?php if ($is_follower[$i] == 1) : ?>
                                                                    <span class="my-follower">フォローされています</span>
                                                                <?php endif ?>
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
                                    <?php } ?>
                                <?php endfor ?>
                            </div>
                        <?php } else { ?>
                            <p class="no-user">フォローしているユーザーはいません</p>
                        <?php } ?>
                        <div class="bl"></div>
                    </div>
                <?php } else { ?>
                    <div id="letter-area">
                        <div class="outer-hide-status">
                            <div class="hide-status">
                                <div class="icon"><img src="./img/hide-status.svg"></div>
                                <h3>手紙は非公開です</h3>
                                <p><?= $rec4['nickname'] ?>さんから承認された場合のみ手紙を表示できます。承認をリクエストするには [＋フォロー] をクリックします</p>
                            </div>
                        </div>
                        <div class="bl"></div>
                    </div>
                <?php } ?>
                <!-- ０ -->
                <?php include_once('./views/user-window.php') ?>
            </div>
            <!-- １ -->
            <div class="sp-modal-bg modal-bg close">
                <div class="sp-modal">
                    <ul class="sp-report-list">
                        <li><button class="report-user-modal-btn">このユーザーを通報する</button></li>
                        <li><button class="modal-cancel">キャンセル</button></li>
                    </ul>
                </div>
            </div>
            <!-- ２ -->
            <div class="modal-bg close">
                <div class="report-modal modal">
                    <div class="modal-cancel"></div>
                    <div class="sp-inner">
                        <p class="header">ユーザーの報告</p>
                        <div id="report-form">
                            <dl>
                                <dt>報告理由</dt>
                                <dd>
                                    <select id="user-reason">
                                        <option value="1">プロフィールの情報や画像が不適切である</option>
                                        <option value="2">投稿した内容に暴言や差別・脅迫が含まれている</option>
                                        <option value="3">自傷行為・自殺をほのめかしている</option>
                                        <option value="4">アカウントが乗っ取られている</option>
                                        <option value="5">他のユーザーになりすましている</option>
                                    </select>
                                </dd>
                            </dl>
                            <p>ご報告いただいた内容は確認の上、適切に対応いたします。該当するユーザーの削除をお約束するものではありませんので、予めご了承ください。</p>
                            <button class="user-report-btn">報告する</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ３プロフィール（鍵垢） -->
            <div class="modal-bg close">
                <div class="modal-confirm modal">
                    <p class="header">フォローリクエストを破棄</p>
                    <p class="modal-text">未承認のフォローリクエストがキャンセルされ、<?= $rec4['nickname'] ?>さんには表示されなくなります。</p>
                    <div>
                        <button class="modal-cancel">キャンセル</button>
                        <button class="request-cancel-btn">破棄する</button>
                    </div>
                </div>
            </div>
            <!-- ４リスト（鍵垢） -->
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
            <!-- ５プロフィール（鍵垢） -->
            <div class="modal-bg close">
                <div class="modal-confirm modal">
                    <p class="header"><?= $rec4['nickname'] ?>さんをフォロー解除</p>
                    <p>このユーザーが非公開になっている場合は、手紙などの情報が表示されなくなります。 </p>
                    <div>
                        <button class="modal-cancel">キャンセル</button>
                        <button class="hide-unfollow-btn">フォロー解除</button>
                    </div>
                </div>
            </div>
            <!-- ６リスト（鍵垢） -->
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
            <!-- ７プロフィール（通常） -->
            <div class="modal-bg close">
                <div class="modal-confirm modal">
                    <p class="header"><?= $rec4['nickname'] ?>さんをフォロー解除</p>
                    <p>このユーザーが非公開になっている場合は、手紙などの情報が表示されなくなります。 </p>
                    <div>
                        <button class="modal-cancel">キャンセル</button>
                        <button class="unfollow-btn">フォロー解除</button>
                    </div>
                </div>
            </div>
            <!-- ８リスト（通常） -->
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
            <!-- ９ -->
            <div class="modal-bg close icon-modal">
                <div class="modal-cancel"></div>
                <div class="icon"><img src="./icon/<?= $rec4['icon'] ?>" alt="<?= $nickname ?>のアイコン"></div>
            </div>
        <?php } else { ?>
            <?php include_once('./views/notlogin.html') ?>
        <?php } ?>
        <?php if (isset($_COOKIE['ajax'])) : ?>
            <?php if ($_COOKIE['ajax'] == 'report-user') : ?>
                <div class="notice"><img src="./img/check-red.svg">ユーザーを報告しました</div>
            <?php endif ?>
        <?php endif ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script src="./js/intro.js"></script>
    <script>
        var userid = JSON.parse('<?= $id_json ?>');
        var followid = JSON.parse('<?= $user_id_json ?>');
        const login = '<?= $login ?>';
        const userNickName = '<?= $user_nickname ?>';
        $('.top-icon').on('click', function() {
            modalBg[9].classList.remove('close');
            document.addEventListener('touchmove', disableScroll, {
                passive: false
            });
            $('body').css('overflow', 'hidden');
        })
    </script>
    <script src="./js/userpage-follow-btn.js"></script>
    <script src="./js/index.js"></script>
</body>

</html>