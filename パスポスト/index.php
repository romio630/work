<?php
require_once('./common/dbconnect.php');
require_once('./common/sanitize.php');
require_once('./common/function.php');

if (isset($_COOKIE['paspost_id'])) {
    if (isset($_SESSION)) {
        session_start();
        session_regenerate_id(true);
    } else {
        $sql = 'SELECT nickname,icon FROM pp_user WHERE id=?';
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($_COOKIE['paspost_id']));
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);

        session_start();
        $_SESSION['nickname'] = $rec['nickname'];
        $_SESSION['id'] = $_COOKIE['paspost_id'];
        $_SESSION['icon'] = $rec['icon'];
    }
    $login = 1;
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];
    $id_json = json_encode($id);
    require_once('./common/sql.php');

    $sql = 'SELECT letter_id FROM good_list WHERE giver_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $good_list[] = $rec['letter_id'];
    }
    if (!isset($good_list)) {
        $good_list = [];
    }

    $sql = "SELECT * from questionnaire WHERE user_id=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    $already_write = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $login = 0;
    $id_json = json_encode(1);    // バグ対策
}

$is_mv_flg = true;
if (isset($_GET['sort'])) {
    $is_mv_flg = false;
}
if (isset($_GET['page'])) {
    $is_mv_flg = false;
}

if (isset($_REQUEST['page'])) {
    $page = htmlspecialchars($_REQUEST['page'], ENT_QUOTES);
} else {
    $page = 1;
}
$start = 8 * ($page - 1);

require_once('./common/sort.php');
require_once('./common/category.php');
?>

<!DOCTYPE html>
<html lang="ja">

