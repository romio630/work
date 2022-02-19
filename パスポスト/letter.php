<?php
require_once('./common/dbconnect.php');
require_once('./common/sanitize.php');
require_once('./common/function.php');
require_once('./common/category.php');
session_start();
session_regenerate_id(true);
$letter_id = $_REQUEST['id'];

if (isset($_COOKIE['paspost_id'])) {
    $login = 1;
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];
    $id_json = json_encode($id);
    $letter_id_json = json_encode($letter_id);

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
    $id_json = json_encode(0); // バグ対策
    $letter_id_json = json_encode(0); // バグ対策
}

$sql = "SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,L.edit,U.icon,U.hide 
FROM letter as L join pp_user as U on L.user_id=U.id WHERE L.id=? ORDER BY created_at DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT U.id,U.nickname,U.icon FROM pp_user as U join good_list as G on U.id=G.giver_id WHERE G.letter_id=? ORDER BY G.created_at DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
while ($good_user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $good_user_list[] = $good_user;
}
if (!isset($good_user_list)) {
    $good_user_list = [];
}

$sql = "SELECT C.id,C.comment,C.created_at,C.is_delete,C.user_id,C.ng_word,U.nickname,U.icon,U.hide FROM comment as C join pp_user as U on C.user_id=U.id 
WHERE C.letter_id = ? ORDER BY C.created_at DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
while ($comment_item = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $comment_list[] = $comment_item;
}

