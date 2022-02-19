<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
require_once('./common/function.php');
session_start();
session_regenerate_id(true);

$get = sanitize($_GET);
$searchname = $get['name'];
$replace_searchname = preg_replace('/\s+/u', '', $searchname);

if ($searchname == '') {
    $no_word = true;
    $count['user'] = 0;
} else {
    $no_word = false;
    $sql = 'SELECT id,nickname,icon,intro,hide FROM pp_user WHERE nickname LIKE ?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array('%' . $replace_searchname . '%'));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hit_list[] = $rec;
    }
    if (!isset($hit_list)) {
        $hit_list = null;
    }

    $sql = 'SELECT count(*) as user FROM pp_user WHERE nickname LIKE ?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array('%' . $replace_searchname . '%'));
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_COOKIE['paspost_id'])) {
    $login = 1;
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];
    $id_json = json_encode($id);

    $sql = 'SELECT following_id from follow where follower_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mutual[] = $rec['following_id'];
    }
    if (!isset($mutual)) {
        $mutual = [];
    }

    if ($hit_list != null) {
        $row_status = [];
        for ($i = 0; $i < count($hit_list); $i++) {
            $sql = "SELECT status FROM hide_status WHERE from_id=? and to_id=?";
            $stmt = $dbh->prepare($sql);
            $data = [];
            $data[] = $id;
            $data[] = $hit_list[$i]['id'];
            $stmt->execute($data);
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!isset($rec['status'])) {
                $rec['status'] = null;
            }
            $row_status[$i] = $rec['status'];
        }

        $is_follower = [];
        for ($i = 0; $i < count($hit_list); $i++) {
            $sql = "SELECT id FROM follow WHERE follower_id=? and following_id=?";
            $stmt = $dbh->prepare($sql);
            $data = [];
            $data[] = $hit_list[$i]['id'];
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

    require_once('./common/sql.php');
} else {
    $login = 0;
    $user_id_json = json_encode(0); //バグ対策
    $id_json = json_encode(0); //バグ対策
}

