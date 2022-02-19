<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
require_once('./common/function.php');
require_once('./common/category.php');

session_start();
session_regenerate_id(true);

$request = sanitize($_REQUEST);
$user_id = $request['id'];

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

$sql = "SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,L.edit,U.icon,U.hide
FROM letter as L join pp_user as U on L.user_id=U.id WHERE L.user_id=? ORDER BY created_at DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($user_id));
while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row[] = $rec;
}

$sql = "SELECT sum(good) as good FROM letter WHERE user_id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($user_id));
$user_total = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user_total['good'] == null) {
    $user_total['good'] = 0;
}

if (isset($_COOKIE['paspost_id'])) {
    $login = 1;
    $user_id_json = json_encode($user_id);
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];
    $id_json = json_encode($id);

    require_once('./common/sql.php');

    $sql = 'SELECT following_id from follow where follower_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mutual[] = $rec['following_id'];
    }
    if (!isset($mutual)) {
        $mutual = [];
    }

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

    $sql = 'SELECT letter_id FROM good_list WHERE giver_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $good_list[] = $rec['letter_id'];
    }
    if (!isset($good_list)) {
        $good_list = [];
    }

    $sql = "SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,L.edit,U.icon,U.hide,G.created_at as good_at
    FROM letter as L join pp_user as U on L.user_id=U.id join good_list as G on L.id=G.letter_id
    WHERE G.giver_id=? and U.hide=1
    union all SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,L.edit,U.icon,U.hide,G.created_at as good_at
    FROM letter as L join pp_user as U on L.user_id=U.id join good_list as G on L.id=G.letter_id
    WHERE G.giver_id=? and U.hide=2 and (L.user_id in(select F.following_id from follow as F where F.follower_id=?) or L.user_id=?)
    ORDER BY good_at DESC";
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $user_id;
    $data[] = $user_id;
    $data[] = $id;
    $data[] = $id;
    $stmt->execute($data);
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $g_row[] = $rec;
    }

    if (isset($_REQUEST['r'])) {
        $read_id = $_REQUEST['r'];
        $sql = 'UPDATE notification SET already_read=? WHERE id=?';
        $stmt = $dbh->prepare($sql);
        $data = [];
        $data[] = 1;
        $data[] = $read_id;
        $stmt->execute($data);
    }
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
    <title><?= $rec4['nickname'] ?>の投稿した手紙 -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div class="flex-userpage wrapper">
                <?php include_once('./views/userpage-profile.php') ?>
                <?php if ($hide['status'] == 1) { ?>
                    <div id="letter-area">
                        <ul class="tab-list">
                            <li class="tab active" data-left="0">投函した手紙</li>
                            <li class="tab" data-left="50%">いいね！した手紙</li>
                            <div class="active-bar"></div>
                            <div class="bb"></div>
                        </ul>
                        <div id="letter-list" class="panel show">
                            <?php if (!isset($row)) { ?>
                                <p class="no-letter">まだ投函されたお手紙はありません</p>
                            <?php } else { ?>
                                <?php foreach ($row as $value) : ?>
                                    <a href="./letter.php?id=<?= $value['id'] ?>" class="letter hover">
                                        <?php if ($value['user_id'] == $id) { ?>
                                            <div class="report-btn-bg sp-edit-btn" data-id="<?= $value['id'] ?>">
                                                <div class="report-icon">
                                                    <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                                                        <g>
                                                            <circle class="st0" cx="256" cy="55.091" r="55.091" style="fill: #7a7a7a"></circle>
                                                            <circle class="st0" cx="256" cy="256" r="55.091" style="fill: #7a7a7a"></circle>
                                                            <circle class="st0" cx="256" cy="456.909" r="55.091" style="fill: #7a7a7a"></circle>
                                                        </g>
                                                    </svg>
                                                </div>
                                            </div>
                                            <button data-id="<?= $value['id'] ?>" class="edit-btn">手紙を編集する</button>
                                            <article>
                                                <object class="icon"><a href="./mypage_top.php"><img src="./icon/<?= $value['icon'] ?>" alt="<?= $value['nickname'] ?>のアイコン"></a></object>
                                                <div class="contents">
                                                    <div class="info">
                                                        <div class="sp-block">
                                                            <object class="writer">
                                                                <a href="./mypage_top.php">
                                                                    <?php if ($value['hide'] == 2) : ?>
                                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                    <?php endif ?>
                                                                    <p><?= $value['nickname'] ?></p>
                                                                </a>
                                                            </object>
                                                        <?php } else { ?>
                                                            <div class="report-btn-bg sp-report-btn" data-id="<?= $value['id'] ?>">
                                                                <div class="report-icon">
                                                                    <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                                                                        <g>
                                                                            <circle class="st0" cx="256" cy="55.091" r="55.091" style="fill: #7a7a7a"></circle>
                                                                            <circle class="st0" cx="256" cy="256" r="55.091" style="fill: #7a7a7a"></circle>
                                                                            <circle class="st0" cx="256" cy="456.909" r="55.091" style="fill: #7a7a7a"></circle>
                                                                        </g>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <button class="report-modal-btn" data-id="<?= $value['id'] ?>">手紙を通報する</button>
                                                            <article>
                                                                <object class="icon"><a href="./userpage_top.php?id=<?= $value['user_id'] ?>"><img src="./icon/<?= $value['icon'] ?>" alt="<?= $value['nickname'] ?>のアイコン"></a></object>
                                                                <div class="contents">
                                                                    <div class="info">
                                                                        <div class="sp-block">
                                                                            <object class="writer">
                                                                                <a href="./userpage_top.php?id=<?= $value['user_id'] ?>">
                                                                                    <?php if ($value['hide'] == 2) : ?>
                                                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                                    <?php endif ?>
                                                                                    <p><?= $value['nickname'] ?></p>
                                                                                </a>
                                                                            </object>
                                                                        <?php } ?>
                                                                        <time class="time" datetime="<?= date("Y.m.d H:i", strtotime($value['created_at'])) ?>">・<?= letter_time($value['created_at']) ?></time>
                                                                        </div>
                                                                        <?php if (($value['ng_word'] == 1)) { ?>
                                                                    </div>
                                                                    <p class="message ng">不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                                <?php } else { ?>
                                                                    <p class="title">
                                                                        <?php if ($value['cur_age'] == 200) { ?>
                                                                            <span class="cur-age">非公表</span>
                                                                        <?php } else { ?>
                                                                            <span class="cur-age"><?= $value['cur_age'] ?>歳</span>
                                                                        <?php } ?>
                                                                        <span class="send"></span>
                                                                        <span class="pos-age"><?= $value['pos_age'] ?>歳の自分へ</span>
                                                                    </p>
                                                                </div>
                                                                <p class="message line-limit">
                                                                    <?= nl2br($value['message']); ?>
                                                                    <?php if ($value['edit'] == 1) : ?>
                                                                        <span class="edited">（編集済み）</span>
                                                                    <?php endif ?>
                                                                </p>
                                                                <object>
                                                                    <a class="category" href="./index.php?cur=&pos=&user=&order=&category=<?= $value['category'] ?>&sort=1">
                                                                        <span><?= $category_list[$value['category']] ?></span>
                                                                        <div class="rbr"></div>
                                                                        <div class="rbl"></div>
                                                                        <div class="rbb"></div>
                                                                        <div class="rbt"></div>
                                                                    </a>
                                                                </object>
                                                            <?php } ?>
                                                            <ul class="reaction">
                                                                <li class="comment-btn hover" data-id="<?= $value['id'] ?>">
                                                                    <div class="heart-bg"></div>
                                                                    <div class="comment"></div>
                                                                    <span><?= number_unit($value['comment']) ?></span>
                                                                </li>
                                                                <?php if (in_array($value['id'], $good_list)) { ?>
                                                                    <li class="dl-good-btn" data-id="<?= $value['id'] ?>" data-comment="<?= $value['comment'] ?>" data-good="<?= $value['good'] ?>">
                                                                        <div class="heart-bg"></div>
                                                                        <div class="heart-after"></div>
                                                                        <div class="good-count">
                                                                            <span><?= number_unit($value['good'] - 1) ?></span>
                                                                            <span><?= number_unit($value['good']) ?></span>
                                                                        </div>
                                                                    </li>
                                                                <?php } else { ?>
                                                                    <li class="good-btn" data-id="<?= $value['id'] ?>" data-comment="<?= $value['comment'] ?>" data-good="<?= $value['good'] ?>">
                                                                        <div class="heart-bg"></div>
                                                                        <div class="heart-before"></div>
                                                                        <div class="good-count">
                                                                            <span><?= number_unit($value['good']) ?></span>
                                                                            <span><?= number_unit($value['good'] + 1) ?></span>
                                                                        </div>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                            </article>
                                            <div class="br"></div>
                                            <div class="bl"></div>
                                            <div class="bb"></div>
                                            <div class="bt"></div>
                                    </a>
                                <?php endforeach ?>
                            <?php } ?>
                        </div>
                        <div id="good-letter-list" class="panel">
                            <?php if (!isset($g_row)) { ?>
                                <p class="no-letter">いいね！された手紙はまだありません</p>
                            <?php } else { ?>
                                <?php foreach ($g_row as $value) : ?>
                                    <a href="./letter.php?id=<?= $value['id'] ?>" class="letter hover">
                                        <?php if ($value['user_id'] == $id) { ?>
                                            <div class="report-btn-bg sp-edit-btn" data-id="<?= $value['id'] ?>">
                                                <div class="report-icon">
                                                    <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                                                        <g>
                                                            <circle class="st0" cx="256" cy="55.091" r="55.091" style="fill: #7a7a7a"></circle>
                                                            <circle class="st0" cx="256" cy="256" r="55.091" style="fill: #7a7a7a"></circle>
                                                            <circle class="st0" cx="256" cy="456.909" r="55.091" style="fill: #7a7a7a"></circle>
                                                        </g>
                                                    </svg>
                                                </div>
                                            </div>
                                            <button data-id="<?= $value['id'] ?>" class="edit-btn">手紙を編集する</button>
                                            <article>
                                                <object class="icon"><a href="./mypage_top.php"><img src="./icon/<?= $value['icon'] ?>" alt="<?= $value['nickname'] ?>のアイコン"></a></object>
                                                <div class="contents">
                                                    <div class="info">
                                                        <div class="sp-block">
                                                            <object class="writer">
                                                                <a href="./mypage_top.php">
                                                                    <?php if ($value['hide'] == 2) : ?>
                                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                    <?php endif ?>
                                                                    <p><?= $value['nickname'] ?></p>
                                                                </a>
                                                            </object>
                                                        <?php } else { ?>
                                                            <div class="report-btn-bg sp-report-btn" data-id="<?= $value['id'] ?>">
                                                                <div class="report-icon">
                                                                    <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                                                                        <g>
                                                                            <circle class="st0" cx="256" cy="55.091" r="55.091" style="fill: #7a7a7a"></circle>
                                                                            <circle class="st0" cx="256" cy="256" r="55.091" style="fill: #7a7a7a"></circle>
                                                                            <circle class="st0" cx="256" cy="456.909" r="55.091" style="fill: #7a7a7a"></circle>
                                                                        </g>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <button class="report-modal-btn" data-id="<?= $value['id'] ?>">手紙を通報する</button>
                                                            <article>
                                                                <object class="icon"><a href="./userpage_top.php?id=<?= $value['user_id'] ?>"><img src="./icon/<?= $value['icon'] ?>" alt="<?= $value['nickname'] ?>のアイコン"></a></object>
                                                                <div class="contents">
                                                                    <div class="info">
                                                                        <div class="sp-block">
                                                                            <object class="writer">
                                                                                <a href="./userpage_top.php?id=<?= $value['user_id'] ?>">
                                                                                    <?php if ($value['hide'] == 2) : ?>
                                                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                                    <?php endif ?>
                                                                                    <p><?= $value['nickname'] ?></p>
                                                                                </a>
                                                                            </object>
                                                                        <?php } ?>
                                                                        <time class="time" datetime="<?= date("Y.m.d H:i", strtotime($value['created_at'])) ?>">・<?= letter_time($value['created_at']) ?></time>
                                                                        </div>
                                                                        <?php if (($value['ng_word'] == 1)) { ?>
                                                                    </div>
                                                                    <p class="message ng">不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                                <?php } else { ?>
                                                                    <p class="title">
                                                                        <?php if ($value['cur_age'] == 200) { ?>
                                                                            <span class="cur-age">非公表</span>
                                                                        <?php } else { ?>
                                                                            <span class="cur-age"><?= $value['cur_age'] ?>歳</span>
                                                                        <?php } ?>
                                                                        <span class="send"></span>
                                                                        <span class="pos-age"><?= $value['pos_age'] ?>歳の自分へ</span>
                                                                    </p>
                                                                </div>
                                                                <p class="message line-limit">
                                                                    <?= nl2br($value['message']); ?>
                                                                    <?php if ($value['edit'] == 1) : ?>
                                                                        <span class="edited">（編集済み）</span>
                                                                    <?php endif ?>
                                                                </p>
                                                                <object>
                                                                    <a class="category" href="./index.php?cur=&pos=&user=&order=&category=<?= $value['category'] ?>&sort=1">
                                                                        <span><?= $category_list[$value['category']] ?></span>
                                                                        <div class="rbr"></div>
                                                                        <div class="rbl"></div>
                                                                        <div class="rbb"></div>
                                                                        <div class="rbt"></div>
                                                                    </a>
                                                                </object>
                                                            <?php } ?>
                                                            <ul class="reaction">
                                                                <li class="comment-btn hover" data-id="<?= $value['id'] ?>">
                                                                    <div class="heart-bg"></div>
                                                                    <div class="comment"></div>
                                                                    <span><?= number_unit($value['comment']) ?></span>
                                                                </li>
                                                                <?php if (in_array($value['id'], $good_list)) { ?>
                                                                    <li class="dl-good-btn" data-id="<?= $value['id'] ?>" data-comment="<?= $value['comment'] ?>" data-good="<?= $value['good'] ?>">
                                                                        <div class="heart-bg"></div>
                                                                        <div class="heart-after"></div>
                                                                        <div class="good-count">
                                                                            <span><?= number_unit($value['good'] - 1) ?></span>
                                                                            <span><?= number_unit($value['good']) ?></span>
                                                                        </div>
                                                                    </li>
                                                                <?php } else { ?>
                                                                    <li class="good-btn" data-id="<?= $value['id'] ?>" data-comment="<?= $value['comment'] ?>" data-good="<?= $value['good'] ?>">
                                                                        <div class="heart-bg"></div>
                                                                        <div class="heart-before"></div>
                                                                        <div class="good-count">
                                                                            <span><?= number_unit($value['good']) ?></span>
                                                                            <span><?= number_unit($value['good'] + 1) ?></span>
                                                                        </div>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                            </article>
                                            <div class="br"></div>
                                            <div class="bl"></div>
                                            <div class="bb"></div>
                                            <div class="bt"></div>
                                    </a>
                                <?php endforeach; ?>
                            <?php } ?>
                        </div>
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
            </div>
        <?php } else { ?>
            <div class="flex-userpage wrapper">
                <div class="user-profile">
                    <div class="user">
                        <div class="user-info">
                            <div class="top-icon"><img src="./icon/<?= $rec4['icon'] ?>" alt="<?= $rec4['nickname'] ?>のアイコン"></div>
                            <div class="user-name">
                                <div class="nickname" style="margin-bottom: 20px;">
                                    <?php if ($rec4['hide'] == 2) : ?>
                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                    <?php endif ?>
                                    <span><?= $rec4['nickname'] ?></span>
                                </div>
                                <p class="good"><img src="./img/heart-after.svg" alt="いいね！マーク"><?= number_unit($user_total['good']) ?></p>
                                <div class="sp-user-btn">
                                    <button class="follow-btn">
                                        <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                    </button>
                                    <div class="user-menu-btn"></div>
                                    <button class="report-user-modal-btn">このユーザーを通報する</button>
                                </div>
                            </div>
                        </div>
                        <div class="user-btn">
                            <button class="follow-btn">
                                <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                            </button>
                            <div class="user-menu-btn"></div>
                            <button class="report-user-modal-btn">このユーザーを通報する</button>
                        </div>
                    </div>
                    <ul>
                        <li><a href="userpage_top.php?id=<?= $user_id ?>"><span><?= number_unit($rec1['lt_count']) ?></span>投函数</a></li>
                        <li>
                            <a href="mypage_follower.php" class="follower"><span><?= number_unit($rec2['follower_count']) ?></span>フォロワー</a>
                            <div class="bl"></div>
                        </li>
                        <li>
                            <a href="mypage_following.php" class="following"><span><?= number_unit($rec3['following_count']) ?></span>フォロー中</a>
                            <div class="bl"></div>
                        </li>
                    </ul>
                    <p class="intro"><?= nl2br($rec4['intro']) ?></p>
                </div>
                <?php if ($rec4['hide'] == 1) { ?>
                    <div id="letter-area">
                        <ul class="tab-list">
                            <li class="tab active" data-left="0">投函した手紙</li>
                            <li class="tab good-letter" data-left="50%">いいね！した手紙</li>
                            <div class="active-bar"></div>
                            <div class="bb"></div>
                        </ul>
                        <div id="letter-list">
                            <?php if (!isset($row)) { ?>
                                <p class="no-letter">まだ投函されたお手紙はありません</p>
                            <?php } else { ?>
                                <?php foreach ($row as $value) : ?>
                                    <a href="./letter.php?id=<?= $value['id'] ?>" class="letter">
                                        <div class="report-btn-bg sp-report-btn" data-id="<?= $value['id'] ?>">
                                            <div class="report-icon">
                                                <svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve">
                                                    <g>
                                                        <circle class="st0" cx="256" cy="55.091" r="55.091" style="fill: #7a7a7a"></circle>
                                                        <circle class="st0" cx="256" cy="256" r="55.091" style="fill: #7a7a7a"></circle>
                                                        <circle class="st0" cx="256" cy="456.909" r="55.091" style="fill: #7a7a7a"></circle>
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                        <article>
                                            <object class="icon"><a href="./userpage_top.php?id=<?= $value['user_id'] ?>"><img src="./icon/<?= $value['icon'] ?>" alt="<?= $value['nickname'] ?>のアイコン"></a></object>
                                            <div class="contents">
                                                <div class="info">
                                                    <div class="sp-block">
                                                        <object class="writer"><a href="./userpage_top.php?id=<?= $value['user_id'] ?>"><?= $value['nickname'] ?></a></object>
                                                        <time class="time" datetime="<?= date("Y.m.d H:i", strtotime($value['created_at'])) ?>">・<?= letter_time($value['created_at']) ?></time>
                                                    </div>
                                                    <?php if (($value['ng_word'] == 1)) { ?>
                                                </div>
                                                <p class="message ng">不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                            <?php } else { ?>
                                                <p class="title">
                                                    <?php if ($value['cur_age'] == 200) { ?>
                                                        <span class="cur-age">非公表</span>
                                                    <?php } else { ?>
                                                        <span class="cur-age"><?= $value['cur_age'] ?>歳</span>
                                                    <?php } ?>
                                                    <span class="send"></span>
                                                    <span class="pos-age"><?= $value['pos_age'] ?>歳の自分へ</span>
                                                </p>
                                            </div>
                                            <p class="message line-limit">
                                                <?= nl2br($value['message']); ?>
                                                <?php if ($value['edit'] == 1) : ?>
                                                    <span class="edited">（編集済み）</span>
                                                <?php endif ?>
                                            </p>
                                            <object>
                                                <a class="category" href="./index.php?cur=&pos=&user=&order=&category=<?= $value['category'] ?>&sort=1">
                                                    <span><?= $category_list[$value['category']] ?></span>
                                                    <div class="rbr"></div>
                                                    <div class="rbl"></div>
                                                    <div class="rbb"></div>
                                                    <div class="rbt"></div>
                                                </a>
                                            </object>
                                        <?php } ?>
                                        <ul class="reaction">
                                            <li class="comment-btn hover">
                                                <div class="heart-bg"></div>
                                                <div class="comment"></div>
                                                <span><?= number_unit($value['comment']) ?></span>
                                            </li>
                                            <li class="good-btn">
                                                <div class="heart-bg"></div>
                                                <div class="heart-before"></div>
                                                <div class="good-count">
                                                    <span><?= number_unit($value['good']) ?></span>
                                                    <span><?= number_unit($value['good'] + 1) ?></span>
                                                </div>
                                            </li>
                                        </ul>
                        </div>
                        </article>
                        <div class="br"></div>
                        <div class="bl"></div>
                        <div class="bb"></div>
                        <div class="bt"></div>
                        </a>
                    <?php endforeach ?>
                <?php } ?>
                    </div>
                    <div class="bl">
                    </div>
                <?php } else { ?>
                    <div id="letter-area">
                        <div class="hide-status-outer">
                            <div class="hide-status">
                                <div class="icon"><img src="./img/hide-status.svg"></div>
                                <h3>手紙は非公開です</h3>
                                <p><?= $rec4['nickname'] ?>さんから承認された場合のみ手紙を表示できます。承認をリクエストするには [＋フォロー] をクリックします</p>
                            </div>
                        </div>
                        <div class="bl"></div>
                    </div>
                <?php } ?>
            </div>
            </div>
        <?php } ?>
        <!-- ０ -->
        <?php include_once('./views/user-window.php') ?>
        <!-- １ -->
        <?php include_once('./views/report-modal.html') ?>
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
                <p>未承認のフォローリクエストがキャンセルされ、<?= $rec4['nickname'] ?>さんには表示されなくなります。 </p>
                <div>
                    <button class="modal-cancel">キャンセル</button>
                    <button class="request-cancel-btn">破棄する</button>
                </div>
            </div>
        </div>
        <!-- ４プロフィール（鍵垢） -->
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
        <!-- ５プロフィール（通常） -->
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
        <!-- ６ -->
        <div class="sp-modal-bg modal-bg close">
            <div class="sp-modal">
                <ul class="sp-report-list">
                    <li><button class="report-user-modal-btn">このユーザーを通報する</button></li>
                    <li><button class="modal-cancel">キャンセル</button></li>
                </ul>
            </div>
        </div>
        <!-- ７ -->
        <div class="sp-report-bg modal-bg close">
            <div class="sp-modal">
                <ul class="sp-report-list">
                    <li> <button class="report-modal-btn" data-id="<?= $value['id'] ?>">手紙を通報する</button></li>
                    <li><button class="modal-cancel">キャンセル</button></li>
                </ul>
            </div>
        </div>
        <!-- ８ -->
        <div class="sp-edit-bg modal-bg close">
            <div class="sp-modal">
                <ul class="sp-report-list">
                    <li><button data-id="<?= $value['id'] ?>" class="edit-btn">手紙を編集する</button></li>
                    <li><button class="modal-cancel">キャンセル</button></li>
                </ul>
            </div>
        </div>
        <!-- ９ -->
        <div class="modal-bg close icon-modal">
            <div class="modal-cancel"></div>
            <div id="modal-icon" class="icon"><img src="./icon/<?= $rec4['icon'] ?>" alt="<?= $nickname ?>のアイコン"></div>
        </div>
        <!-- １０ -->
        <div class="modal-bg close">
            <div class="comment-modal modal">
                <div class="modal-cancel"></div>
                <div class="comment-form">
                    <dl>
                        <dt><label for="comment">手紙へのコメント</label></dt>
                        <dd>
                            <textarea name="comment" id="comment" placeholder="コメントする"></textarea>
                            <div class="input-assistance">
                                <p class="alert-text"></p>
                                <span class="comment-length">0/500</span>
                            </div>
                        </dd>
                    </dl>
                    <p>相手のことを考え丁寧なコメントを心がけましょう。不快な言葉遣いなどはコメント削除や退会処分となることがあります。</p>
                    <button id="comment-btn">コメントを送信する</button>
                </div>
            </div>
        </div>
        <!-- １１ -->
        <?php include_once('./views/login-modal.html') ?>
        <?php if (isset($_COOKIE['ajax'])) : ?>
            <?php if ($_COOKIE['ajax'] == 'report-letter') : ?>
                <div class="notice"><img src="./img/check-red.svg">手紙を報告しました</div>
            <?php endif ?>
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
    <script src="./js/function.js"></script>
    <script>
        'use strict';
        const userid = JSON.parse('<?= $id_json ?>');
        const followid = JSON.parse('<?= $user_id_json ?>');
        const login = '<?= $login ?>';
        const userNickName = '<?= $user_nickname ?>';
        const comment = document.querySelector('#comment');

        $('.top-icon').on('click', function() {
            modalBg[9].classList.remove('close');
            document.addEventListener('touchmove', disableScroll, {
                passive: false
            });
            $('body').css('overflow', 'hidden');
        })
        $('#modal-icon').on('click', function() {
            return false;
        })

        if (login == 1) {
            $(document).on('click', '.follow-btn', function() {
                $.ajax({
                    type: "POST",
                    url: "ajax_follow.php",
                    data: {
                        "followid": followid,
                        "userid": userid
                    }
                }).done((data) => {
                    let windowWidth = window.innerWidth;
                    if (windowWidth < 769) {
                        $(this).remove();
                        $('.sp-user-btn').prepend(data);
                    } else {
                        $(this).remove();
                        $('.user-btn').prepend(data);
                    }
                });
                return false;
            });

            $(document).on('click', '.follow-request-btn', function() {
                $.ajax({
                    type: "POST",
                    url: "ajax_follow_request.php",
                    data: {
                        "followid": followid,
                        "userid": userid
                    }
                }).done((data) => {
                    let windowWidth = window.innerWidth;
                    if (windowWidth < 769) {
                        $(this).remove();
                        $('.sp-user-btn').prepend(data);
                        $('.notice').remove();
                        $('body').append(`<div class="notice nocheck">${userNickName}さんへフォローリクエストが送信され、承認待ちになりました。</div>`);
                    } else {
                        $(this).remove();
                        $('.user-btn').prepend(data);
                        $('.notice').remove();
                        $('body').append(`<div class="notice nocheck">${userNickName}さんへフォローリクエストが送信され、承認待ちになりました。</div>`);
                    }
                });
                return false;
            });

            $(document).on('click', '.unapproved-btn', function() {
                modalBg[3].classList.remove('close');
                document.addEventListener('touchmove', disableScroll, {
                    passive: false
                });
                $('body').css('overflow', 'hidden');
            });

            $(document).on('click', '.hide-unfollow-confirm', function() {
                modalBg[4].classList.remove('close');
                document.addEventListener('touchmove', disableScroll, {
                    passive: false
                });
                $('body').css('overflow', 'hidden');
            });

            $(document).on('click', '.unfollow-confirm', function() {
                modalBg[5].classList.remove('close');
                document.addEventListener('touchmove', disableScroll, {
                    passive: false
                });
                $('body').css('overflow', 'hidden');
                return false;
            });

            $('.request-cancel-btn').on('click', function() {
                $.ajax({
                    type: "POST",
                    url: "ajax_follow_request_cancel.php",
                    data: {
                        "followid": followid,
                        "userid": userid
                    }
                }).done((data) => {
                    let windowWidth = window.innerWidth;
                    if (windowWidth < 769) {
                        $('.unapproved-btn').remove();
                        $('.sp-user-btn').prepend(data);
                    } else {
                        $('.unapproved-btn').remove();
                        $('.user-btn').prepend(data);
                    }
                    modalBg[3].classList.add('close');
                });
                return false;
            });

            $('.hide-unfollow-btn').on('click', function() {
                $.ajax({
                    type: "POST",
                    url: "ajax_hide_unfollow.php",
                    data: {
                        "unfollowid": followid,
                        "userid": userid
                    }
                }).done((data) => {
                    let windowWidth = window.innerWidth;
                    if (windowWidth < 769) {
                        $('.hide-unfollow-confirm').remove();
                        $('.sp-user-btn').prepend(data);
                    } else {
                        $('.hide-unfollow-confirm').remove();
                        $('.user-btn').prepend(data);
                    }
                    modalBg[4].classList.add('close');
                    location.reload();
                });
                return false;
            });

            $('.unfollow-btn').on('click', function() {
                $.ajax({
                    type: "POST",
                    url: "ajax_unfollow.php",
                    data: {
                        "unfollowid": followid,
                        "userid": userid
                    }
                }).done((data) => {
                    let windowWidth = window.innerWidth;
                    if (windowWidth < 769) {
                        $(this).remove();
                        $('.sp-user-btn').prepend(data);
                    } else {
                        $('.unfollow-confirm').remove();
                        $('.user-btn').prepend(data);
                    }
                    modalBg[5].classList.add('close');
                });
                return false;
            });

            $('.comment-btn').on('click', function() {
                const letterid = $(this).attr('data-id');
                $('#comment-btn').attr('data-id', letterid);
                modalBg[10].classList.remove('close');
                $('body').css('overflow', 'hidden');
                document.removeEventListener('touchmove', disableScroll, {
                    passive: false
                });
                return false;
            })

            inputlength(comment, '.comment-length', 500);
            $('#comment-btn').on('click', function() {
                const alert_text = document.querySelectorAll('.alert-text');
                const letterid = $(this).attr('data-id');
                let flg = false;

                if (!comment.value.match(/[\S]+/)) {
                    alert_text[0].textContent = 'コメントを入力してください';
                    textArea[0].style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (comment.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                        alert_text[0].textContent = '特殊文字は入力できません';
                        textArea[0].style.border = '1px solid #e33339';
                        flg = true;
                    } else {
                        if (comment.value.length > 500) {
                            alert_text[0].textContent = '500文字以下で入力してください';
                            textArea[0].style.border = '1px solid #e33339';
                            flg = true;
                        }
                    }
                }

                if (flg) {
                    comment.addEventListener('keyup', () => {
                        if (!comment.value.match(/[\S]+/)) {
                            alert_text[0].textContent = 'コメントを入力してください';
                            textArea[0].style.border = '1px solid #e33339';
                        } else {
                            if (comment.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                                alert_text[0].textContent = '特殊文字は入力できません';
                                textArea[0].style.border = '1px solid #e33339';
                            } else {
                                if (comment.value.length > 500) {
                                    alert_text[0].textContent = '500文字以下で入力してください';
                                    textArea[0].style.border = '1px solid #e33339';
                                } else {
                                    alert_text[0].textContent = '';
                                    textArea[0].style.border = '1px solid #696969';
                                }
                            }
                        }
                    })
                } else {
                    var replaceCom = comment.value.replace(/[^\S\n]+/g, '');
                    $.ajax({
                        type: "POST",
                        url: "ajax_ngword_check.php",
                        datatype: "json",
                        data: {
                            "check1": replaceCom,
                        }
                    }).done((data) => {
                        if (data == 1) {
                            var wordCheck = 1;
                            $.ajax({
                                type: "POST",
                                url: "ajax_comment_insert.php",
                                data: {
                                    "wordcheck": wordCheck,
                                    "letterid": letterid,
                                    "userid": userid,
                                    "comment": replaceCom,
                                }
                            }).done((data) => {
                                document.getElementById('comment').value = '';
                                modalBg[10].classList.add('close');
                                $('body').css('overflow', 'visible');
                                document.removeEventListener('touchmove', disableScroll, {
                                    passive: false
                                });
                                $('.notice').remove();
                                $('body').append(`<div class="notice"><img src="./img/check-red.svg">コメントを送信しました</div>`);
                            });
                        } else {
                            var wordCheck = 0;
                            $.ajax({
                                type: "POST",
                                url: "ajax_comment_insert.php",
                                data: {
                                    "wordcheck": wordCheck,
                                    "letterid": letterid,
                                    "userid": userid,
                                    "comment": replaceCom,
                                }
                            }).done((data) => {
                                document.getElementById('comment').value = '';
                                modalBg[10].classList.add('close');
                                $('body').css('overflow', 'visible');
                                document.removeEventListener('touchmove', disableScroll, {
                                    passive: false
                                });
                                $('.notice').remove();
                                $('body').append(`<div class="notice"><img src="./img/check-red.svg">コメントを送信しました</div>`);
                            });
                        }
                    });
                    return false;
                }
            })
        } else {
            $('.follow-btn,.good-btn,.comment-btn,.following,.follower,.good-letter,.user-menu-btn,.report-btn-bg').click(() => {
                event.preventDefault();
                modalBg[11].classList.remove('close');
                document.addEventListener('touchmove', disableScroll, {
                    passive: false
                });
                $('body').css('overflow', 'hidden');
            })
        }
    </script>
    <script src="./js/index.js"></script>
</body>

</html>