$stmt = null;
$dbh = null;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/userpage.css">
    <link rel="stylesheet" href="./css/form.css">
    <title>手紙の投稿 -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <div class="wrapper">
            <?php if ($rec) { ?>
                <div id="letter" class="flex-letter">
                    <?php if (isset($_COOKIE['paspost_id'])) { ?>
                        <div class="letter">
                            <?php if ($rec['user_id'] == $id) { ?>
                                <div class="report-btn-bg sp-edit-btn" data-id="<?= $rec['id'] ?>">
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
                                <button data-id="<?= $rec['id'] ?>" class="edit-btn">手紙を編集する</button>
                                <article>
                                    <object class="icon"><a href="./mypage_top.php"><img src="./icon/<?= $rec['icon'] ?>" alt="<?= $rec['nickname'] ?>のアイコン"></a></object>
                                    <div class="contents">
                                        <div class="info">
                                            <div class="sp-block">
                                                <object class="writer">
                                                    <a href="./mypage_top.php">
                                                        <?php if ($rec['hide'] == 2) : ?>
                                                            <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                        <?php endif ?>
                                                        <p><?= $rec['nickname'] ?></p>
                                                    </a>
                                                </object>
                                            <?php } else { ?>
                                                <div class="report-btn-bg sp-report-btn" data-id="<?= $rec['id'] ?>">
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
                                                <button class="report-modal-btn" data-id="<?= $rec['id'] ?>">手紙を通報する</button>
                                                <article>
                                                    <object class="icon"><a href="./userpage_top.php?id=<?= $rec['user_id'] ?>"><img src="./icon/<?= $rec['icon'] ?>" alt="<?= $rec['nickname'] ?>のアイコン"></a></object>
                                                    <div class="contents">
                                                        <div class="info">
                                                            <div class="sp-block">
                                                                <object class="writer">
                                                                    <a href="./userpage_top.php?id=<?= $rec['user_id'] ?>">
                                                                        <?php if ($rec['hide'] == 2) : ?>
                                                                            <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                        <?php endif ?>
                                                                        <p><?= $rec['nickname'] ?></p>
                                                                    </a>
                                                                </object>
                                                            <?php } ?>
                                                            </div>
                                                            <?php if (($rec['ng_word'] == 1)) { ?>
                                                        </div>
                                                        <p class="message ng inside">不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                        <time class="time detail" datetime="<?= date("Y.m.d H:i", strtotime($rec['created_at'])) ?>"><?= detail_time($rec['created_at']) ?></time>
                                                    <?php } else { ?>
                                                        <p class="title">
                                                            <?php if ($rec['cur_age'] == 200) { ?>
                                                                <span class="cur-age">非公表</span>
                                                            <?php } else { ?>
                                                                <span class="cur-age"><?= $rec['cur_age'] ?>歳</span>
                                                            <?php } ?>
                                                            <span class="send"></span>
                                                            <span class="pos-age"><?= $rec['pos_age'] ?>歳の自分へ</span>
                                                        </p>
                                                    </div>
                                                    <p class="message inside">
                                                        <?= nl2br($rec['message']); ?>
                                                        <?php if ($rec['edit'] == 1) : ?>
                                                            <span class="edited">（編集済み）</span>
                                                        <?php endif ?>
                                                    </p>
                                                    <object>
                                                        <a class="category" href="./index.php?cur=&pos=&user=&order=&category=<?= $rec['category'] ?>&sort=1">
                                                            <span><?= $category_list[$rec['category']] ?></span>
                                                            <div class="rbr"></div>
                                                            <div class="rbl"></div>
                                                            <div class="rbb"></div>
                                                            <div class="rbt"></div>
                                                        </a>
                                                    </object>
                                                    <time class="time detail" datetime="<?= date("Y.m.d H:i", strtotime($rec['created_at'])) ?>"><?= detail_time($rec['created_at']) ?></time>
                                                <?php } ?>
                                                <ul class="reaction">
                                                    <li class="comment-btn">
                                                        <div class="heart-bg"></div>
                                                        <div class="comment"></div>
                                                        <span><?= number_unit($rec['comment']) ?></span>
                                                    </li>
                                                    <?php if (in_array($rec['id'], $good_list)) { ?>
                                                        <li class="letter-dl-good-btn" data-id="<?= $rec['id'] ?>">
                                                            <div class="heart-bg"></div>
                                                            <div class="heart-after"></div>
                                                            <div class="good-count">
                                                                <span><?= number_unit($rec['good'] - 1) ?></span>
                                                                <span><?= number_unit($rec['good']) ?></span>
                                                            </div>
                                                        </li>
                                                        <li class="good-user-btn">件のいいね</li>
                                                    <?php } else { ?>
                                                        <li class="letter-good-btn" data-id="<?= $rec['id'] ?>">
                                                            <div class="heart-bg"></div>
                                                            <div class="heart-before"></div>
                                                            <div class="good-count">
                                                                <span><?= number_unit($rec['good']) ?></span>
                                                                <span><?= number_unit($rec['good'] + 1) ?></span>
                                                            </div>
                                                        </li>
                                                        <?php if (count($good_user_list) > 0) : ?>
                                                            <li class="good-user-btn">件のいいね</li>
                                                        <?php endif ?>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                </article>
                                <div class="br"></div>
                                <div class="bl"></div>
                                <div class="bb"></div>
                                <div class="bt"></div>
                        </div>
                    <?php } else { ?>
                        <div class="letter">
                            <div class="report-btn-bg" data-id="<?= $rec['id'] ?>">
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
                                <object class="icon"><a href="./userpage_top.php?id=<?= $rec['user_id'] ?>"><img src="./icon/<?= $rec['icon'] ?>" alt="<?= $rec['nickname'] ?>のアイコン"></a></object>
                                <div class="contents">
                                    <div class="info">
                                        <div class="sp-block">
                                            <object class="writer">
                                                <a href="./userpage_top.php?id=<?= $rec['user_id'] ?>">
                                                    <?php if ($rec['hide'] == 2) : ?>
                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                    <?php endif ?>
                                                    <p><?= $rec['nickname'] ?></p>
                                                </a>
                                            </object>
                                        </div>
                                        <?php if (($rec['ng_word'] == 1)) { ?>
                                    </div>
                                    <p class="message ng inside">不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                    <time class="time detail" datetime="<?= date("Y.m.d H:i", strtotime($rec['created_at'])) ?>"><?= detail_time($rec['created_at']) ?></time>
                                <?php } else { ?>
                                    <p class="title">
                                        <?php if ($rec['cur_age'] == 200) { ?>
                                            <span class="cur-age">非公表</span>
                                        <?php } else { ?>
                                            <span class="cur-age"><?= $rec['cur_age'] ?>歳</span>
                                        <?php } ?>
                                        <span class="send"></span>
                                        <span class="pos-age"><?= $rec['pos_age'] ?>歳の自分へ</span>
                                    </p>
                                </div>
                                <p class="message inside">
                                    <?= nl2br($rec['message']); ?>
                                    <?php if ($rec['edit'] == 1) : ?>
                                        <span class="edited">（編集済み）</span>
                                    <?php endif ?>
                                </p>
                                <object>
                                    <a class="category" href="./index.php?cur=&pos=&user=&order=&category=<?= $rec['category'] ?>&sort=1">
                                        <span><?= $category_list[$rec['category']] ?></span>
                                        <div class="rbr"></div>
                                        <div class="rbl"></div>
                                        <div class="rbb"></div>
                                        <div class="rbt"></div>
                                    </a>
                                </object>
                                <time class="time detail" datetime="<?= date("Y.m.d H:i", strtotime($rec['created_at'])) ?>"><?= detail_time($rec['created_at']) ?></time>
                            <?php } ?>
                            <ul class="reaction">
                                <li class="comment-btn">
                                    <div class="heart-bg"></div>
                                    <div class="comment"></div>
                                    <span><?= number_unit($rec['comment']) ?></span>
                                </li>
                                <li class="letter-good-btn">
                                    <div class="heart-bg"></div>
                                    <div class="heart-before"></div>
                                    <div class="good-count">
                                        <span><?= number_unit($rec['good']) ?></span>
                                        <span><?= number_unit($rec['good'] + 1) ?></span>
                                    </div>
                                </li>
                                <?php if (count($good_user_list) > 0) : ?>
                                    <li class="good-user-btn">件のいいね</li>
                                <?php endif ?>
                            </ul>
                        </div>
                        </article>
                        <div class="br"></div>
                        <div class="bl"></div>
                        <div class="bb"></div>
                        <div class="bt"></div>
                </div>
            <?php } ?>
            <div class="side-bar">
                <?php if (isset($_COOKIE['paspost_id'])) { ?>
                    <div class="comment-area">
                        <div class="comment-title">
                            <h3>コメント</h3>
                            <?php if ($rec['comment'] != '0') : ?>
                                <p><?= $rec['comment'] ?>件</p>
                            <?php endif ?>
                        </div>
                        <?php if (isset($comment_list)) { ?>
                            <ul id="comment-list">
                                <?php if (count($comment_list) > 3) { ?>
                                    <?php for ($i = 0; $i < 3; $i++) : ?>
                                        <?php if ($comment_list[$i]['is_delete'] == 1) { ?>
                                            <li class="delete-comment-item">
                                                <p class="icon"></p>
                                                <div class="comment-box">
                                                    <div class="comment-balloon">
                                                        <p>投函者がコメントを削除しました。</p>
                                                        <time datetime="<?= date("Y.m.d H:i", strtotime($comment_list[$i]['created_at'])) ?>"><?= letter_time($comment_list[$i]['created_at']) ?></time>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } else { ?>
                                            <?php if ($comment_list[$i]['user_id'] == $id) { ?>
                                                <li class="comment-item" data-id="">
                                                    <p class="icon"><a href="./mypage_top.php"><img src="./icon/<?= $comment_list[$i]['icon'] ?>" alt="<?= $comment_list[$i]['nickname'] ?>のアイコン"></a></p>
                                                    <div class="comment-box">
                                                        <div class="comment-name">
                                                            <a href="./mypage_top.php">
                                                                <?php if ($comment_list[$i]['hide'] == 2) : ?>
                                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                <?php endif ?>
                                                                <span class="nickname"><?= $comment_list[$i]['nickname'] ?></span>
                                                            </a>
                                                        </div>
                                                        <div class="comment-balloon">
                                                            <?php if ($rec['user_id'] == $id) : ?>
                                                                <button class="confirm-btn" data-id="<?= $comment_list[$i]['id'] ?>"><img src="./img/garbage.svg" alt="ゴミ箱マーク"></button>
                                                            <?php endif ?>
                                                            <?php if (($comment_list[$i]['ng_word'] == 1)) { ?>
                                                                <p>不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                            <?php } else { ?>
                                                                <p><?= $comment_list[$i]['comment'] ?></p>
                                                            <?php } ?>
                                                            <time datetime="<?= date("Y.m.d H:i", strtotime($comment_list[$i]['created_at'])) ?>"><?= letter_time($comment_list[$i]['created_at']) ?></time>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } else { ?>
                                                <li class="comment-item" data-id="">
                                                    <p class="icon"><a href="./userpage_top.php?id=<?= $comment_list[$i]['user_id'] ?>"><img src="./icon/<?= $comment_list[$i]['icon'] ?>" alt="<?= $comment_list[$i]['nickname'] ?>のアイコン"></a></p>
                                                    <div class="comment-box">
                                                        <div class="comment-name">
                                                            <a href="./userpage_top.php?id=<?= $comment_list[$i]['user_id'] ?>">
                                                                <?php if ($comment_list[$i]['hide'] == 2) : ?>
                                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                <?php endif ?>
                                                                <span class="nickname"><?= $comment_list[$i]['nickname'] ?></span>
                                                            </a>
                                                        </div>
                                                        <div class="comment-balloon">
                                                            <?php if ($rec['user_id'] == $id) : ?>
                                                                <button class="confirm-btn" data-id="<?= $comment_list[$i]['id'] ?>"><img src="./img/garbage.svg" alt="ゴミ箱マーク"></button>
                                                            <?php endif ?>
                                                            <?php if (($comment_list[$i]['ng_word'] == 1)) { ?>
                                                                <p>不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                            <?php } else { ?>
                                                                <p><?= $comment_list[$i]['comment'] ?></p>
                                                            <?php } ?>
                                                            <time datetime="<?= date("Y.m.d H:i", strtotime($comment_list[$i]['created_at'])) ?>"><?= letter_time($comment_list[$i]['created_at']) ?></time>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php endfor ?>
                                    <li class="more-comment">コメントをもっと見る</li>
                                    <?php for ($i = 3; $i < count($comment_list); $i++) : ?>
                                        <?php if ($comment_list[$i]['is_delete'] == 1) { ?>
                                            <li class="delete-comment-item more">
                                                <p class="icon"></p>
                                                <div class="comment-box">
                                                    <div class="comment-balloon">
                                                        <p>投函者がコメントを削除しました。</p>
                                                        <time datetime="<?= date("Y.m.d H:i", strtotime($comment_list[$i]['created_at'])) ?>"><?= letter_time($comment_list[$i]['created_at']) ?></time>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } else { ?>
                                            <?php if ($comment_list[$i]['user_id'] == $id) { ?>
                                                <li class="comment-item more" data-id="">
                                                    <p class="icon"><a href="./mypage_top.php"><img src="./icon/<?= $comment_list[$i]['icon'] ?>" alt="<?= $comment_list[$i]['nickname'] ?>のアイコン"></a></p>
                                                    <div class="comment-box">
                                                        <div class="comment-name">
                                                            <a href="./mypage_top.php">
                                                                <?php if ($comment_list[$i]['hide'] == 2) : ?>
                                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                <?php endif ?>
                                                                <span class="nickname"><?= $comment_list[$i]['nickname'] ?></span>
                                                            </a>
                                                        </div>
                                                        <div class="comment-balloon">
                                                            <?php if ($rec['user_id'] == $id) : ?>
                                                                <button class="confirm-btn" data-id="<?= $comment_list[$i]['id'] ?>"><img src="./img/garbage.svg" alt="ゴミ箱マーク"></button>
                                                            <?php endif ?>
                                                            <?php if (($comment_list[$i]['ng_word'] == 1)) { ?>
                                                                <p>不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                            <?php } else { ?>
                                                                <p><?= $comment_list[$i]['comment'] ?></p>
                                                            <?php } ?>
                                                            <time datetime="<?= date("Y.m.d H:i", strtotime($comment_list[$i]['created_at'])) ?>"><?= letter_time($comment_list[$i]['created_at']) ?></time>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } else { ?>
                                                <li class="comment-item more" data-id="">
                                                    <p class="icon"><a href="./userpage_top.php?id=<?= $comment_list[$i]['user_id'] ?>"><img src="./icon/<?= $comment_list[$i]['icon'] ?>" alt="<?= $comment_list[$i]['nickname'] ?>のアイコン"></a></p>
                                                    <div class="comment-box">
                                                        <div class="comment-name">
                                                            <a href="./userpage_top.php?id=<?= $comment_list[$i]['user_id'] ?>">
                                                                <?php if ($comment_list[$i]['hide'] == 2) : ?>
                                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                <?php endif ?>
                                                                <span class="nickname"><?= $comment_list[$i]['nickname'] ?></span>
                                                            </a>
                                                        </div>
                                                        <div class="comment-balloon">
                                                            <?php if ($rec['user_id'] == $id) : ?>
                                                                <button class="confirm-btn" data-id="<?= $comment_list[$i]['id'] ?>"><img src="./img/garbage.svg" alt="ゴミ箱マーク"></button>
                                                            <?php endif ?>
                                                            <?php if (($comment_list[$i]['ng_word'] == 1)) { ?>
                                                                <p>不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                            <?php } else { ?>
                                                                <p><?= $comment_list[$i]['comment'] ?></p>
                                                            <?php } ?>
                                                            <time datetime="<?= date("Y.m.d H:i", strtotime($comment_list[$i]['created_at'])) ?>"><?= letter_time($comment_list[$i]['created_at']) ?></time>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php endfor ?>
                                <?php } else { ?>
                                    <?php foreach ($comment_list as $value) : ?>
                                        <?php if ($value['is_delete'] == 1) { ?>
                                            <li class="delete-comment-item">
                                                <p class="icon"></p>
                                                <div class="comment-box">
                                                    <div class="comment-balloon">
                                                        <p>投函者がコメントを削除しました。</p>
                                                        <time datetime="<?= date("Y.m.d H:i", strtotime($value['created_at'])) ?>"><?= letter_time($value['created_at']) ?></time>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } else { ?>
                                            <?php if ($value['user_id'] == $id) { ?>
                                                <li class="comment-item" data-id="">
                                                    <p class="icon"><a href="./mypage_top.php"><img src="./icon/<?= $value['icon'] ?>" alt="<?= $value['nickname'] ?>のアイコン"></a></p>
                                                    <div class="comment-box">
                                                        <div class="comment-name">
                                                            <a href="./mypage_top.php">
                                                                <?php if ($value['hide'] == 2) : ?>
                                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                <?php endif ?>
                                                                <span class="nickname"><?= $value['nickname'] ?></span>
                                                            </a>
                                                        </div>
                                                        <div class="comment-balloon">
                                                            <?php if ($rec['user_id'] == $id) : ?>
                                                                <button class="confirm-btn" data-id="<?= $value['id'] ?>"><img src="./img/garbage.svg" alt="ゴミ箱マーク"></button>
                                                            <?php endif ?>
                                                            <?php if (($value['ng_word'] == 1)) { ?>
                                                                <p>不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                            <?php } else { ?>
                                                                <p><?= $value['comment'] ?></p>
                                                            <?php } ?>
                                                            <time datetime="<?= date("Y.m.d H:i", strtotime($value['created_at'])) ?>"><?= letter_time($value['created_at']) ?></time>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } else { ?>
                                                <li class="comment-item" data-id="">
                                                    <p class="icon"><a href="./userpage_top.php?id=<?= $value['user_id'] ?>"><img src="./icon/<?= $value['icon'] ?>" alt="<?= $value['nickname'] ?>のアイコン"></a></p>
                                                    <div class="comment-box">
                                                        <div class="comment-name">
                                                            <a href="./userpage_top.php?id=<?= $value['user_id'] ?>">
                                                                <?php if ($value['hide'] == 2) : ?>
                                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                                <?php endif ?>
                                                                <span class="nickname"><?= $value['nickname'] ?></span>
                                                            </a>
                                                        </div>
                                                        <div class="comment-balloon">
                                                            <?php if ($rec['user_id'] == $id) : ?>
                                                                <button class="confirm-btn" data-id="<?= $value['id'] ?>"><img src="./img/garbage.svg" alt="ゴミ箱マーク"></button>
                                                            <?php endif ?>
                                                            <?php if (($value['ng_word'] == 1)) { ?>
                                                                <p>不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                            <?php } else { ?>
                                                                <p><?= $value['comment'] ?></p>
                                                            <?php } ?>
                                                            <time datetime="<?= date("Y.m.d H:i", strtotime($value['created_at'])) ?>"><?= letter_time($value['created_at']) ?></time>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php endforeach ?>
                                <?php } ?>
                            </ul>
                        <?php } else { ?>
                            <p class="no-comment">コメントはありません</p>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="comment-area">
                        <div class="comment-title">
                            <h3>コメント</h3>
                            <?php if ($rec['comment'] != '0') : ?>
                                <p><?= $rec['comment'] ?>件</p>
                            <?php endif ?>
                        </div>
                        <?php if (isset($comment_list)) { ?>
                            <ul id="comment-list">
                                <?php if (count($comment_list) > 3) { ?>
                                    <?php for ($i = 0; $i < 3; $i++) : ?>
                                        <?php if ($comment_list[$i]['is_delete'] == 0) { ?>
                                            <li class="comment-item" data-id="">
                                                <p class="icon"><a href="./userpage_top.php?id=<?= $comment_list[$i]['user_id'] ?>"><img src="./icon/<?= $comment_list[$i]['icon'] ?>" alt="<?= $comment_list[$i]['nickname'] ?>のアイコン"></a></p>
                                                <div class="comment-box">
                                                    <div class="comment-name">
                                                        <a href="./userpage_top.php?id=<?= $comment_list[$i]['user_id'] ?>">
                                                            <?php if ($comment_list[$i]['hide'] == 2) : ?>
                                                                <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                            <?php endif ?>
                                                            <span class="nickname"><?= $comment_list[$i]['nickname'] ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="comment-balloon">
                                                        <?php if (($comment_list[$i]['ng_word'] == 1)) { ?>
                                                            <p>不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                        <?php } else { ?>
                                                            <p><?= $comment_list[$i]['comment'] ?></p>
                                                        <?php } ?>
                                                        <time datetime="<?= date("Y.m.d H:i", strtotime($comment_list[$i]['created_at'])) ?>"><?= letter_time($comment_list[$i]['created_at']) ?></time>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } else { ?>
                                            <li class="delete-comment-item">
                                                <p class="icon"></p>
                                                <div class="comment-box">
                                                    <div class="comment-balloon">
                                                        <p>投函者がコメントを削除しました。</p>
                                                        <time datetime="<?= date("Y.m.d H:i", strtotime($comment_list[$i]['created_at'])) ?>"><?= letter_time($comment_list[$i]['created_at']) ?></time>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    <?php endfor ?>
                                    <li class="more-comment">コメントをもっと見る</li>
                                    <?php for ($i = 3; $i < count($comment_list); $i++) : ?>
                                        <?php if ($comment_list[$i]['is_delete'] == 0) { ?>
                                            <li class="comment-item more" data-id="">
                                                <p class="icon"><a href="./userpage_top.php?id=<?= $comment_list[$i]['user_id'] ?>"><img src="./icon/<?= $comment_list[$i]['icon'] ?>" alt="<?= $comment_list[$i]['nickname'] ?>のアイコン"></a></p>
                                                <div class="comment-box">
                                                    <div class="comment-name">
                                                        <a href="./userpage_top.php?id=<?= $comment_list[$i]['user_id'] ?>">
                                                            <?php if ($comment_list[$i]['hide'] == 2) : ?>
                                                                <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                            <?php endif ?>
                                                            <span class="nickname"><?= $comment_list[$i]['nickname'] ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="comment-balloon">
                                                        <?php if (($comment_list[$i]['ng_word'] == 1)) { ?>
                                                            <p>不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                        <?php } else { ?>
                                                            <p><?= $comment_list[$i]['comment'] ?></p>
                                                        <?php } ?>
                                                        <time datetime="<?= date("Y.m.d H:i", strtotime($comment_list[$i]['created_at'])) ?>"><?= letter_time($comment_list[$i]['created_at']) ?></time>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } else { ?>
                                            <li class="delete-comment-item more">
                                                <p class="icon"></p>
                                                <div class="comment-box">
                                                    <div class="comment-balloon">
                                                        <p>投函者がコメントを削除しました。</p>
                                                        <time datetime="<?= date("Y.m.d H:i", strtotime($comment_list[$i]['created_at'])) ?>"><?= letter_time($comment_list[$i]['created_at']) ?></time>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    <?php endfor ?>
                                <?php } else { ?>
                                    <?php foreach ($comment_list as $value) : ?>
                                        <?php if ($value['is_delete'] == 0) { ?>
                                            <li class="comment-item" data-id="">
                                                <p class="icon"><a href="./userpage_top.php?id=<?= $value['user_id'] ?>"><img src="./icon/<?= $value['icon'] ?>" alt="<?= $value['nickname'] ?>のアイコン"></a></p>
                                                <div class="comment-box">
                                                    <div class="comment-name">
                                                        <a href="./userpage_top.php?id=<?= $value['user_id'] ?>">
                                                            <?php if ($value['hide'] == 2) : ?>
                                                                <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                            <?php endif ?>
                                                            <span class="nickname"><?= $value['nickname'] ?></span>
                                                        </a>
                                                    </div>
                                                    <div class="comment-balloon">
                                                        <?php if (($value['ng_word'] == 1)) { ?>
                                                            <p>不適切なコンテンツが含まれている可能性があるため表示されません</p>
                                                        <?php } else { ?>
                                                            <p><?= $value['comment'] ?></p>
                                                        <?php } ?>
                                                        <time datetime="<?= date("Y.m.d H:i", strtotime($value['created_at'])) ?>"><?= letter_time($value['created_at']) ?></time>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } else { ?>
                                            <li class="delete-comment-item">
                                                <p class="icon"></p>
                                                <div class="comment-box">
                                                    <div class="comment-balloon">
                                                        <p>投函者がコメントを削除しました。</p>
                                                        <time datetime="<?= date("Y.m.d H:i", strtotime($value['created_at'])) ?>"><?= letter_time($value['created_at']) ?></time>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    <?php endforeach ?>
                                <?php } ?>
                            </ul>
                        <?php } else { ?>
                            <p class="no-comment">コメントはありません</p>
                        <?php } ?>
                    </div>
                <?php } ?>
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
    <?php } else { ?>
        <div class="caution">
            <div class="logo"><img src="./img/logo-glee.svg" alt="パスポストのアイコン"></div>
            <p>すでに削除された手紙です</p>
            <div class="home-btn"><a class="home-btn-inner" href="./index.php">ホームに戻る</a></div>
        </div>
    <?php } ?>
    </div>
    <?php if (isset($_COOKIE['paspost_id'])) { ?>
        <!-- ０ -->
        <?php include_once('./views/user-window.php') ?>
        <!-- １ -->
        <?php include_once('./views/report-modal.html') ?>
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
        <!-- ５ -->
        <div class="modal-bg close">
            <div class="modal-confirm modal">
                <p class="header">コメント削除</p>
                <p>本当にこのコメントを削除してもよろしいですか？</p>
                <div>
                    <button class="modal-cancel">キャンセル</button>
                    <button class="comment-delete-btn" data-id="">削除する</button>
                </div>
            </div>
        </div>
        <!-- ６ -->
        <div class="sp-report-bg modal-bg close">
            <div class="sp-modal">
                <ul class="sp-report-list">
                    <li> <button class="report-modal-btn" data-id="<?= $value['id'] ?>">手紙を通報する</button></li>
                    <li><button class="modal-cancel">キャンセル</button></li>
                </ul>
            </div>
        </div>
        <!-- ７ -->
        <div class="sp-edit-bg modal-bg close">
            <div class="sp-modal">
                <ul class="sp-report-list">
                    <li><button data-id="<?= $rec['id'] ?>" class="edit-btn">手紙を編集する</button></li>
                    <li><button class="modal-cancel">キャンセル</button></li>
                </ul>
            </div>
        </div>
        <!-- ８いいね！したユーザー -->
        <div class="user-modal-bg close">
            <div class="good-user-modal"></div>
        </div>
    <?php } else { ?>
        <?php include_once('./views/login-modal.html') ?>
    <?php } ?>
    <?php if (isset($_COOKIE['ajax'])) : ?>
        <?php if ($_COOKIE['ajax'] == 'letter-edit') : ?>
            <div class="notice"><img src="./img/check-red.svg">手紙の内容を更新しました</div>
        <?php endif ?>
        <?php if ($_COOKIE['ajax'] == 'report-letter') : ?>
            <div class="notice"><img src="./img/check-red.svg">手紙を報告しました</div>
        <?php endif ?>
    <?php endif ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/function.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/input-focus.js"></script>
    <script src="./js/list-follow-btn.js"></script>
    <script>
        const comment = document.querySelector('#comment');
        const login = '<?= $login ?>';
        const button = document.querySelector('#comment-btn');

        $(document).on('click', '.more-comment', function() {
            $(this).css('display', 'none');
            $('.more').css('display', 'flex');
        })

        $(window).on('load', function() {
            let windowWidth = window.innerWidth;
            let widnowHeight = window.innerHeight;
            if (windowWidth > 768) {
                if ($('.letter').innerHeight() > widnowHeight - 150) {
                    $('.letter').css({
                        'position': 'relative',
                        'top': 0,
                    });
                } else {
                    $('.letter').css({
                        'position': 'sticky',
                        'position': '-webkit-sticky',
                        'top': '150px',
                    });
                }
            }
        })

        if (login == 1) {
            var userid = JSON.parse('<?= $id_json ?>');
            var letterid = JSON.parse('<?= $letter_id_json ?>');
            const userModalBg = document.getElementsByClassName('user-modal-bg');

            userModalBg[0].addEventListener('click', () => {
                if (!userModalBg[0].classList.contains('close')) {
                    userModalBg[0].classList.add('close');
                    $('body').css('overflow', 'visible');
                    document.removeEventListener('touchmove', disableScroll, {
                        passive: false
                    });
                }
            });

            $(document).on('click', '.good-user-btn', function() {
                $.ajax({
                    type: "GET",
                    url: "ajax-good-user-modal.php",
                    data: {
                        "letterid": letterid,
                        "userid": userid,
                    }
                }).done((data) => {
                    $('.good-user-modal').html(data);
                    $('.user-modal-bg').removeClass('close');
                    document.addEventListener('touchmove', disableScroll, {
                        passive: false
                    });
                    $('body').css('overflow', 'hidden');
                });
            })

            $('.good-user-modal').on('click', function(event) {
                const modalTitle = document.getElementById('modal-title');
                const modalInner = document.getElementsByClassName('modal-inner');
                const goodUser = document.getElementsByClassName('good-user');
                if (event.target == modalTitle || event.target == modalInner[0] || event.target == goodUser[0]) {
                    return false;
                }
            })

            inputlength(comment, '.comment-length', 500);

            button.addEventListener('click', (e) => {
                const alert_text = document.querySelectorAll('.alert-text');
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
                                location.reload();
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
                                location.reload();
                            });
                        }
                    });

                    return false;
                }
            })

            $('.confirm-btn').on('click', function() {
                const comment_id = $(this).attr('data-id');
                $('.comment-delete-btn').attr('data-id', comment_id);
                modalBg[5].classList.remove('close');
                $('body').css('overflow', 'hidden');
                document.removeEventListener('touchmove', disableScroll, {
                    passive: false
                });
            })

            $('.comment-delete-btn').on('click', function() {
                $('#confirm-modal-bg').addClass('close');
                $.ajax({
                    type: "POST",
                    url: "ajax_comment_delete.php",
                    datatype: "json",
                    data: {
                        "commentid": $(this).attr('data-id'),
                        "letterid": letterid,
                    }
                }).fail((data) => {
                    location.reload();
                });
                return false;
            });
        } else {
            $('.letter-good-btn,#comment-btn,.report-btn-bg,.good-user-btn').click(() => {
                event.preventDefault();
                modalBg[0].classList.remove('close');
            })

            comment.onfocus = () => {
                modalBg[0].classList.remove('close');
            };
        }
    </script>
    <script src="./js/index.js"></script>
</body>

</html>