<head prefix="og:http://ogp.me/ns#">
    <meta property="og:title" content="パスポスト">
    <meta property="og:description" content="昔の自分に宛てた手紙を今を生きる誰かに届けるサイト">
    <meta property="og:url" content="https://pas-post.com">
    <meta property="og:image" content="https://pas-post.com/img/ogp.jpg">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="パスポスト">
    <title>パスポスト</title>
    <meta name="description" content="昔の自分に宛てた手紙を今を生きる誰かに届けるサイト" />
    <meta name="keywords" content="パスポスト,ポスト,手紙,昔,今,思い出,掲示板,仕事,恋愛,学生時代,家族,後悔,告白" />
    <?php include('./views/head.html') ?>
    <link rel="stylesheet" href="./css/vegas.min.css">
    <link rel="stylesheet" href="./css/top.css">
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php if ($is_mv_flg) { ?>
            <div class="mainvisual wrapper">
                <div class="slider-outer">
                    <div id="slider">
                        <div class="br"></div>
                        <div class="bl"></div>
                        <div class="bb"></div>
                    </div>
                    <div class="main-text">
                        <p class="main-text-right"><span>昔の自分に宛てた手紙が</span></p>
                        <p class="main-text-left"><span>今の誰かの心に届く。</span></p>
                    </div>
                </div>
            </div>
            <div id="container" class="wrapper">
            <?php } else { ?>
                <div id="container" class="wrapper page">
                <?php } ?>
                <div class="main-content">
                    <?php if ($letter_number != 0) { ?>
                        <?php if (isset($_GET['sort'])) { ?>
                            <h3>検索結果</h3>
                            <?php if ($page < $max_page) { ?>
                                <p class="result-stats">該当数<?= $letter_number ?>通<span><?= $start + 1 ?>～<?= $start + 8 ?>通目表示</span></p>
                            <?php } else { ?>
                                <p class="result-stats">該当数<?= $letter_number ?>通<span><?= $start + 1 ?>～<?= $letter_number ?>通目表示</span></p>
                            <?php } ?>
                        <?php } else { ?>
                            <?php if ($page < $max_page) { ?>
                                <p class="result-stats">全<?= $letter_number ?>通<span><?= $start + 1 ?>～<?= $start + 8 ?>通目表示</span></p>
                            <?php } else { ?>
                                <p class="result-stats">全<?= $letter_number ?>通<span><?= $start + 1 ?>～<?= $letter_number ?>通目表示</span></p>
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                        <h3>検索結果</h3>
                        <p class="result-stats">該当数0通</p>
                    <?php } ?>
                    <div id="letter-list">
                        <?php if (isset($row)) { ?>
                            <?php if (isset($_COOKIE['paspost_id'])) { ?>
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
                                                                <object class="icon">
                                                                    <a href="./userpage_top.php?id=<?= $value['user_id'] ?>"><img src="./icon/<?= $value['icon'] ?>" alt="<?= $value['nickname'] ?>のアイコン"></a>
                                                                </object>
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
                            <?php } else { ?>
                                <?php foreach ($row as $value) : ?>
                                    <a href="./letter.php?id=<?= $value['id'] ?>" class="letter hover">
                                        <div class="report-btn-bg" data-id="<?= $value['id'] ?>">
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
                                                        <object class="writer">
                                                            <a href="./userpage_top.php?id=<?= $value['user_id'] ?>">
                                                                <?php if ($value['hide'] == 2) : ?>
                                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                <?php endif ?>
                                                                <p><?= $value['nickname'] ?></p>
                                                            </a>
                                                        </object>
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
                <?php endforeach; ?>
            <?php } ?>
        <?php } else { ?>
            <div class="caution">
                <div class="logo"><img src="./img/logo-glee.svg" alt="パスポストのアイコン"></div>
                <?php if (isset($_GET['sort'])) { ?>
                    <p>条件に該当する手紙はありません</p>
                <?php } else { ?>
                    <p>投函された手紙はありません</p>
                <?php } ?>
            </div>
        <?php } ?>
                </div>
                <ul class="page-num">
                    <?php if (isset($_GET['sort'])) { ?>
                        <?php if ($page >= 2) { ?>
                            <li class="prev">
                                <a href="index.php?page=<?= $page - 1 ?>&cur=<?= $cur_above ?>&pos=<?= $pos_above ?>&user=<?= $user ?>&order=<?= $order ?>&category=<?= $category ?>&sort=1">前へ</a>
                            </li>
                        <?php } else { ?>
                            <li class="prev none">
                                <a href="index.php?page=<?= $page - 1 ?>&cur=<?= $cur_above ?>&pos=<?= $pos_above ?>&user=<?= $user ?>&order=<?= $order ?>&category=<?= $category ?>&sort=1">前へ</a>
                            </li>
                        <?php } ?>
                        <?php if ($letter_number > 8) : ?>
                            <li class="current-page"><?= $page ?></li>
                        <?php endif ?>
                        <?php if ($page < $max_page) { ?>
                            <li class="next">
                                <a href="index.php?page=<?= $page + 1 ?>&cur=<?= $cur_above ?>&pos=<?= $pos_above ?>&user=<?= $user ?>&order=<?= $order ?>&category=<?= $category ?>&sort=1">次へ</a>
                            </li>
                        <?php } else { ?>
                            <li class="next none">
                                <a href="index.php?page=<?= $page + 1 ?>&cur=<?= $cur_above ?>&pos=<?= $pos_above ?>&user=<?= $user ?>&order=<?= $order ?>&category=<?= $category ?>&sort=1">次へ</a>
                            </li>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if ($page >= 2) { ?>
                            <li class="prev">
                                <a href="index.php?page=<?= $page - 1 ?>">前へ</a>
                            </li>
                        <?php } else { ?>
                            <li class="prev none">
                                <a href="index.php?page=<?= $page - 1 ?>">前へ</a>
                            </li>
                        <?php } ?>
                        <?php if ($letter_number > 8) : ?>
                            <li class="current-page"><?= $page ?></li>
                        <?php endif ?>
                        <?php if ($page < $max_page) { ?>
                            <li class="next">
                                <a href="index.php?page=<?= $page + 1 ?>">次へ</a>
                            </li>
                        <?php } else { ?>
                            <li class="next none">
                                <a href="index.php?page=<?= $page + 1 ?>">次へ</a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
                </div>
                <div id="sort">
                    <div class="letter-search-btn">手紙を検索する</div>
                    <form id="letter-search" action="./index.php" method="get">
                        <dl>
                            <dt><i class="fas fa-history"></i>年齢から探す</dt>
                            <dd>
                                <div class="age-select">
                                    <?php if (isset($cur_above)) { ?>
                                        <select id="cur-age" name="cur">
                                            <?php if ($cur_above == 200) { ?>
                                                <option value="">全年齢</option>
                                                <?php for ($i = 10; $i <= 120; $i = $i + 10) { ?>
                                                    <option value="<?= $i ?>"><?= $i ?>代</option>
                                                <?php } ?>
                                                <option value="200" selected>非公表</option>
                                            <?php } else { ?>
                                                <option value="">全年齢</option>
                                                <?php for ($i = 10; $i <= 120; $i = $i + 10) { ?>
                                                    <?php if ($cur_above == $i) { ?>
                                                        <option value="<?= $i ?>" selected><?= $i ?>代</option>
                                                    <?php } else { ?>
                                                        <option value="<?= $i ?>"><?= $i ?>代</option>
                                                    <?php } ?>
                                                <?php } ?>
                                                <option value="200">非公表</option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select id="cur-age" name="cur">
                                            <option value="">全年齢</option>
                                            <?php for ($i = 10; $i <= 120; $i = $i + 10) { ?>
                                                <option value="<?= $i ?>"><?= $i ?>代</option>
                                            <?php } ?>
                                            <option value="200">非公表</option>
                                        </select>
                                    <?php } ?>
                                </div>
                                <div class="mail-to">
                                    <span class="send"><img src="./img/send-white.svg"></span>
                                </div>
                                <div class="age-select">
                                    <?php if (isset($pos_above)) { ?>
                                        <select id="pos-age" name="pos">
                                            <option value="">全年齢</option>
                                            <?php for ($i = 10; $i <= 120; $i = $i + 10) { ?>
                                                <?php if ($pos_above == $i) { ?>
                                                    <option value="<?= $i ?>" selected><?= $i ?>代</option>
                                                <?php } else { ?>
                                                    <option value="<?= $i ?>"><?= $i ?>代</option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <select id="pos-age" name="pos">
                                            <option value="">全年齢</option>
                                            <?php for ($i = 10; $i <= 120; $i = $i + 10) { ?>
                                                <option value="<?= $i ?>"><?= $i ?>代</option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                            </dd>
                            <dt><i class="fas fa-sort-numeric-down"></i>並びかえる</dt>
                            <dd>
                                <?php if (isset($order)) { ?>
                                    <?php if ($order == 2) { ?>
                                        <input type="radio" id="order-cur" value="1" name="order"><label for="order-cur">新着順</label>
                                        <input type="radio" id="order-good" value="2" name="order" checked><label for="order-good">いいね！順</label>
                                    <?php } else { ?>
                                        <input type="radio" id="order-cur" value="1" name="order" checked><label for="order-cur">新着順</label>
                                        <input type="radio" id="order-good" value="2" name="order"><label for="order-good">いいね！順</label>
                                    <?php } ?>
                                <?php } else { ?>
                                    <input type="radio" id="order-cur" value="1" name="order" checked><label for="order-cur">新着順</label>
                                    <input type="radio" id="order-good" value="2" name="order"><label for="order-good">いいね！順</label>
                                <?php } ?>
                            </dd>
                            <?php if (isset($_SESSION['id'])) : ?>
                                <dt><i class="fas fa-user"></i>ユーザー</dt>
                                <dd>
                                    <?php if (isset($user)) { ?>
                                        <?php if ($user == 2) { ?>
                                            <input type="radio" id="user-all" value="1" name="user"><label for="user-all">全ユーザー</label>
                                            <input type="radio" id="user-following" value="2" name="user" checked><label for="user-following">フォロー中のみ</label>
                                        <?php } else { ?>
                                            <input type="radio" id="user-all" value="1" name="user" checked><label for="user-all">全ユーザー</label>
                                            <input type="radio" id="user-following" value="2" name="user"><label for="user-following">フォロー中のみ</label>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <input type="radio" id="user-all" value="1" name="user" checked><label for="user-all">全ユーザー</label>
                                        <input type="radio" id="user-following" value="2" name="user"><label for="user-following">フォロー中のみ</label>
                                    <?php } ?>

                                </dd>
                            <?php endif ?>
                            <dt><i class="fas fa-book-open"></i>カテゴリーで探す</dt>
                            <dd>
                                <ul>
                                    <?php if (isset($category)) { ?>
                                        <?php if ($category == 8) { ?>
                                            <li><input type="radio" id="all" name="category" value="8" checked><label for="all">＃全て</label></li>
                                        <?php } else { ?>
                                            <li><input type="radio" id="all" name="category" value="8"><label for="all">＃全て</label></li>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <li><input type="radio" id="all" name="category" value="8" checked><label for="all">＃全て</label></li>
                                    <?php } ?>
                                    <?php for ($i = 0; $i < count($category_list); $i++) : ?>
                                        <li>
                                            <?php if (isset($category)) { ?>
                                                <?php if ($category == $i && $category != '') { ?>
                                                    <input type="radio" id="<?= $en_category_list[$i] ?>" name="category" value="<?= $i ?>" checked><label for="<?= $en_category_list[$i] ?>"><?= $category_list[$i] ?></label>
                                                <?php } else { ?>
                                                    <input type="radio" id="<?= $en_category_list[$i] ?>" name="category" value="<?= $i ?>"><label for="<?= $en_category_list[$i] ?>"><?= $category_list[$i] ?></label>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <input type="radio" id="<?= $en_category_list[$i] ?>" name="category" value="<?= $i ?>"><label for="<?= $en_category_list[$i] ?>"><?= $category_list[$i] ?></label>
                                            <?php } ?>
                                        </li>
                                    <?php endfor ?>
                                </ul>
                            </dd>
                        </dl>
                        <input type="hidden" value="1" name="sort">
                        <div class="sort-btn"><input type="submit" value="検索する"></div>
                    </form>
                </div>
            </div>
            <!-- ０ -->
            <?php include_once('./views/user-window.php') ?>
            <?php if (isset($_COOKIE['paspost_id'])) : ?>
                <?php if (!$already_write) : ?>
                    <a href="./questionnaire.php" class="questionnaire">
                        <img src="./img/questionnaire-banner.svg" alt="アンケートのバナー">
                    </a>
                <?php endif ?>
            <?php endif ?>
            <!-- １ -->
            <?php include_once('./views/report-modal.html') ?>
            <!-- ２ -->
            <?php include_once('./views/login-modal.html') ?>
            <!-- ３ -->
            <div class="sp-report-bg modal-bg close">
                <div class="sp-modal">
                    <ul class="sp-report-list">
                        <li> <button class="report-modal-btn" data-id="<?= $value['id'] ?>">手紙を通報する</button></li>
                        <li><button class="modal-cancel">キャンセル</button></li>
                    </ul>
                </div>
            </div>
            <!-- ４ -->
            <div class="sp-edit-bg modal-bg close">
                <div class="sp-modal">
                    <ul class="sp-report-list">
                        <li><button data-id="<?= $value['id'] ?>" class="edit-btn">手紙を編集する</button></li>
                        <li><button class="modal-cancel">キャンセル</button></li>
                    </ul>
                </div>
            </div>
            <!-- ５ -->
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
            <?php if (isset($_COOKIE['ajax'])) : ?>
                <?php if ($_COOKIE['ajax'] == 'report-letter') : ?>
                    <div class="notice"><img src="./img/check-red.svg">手紙を報告しました</div>
                <?php endif ?>
            <?php endif ?>
    </main>
    <?php include('./views/footer.html') ?>
    <div class="fixed-menu">
        <div class="bt"></div>
        <nav>
            <ul>
                <li>
                    <a href="./index.php">
                        <div class="btn-inner">
                            <div class="sp-menu-icon"><img src="./img/home-f.svg"></div>
                            <p>ホーム</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="./notifications.php">
                        <div class="btn-inner">
                            <?php if (isset($unread) && $unread['cnt'] != 0) { ?>
                                <span class="notifications-cnt"><?= $unread['cnt'] ?></span>
                            <?php } elseif (isset($unread) && $unread['cnt'] > 99) { ?>
                                <span class="notifications-cnt">99<sup>+</sup></span>
                            <?php } ?>
                            <div class="sp-menu-icon"><img src="./img/bell.svg"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/vegas@2.5.4/dist/vegas.min.js"></script>
    <script src="./js/top.js"></script>
    <script src="./js/click-toggle.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script src="./js/function.js"></script>
    <script>
        var userid = JSON.parse('<?= $id_json ?>');
        const login = '<?= $login ?>';
        const comment = document.querySelector('#comment');
        $('.letter-search-btn').on('click', function() {
            $(this).toggleClass('active');
            $('#letter-search').stop().slideToggle();
        })
        if (login != 1) {
            $('.good-btn,.comment-btn,.report-btn-bg').click(() => {
                event.preventDefault();
                modalBg[2].classList.remove('close');
            })
        } else {
            $('.comment-btn').on('click', function() {
                const letterid = $(this).attr('data-id');
                $('#comment-btn').attr('data-id', letterid);
                modalBg[5].classList.remove('close');
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
                console.log(letterid);
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
                                modalBg[5].classList.add('close');
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
                                modalBg[5].classList.add('close');
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
        }
    </script>
    <script src="./js/index.js"></script>
</body>

</html>