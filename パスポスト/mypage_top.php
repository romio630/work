<?php
session_start();
session_regenerate_id(true);

if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/function.php');
    require_once('./common/dbconnect.php');
    require_once('./common/category.php');
    $login = 1;
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];
    $id_json = json_encode($id);

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

    $sql = "SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,L.edit,U.icon,U.hide 
    FROM letter as L join pp_user as U on L.user_id=U.id WHERE L.user_id=? ORDER BY created_at DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row[] = $rec;
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
    $data[] = $id;
    $data[] = $id;
    $data[] = $id;
    $data[] = $id;
    $stmt->execute($data);
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $g_row[] = $rec;
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
    <title><?= $nickname ?>の投稿した手紙 -パスポスト</title>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/userpage.css">
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div class="flex-userpage wrapper">
                <?php include_once('./views/mypage-profile.php') ?>
                <div id="letter-area">
                    <ul class="tab-list">
                        <li class="tab active" data-left="0">投函した手紙</li>
                        <li class="tab" data-left="50%">いいね！した手紙</li>
                        <div class="active-bar"></div>
                        <div class="bb"></div>
                    </ul>
                    <div id="letter-list" class="panel show">
                        <?php if (!isset($row)) { ?>
                            <p class="no-letter">投函されたお手紙はありません</p>
                        <?php } else { ?>
                            <?php foreach ($row as $value) : ?>
                                <a href="./letter.php?id=<?= $value['id'] ?>" class="letter hover">
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
                <div id="good-letter-list" class="panel">
                    <?php if (!isset($g_row)) { ?>
                        <p class="no-letter">いいね！された手紙はまだありません</p>
                    <?php } else { ?>
                        <?php foreach ($g_row as $value) : ?>
                            <a href="./letter.php?id=<?= $value['id'] ?>" class="letter hover">
                                <?php if ($value['user_id'] == $id) { ?>
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
                <?php include_once('./views/user-window.php') ?>
                <?php include_once('./views/report-modal.html') ?>
            </div>
            <div class="sp-modal-bg modal-bg close">
                <div class="sp-modal">
                    <ul class="sp-report-list">
                        <li><button class="logout">ログアウトする</button></li>
                        <li><button class="modal-cancel">キャンセル</button></li>
                    </ul>
                </div>
            </div>
            <div class="sp-edit-bg modal-bg close">
                <div class="sp-modal">
                    <ul class="sp-report-list">
                        <li><button data-id="<?= $value['id'] ?>" class="edit-btn">手紙を編集する</button></li>
                        <li><button class="modal-cancel">キャンセル</button></li>
                    </ul>
                </div>
            </div>
            <div class="modal-bg close icon-modal">
                <div class="modal-cancel"></div>
                <div class="icon" id="modal-icon"><img src="./icon/<?= $icon ?>" alt="<?= $nickname ?>のアイコン"></div>
            </div>
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
            </div>
        <?php } else { ?>
            <?php include_once('./views/notlogin.html') ?>
        <?php } ?>
        <?php if (isset($_COOKIE['ajax'])) : ?>
            <?php if ($_COOKIE['ajax'] == 'report-letter') : ?>
                <div class="notice"><img src="./img/check-red.svg">手紙を報告しました</div>
            <?php endif ?>
            <?php if ($_COOKIE['ajax'] == 'letter-delete') : ?>
                <div class="notice"><img src="./img/check-red.svg">手紙を削除しました</div>
            <?php endif ?>
        <?php endif ?>
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
                            <div class="sp-menu-icon"><img src="./img/post.svg"></div>
                            <p>投函</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="./mypage_top.php">
                        <div class="btn-inner">
                            <div class="sp-menu-icon"><img src="./img/mypage-f.svg"></div>
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
    <script src="./js/intro.js"></script>
    <script src="./js/function.js"></script>
    <script>
        const userid = JSON.parse('<?= $id_json ?>');
        const login = '<?= $login ?>';
        const comment = document.querySelector('#comment');
        if (window.innerWidth > 768) {
            $('.user-menu-btn').css('display', 'none');
        }
        $('.top-icon').on('click', function() {
            modalBg[4].classList.remove('close');
            document.addEventListener('touchmove', disableScroll, {
                passive: false
            });
            $('body').css('overflow', 'hidden');
        })

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
        $('#modal-icon').on('click', function() {
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
    </script>
    <script src="./js/index.js"></script>
</body>

</html>