$stmt = null;
$dbh = null;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/userpage.css">
    <title>ユーザー検索 -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <div id="search-results" class="form-wrapper">
            <h3>検索結果</h3>
            <p class="result-stats"><?= $count['user'] ?>件</p>
            <?php if ($no_word) { ?>
                <div class="caution">
                    <div class="logo"><img src="./img/logo-glee.svg" alt="パスポストのアイコン"></div>
                    <p>ユーザーを検索しましょう</p>
                </div>
            <?php } else { ?>
                <?php if (isset($_COOKIE['paspost_id'])) { ?>
                    <?php if (count($hit_list) > 0) { ?>
                        <ul class="search-user-list">
                            <?php for ($i = 0; $i < count($hit_list); $i++) : ?>
                                <?php if ($hit_list[$i]['id'] == $id) { ?>
                                    <li>
                                        <a href="./mypage_top.php">
                                            <div class="flex-item">
                                                <div class="icon"><img src="./icon/<?= $hit_list[$i]['icon'] ?>" alt="<?= $hit_list[$i]['nickname'] ?>のアイコン"></div>
                                                <?php if ($hit_list[$i]['intro'] == "") { ?>
                                                    <div class="user-info">
                                                        <div class="user-info-top">
                                                            <div class="user-name">
                                                                <div class="user-name-inner">
                                                                    <?php if ($hit_list[$i]['hide'] == 2) : ?>
                                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                    <?php endif ?>
                                                                    <span class="nickname"><?= $hit_list[$i]['nickname'] ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="user-info">
                                                        <div class="user-info-top" style="margin-bottom: 8px;">
                                                            <div class="user-name">
                                                                <div class="user-name-inner">
                                                                    <?php if ($hit_list[$i]['hide'] == 2) : ?>
                                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                    <?php endif ?>
                                                                    <span class="nickname"><?= $hit_list[$i]['nickname'] ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="list-intro"><?= nl2br($hit_list[$i]['intro']) ?></p>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </a>
                                        <div class="br"></div>
                                        <div class="bl"></div>
                                        <div class="bb"></div>
                                        <div class="bt"></div>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <a href="./userpage_top.php?id=<?= $hit_list[$i]['id'] ?>">
                                            <div class="flex-item">
                                                <div class="icon"><img src="./icon/<?= $hit_list[$i]['icon'] ?>" alt="<?= $hit_list[$i]['nickname'] ?>のアイコン"></div>
                                                <?php if ($hit_list[$i]['intro'] == "") { ?>
                                                    <div class="user-info">
                                                        <div class="user-info-top">
                                                            <div class="user-name">
                                                                <div class="user-name-inner">
                                                                    <?php if ($hit_list[$i]['hide'] == 2) : ?>
                                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                    <?php endif ?>
                                                                    <span class="nickname"><?= $hit_list[$i]['nickname'] ?></span>
                                                                </div>
                                                                <?php if ($is_follower[$i] == 1) : ?>
                                                                    <span class="my-follower">フォローされています</span>
                                                                <?php endif ?>
                                                            </div>
                                                            <?php if ($hit_list[$i]['hide'] == 2) { ?>
                                                                <?php if (isset($row_status[$i])) { ?>
                                                                    <?php if ($row_status[$i] == 0) { ?>
                                                                        <button class="list-unapproved-btn" data-id="<?= $hit_list[$i]['id'] ?>" data-name="<?= $hit_list[$i]['nickname'] ?>">
                                                                            <span class="btn-text">未承認</span>
                                                                            <span class="btn-text">キャンセル</span>
                                                                        </button>
                                                                    <?php } else { ?>
                                                                        <button class="list-hide-unfollow-confirm" data-id="<?= $hit_list[$i]['id'] ?>" data-name="<?= $hit_list[$i]['nickname'] ?>">
                                                                            <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                                            <span class="btn-text">フォロー解除</span>
                                                                        </button>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <button class="list-follow-request-btn" data-id="<?= $hit_list[$i]['id'] ?>" data-name="<?= $hit_list[$i]['nickname'] ?>">
                                                                        <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                                    </button>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <?php if (in_array($hit_list[$i]['id'], $mutual)) { ?>
                                                                    <button class="list-unfollow-confirm" data-id="<?= $hit_list[$i]['id'] ?>" data-name="<?= $hit_list[$i]['nickname'] ?>">
                                                                        <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                                        <span class="btn-text">フォロー解除</span>
                                                                    </button>
                                                                <?php } else { ?>
                                                                    <button class="list-follow-btn" data-id="<?= $hit_list[$i]['id'] ?>" data-name="<?= $hit_list[$i]['nickname'] ?>">
                                                                        <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                                    </button>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="user-info">
                                                        <div class="user-info-top" style="margin-bottom: 8px;">
                                                            <div class="user-name">
                                                                <div class="user-name-inner">
                                                                    <?php if ($hit_list[$i]['hide'] == 2) : ?>
                                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                    <?php endif ?>
                                                                    <span class="nickname"><?= $hit_list[$i]['nickname'] ?></span>
                                                                </div>
                                                                <?php if ($is_follower[$i] == 1) : ?>
                                                                    <span class="my-follower">フォローされています</span>
                                                                <?php endif ?>
                                                            </div>
                                                            <?php if ($hit_list[$i]['hide'] == 2) { ?>
                                                                <?php if (isset($row_status[$i])) { ?>
                                                                    <?php if ($row_status[$i] == 0) { ?>
                                                                        <button class="list-unapproved-btn" data-id="<?= $hit_list[$i]['id'] ?>" data-name="<?= $hit_list[$i]['nickname'] ?>">
                                                                            <span class="btn-text">未承認</span>
                                                                            <span class="btn-text">キャンセル</span>
                                                                        </button>
                                                                    <?php } else { ?>
                                                                        <button class="list-hide-unfollow-confirm" data-id="<?= $hit_list[$i]['id'] ?>" data-name="<?= $hit_list[$i]['nickname'] ?>">
                                                                            <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                                            <span class="btn-text">フォロー解除</span>
                                                                        </button>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <button class="list-follow-request-btn" data-id="<?= $hit_list[$i]['id'] ?>" data-name="<?= $hit_list[$i]['nickname'] ?>">
                                                                        <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                                    </button>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <?php if (in_array($hit_list[$i]['id'], $mutual)) { ?>
                                                                    <button class="list-unfollow-confirm" data-id="<?= $hit_list[$i]['id'] ?>" data-name="<?= $hit_list[$i]['nickname'] ?>">
                                                                        <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                                        <span class="btn-text">フォロー解除</span>
                                                                    </button>
                                                                <?php } else { ?>
                                                                    <button class="list-follow-btn" data-id="<?= $hit_list[$i]['id'] ?>" data-name="<?= $hit_list[$i]['nickname'] ?>">
                                                                        <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                                    </button>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                        <p class="list-intro"><?= nl2br($hit_list[$i]['intro']) ?></p>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </a>
                                        <div class="br"></div>
                                        <div class="bl"></div>
                                        <div class="bb"></div>
                                        <div class="bt"></div>
                                    </li>
                                <?php } ?>
                            <?php endfor ?>
                        </ul>
                    <?php } else { ?>
                        <div class="caution">
                            <div class="logo"><img src="./img/logo-glee.svg" alt="パスポストのアイコン"></div>
                            <p>「<?= $replace_searchname ?>」に該当するユーザーは見つかりませんでした</p>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <?php if (count($hit_list) > 0) { ?>
                        <ul class="search-user-list">
                            <?php foreach ($hit_list as $value) : ?>
                                <li>
                                    <a href="./userpage_top.php?id=<?= $value['id'] ?>">
                                        <div class="flex-item">
                                            <div class="icon"><img src="./icon/<?= $value['icon'] ?>" alt="<?= $value['nickname'] ?>のアイコン"></div>
                                            <?php if ($value['intro'] == "") { ?>
                                                <div class="user-info">
                                                    <div class="user-info-top">
                                                        <div class="user-name">
                                                            <div class="user-name-inner">
                                                                <?php if ($value['hide'] == 2) : ?>
                                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                <?php endif ?>
                                                                <span><?= $value['nickname'] ?></span>
                                                            </div>
                                                        </div>
                                                        <button class="list-follow-btn">
                                                            <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="user-info">
                                                    <div class="user-info-top" style="margin-bottom: 8px;">
                                                        <div class="user-name">
                                                            <div class="user-name-inner">
                                                                <?php if ($value['hide'] == 2) : ?>
                                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                <?php endif ?>
                                                                <span><?= $value['nickname'] ?></span>
                                                            </div>
                                                        </div>
                                                        <button class="list-follow-btn">
                                                            <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                        </button>
                                                    </div>
                                                    <p class="list-intro"><?= nl2br($value['intro']) ?></p>
                                                </div>
                                            <?php } ?>
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
                            <p>「<?= $replace_searchname ?>」<br>に該当するユーザーは見つかりませんでした</p>
                        </div>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <!-- ０ -->
            <?php include_once('./views/user-window.php') ?>
            <!-- １リスト（鍵垢） -->
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
            <!-- ２リスト（鍵垢） -->
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
            <!-- ３リスト（通常） -->
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
        <?php } else { ?>
            <?php include_once('./views/login-modal.html') ?>
        <?php } ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.html') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/function.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script>
        var userid = JSON.parse('<?= $id_json ?>');
        const login = '<?= $login ?>';
        if (login == 1) {
            $(document).on('click', '.list-follow-request-btn', function() {
                const followId = $(this).attr('data-id');
                const nickName = $(this).attr('data-name');
                $.ajax({
                    type: "POST",
                    url: "ajax_list_follow_request.php",
                    data: {
                        "followid": followId,
                        "userid": userid,
                        "nickname": nickName,
                    }
                }).done((data) => {
                    $(this).parent().append(data);
                    $(this).remove();
                    $('.notice').remove();
                    $('body').append(`<div class="notice nocheck">${nickName}さんへフォローリクエストが送信され、承認待ちになりました。</div>`);
                });
                $('.user-modal-bg').removeClass('close');
                return false;
            });

            $(document).on('click', '.list-unapproved-btn', function() {
                const followId = $(this).attr('data-id');
                const nickName = $(this).attr('data-name');
                $('.header').text('フォローリクエストを破棄');
                $('.modal-text').text(`未承認のフォローリクエストがキャンセルされ、${nickName}さんには表示されなくなります。`);
                $('.list-request-cancel-btn').attr({
                    'data-id': followId,
                    'data-name': nickName,
                });
                modalBg[1].classList.remove('close');
                $('.user-modal-bg').removeClass('close');
                return false;
            });

            $(document).on('click', '.list-follow-btn', function() {
                const followId = $(this).attr('data-id');
                const nickName = $(this).attr('data-name');
                $.ajax({
                    type: "POST",
                    url: "ajax_list_follow.php",
                    data: {
                        "followid": followId,
                        "userid": userid,
                        "nickname": nickName,
                    }
                }).done((data) => {
                    $(this).parent().append(data);
                    $(this).remove();
                });
                $('.user-modal-bg').removeClass('close');
                return false;
            });

            $('.list-request-cancel-btn').on('click', function() {
                const followId = $(this).attr('data-id');
                const nickName = $(this).attr('data-name');
                $.ajax({
                    type: "POST",
                    url: "ajax_list_follow_request_cancel.php",
                    data: {
                        "followid": followId,
                        "userid": userid,
                        "nickname": nickName,
                    }
                }).done((data) => {
                    $(`.list-unapproved-btn[data-id="${followId}"]`).parent().append(data);
                    $(`.list-unapproved-btn[data-id="${followId}"]`).remove();
                    modalBg[1].classList.add('close');
                });
                return false;
            });

            $(document).on('click', '.list-hide-unfollow-confirm', function() {
                const followId = $(this).attr('data-id');
                const nickName = $(this).attr('data-name');
                $('.header').text(`${nickName}さんをフォロー解除`);
                $('.list-hide-unfollow-btn').attr({
                    'data-id': followId,
                    'data-name': nickName,
                });
                modalBg[2].classList.remove('close');
                $('.user-modal-bg').removeClass('close');
                return false;
            });

            $(document).on('click', '.list-unfollow-confirm', function() {
                const followId = $(this).attr('data-id');
                const nickName = $(this).attr('data-name');
                $('.header').text(`${nickName}さんをフォロー解除`);
                $('.list-unfollow-btn').attr({
                    'data-id': followId,
                    'data-name': nickName,
                });
                modalBg[3].classList.remove('close');
                $('.user-modal-bg').removeClass('close');
                return false;
            });

            $('.list-hide-unfollow-btn').on('click', function() {
                const followId = $(this).attr('data-id');
                const nickName = $(this).attr('data-name');
                $.ajax({
                    type: "POST",
                    url: "ajax_list_hide_unfollow.php",
                    data: {
                        "unfollowid": followId,
                        "userid": userid,
                        "nickname": nickName,
                    }
                }).done((data) => {
                    $(`.list-hide-unfollow-confirm[data-id="${followId}"]`).parent().append(data);
                    $(`.list-hide-unfollow-confirm[data-id="${followId}"]`).remove();
                    modalBg[2].classList.add('close');
                });
                return false;
            });

            $('.list-unfollow-btn').on('click', function() {
                const followId = $(this).attr('data-id');
                const nickName = $(this).attr('data-name');
                $.ajax({
                    type: "POST",
                    url: "ajax_list_unfollow.php",
                    data: {
                        "unfollowid": followId,
                        "userid": userid,
                        "nickname": nickName
                    }
                }).done((data) => {
                    $(`.list-unfollow-confirm[data-id="${followId}"]`).parent().append(data);
                    $(`.list-unfollow-confirm[data-id="${followId}"]`).remove();
                    modalBg[3].classList.add('close');
                });
                return false;
            });
        } else {
            $('.list-follow-btn').click((event) => {
                event.preventDefault();
                modalBg[0].classList.remove('close');
            })
        }
    </script>
    <script src="./js/index.js"></script>
</body>

</html>