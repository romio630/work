<?php
require_once('./common/dbconnect.php');
$letter_id = $_GET['letterid'];
$id = $_GET['userid'];

$sql = 'SELECT u.id FROM follow as f JOIN pp_user as u ON f.following_id=u.id WHERE follower_id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
while ($rec5 = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $mutual[] = $rec5['id'];
}
if (!isset($mutual)) {
    $mutual = [];
}

$sql = "SELECT U.id,U.nickname,U.icon,U.intro,U.hide FROM pp_user as U join good_list as G on U.id=G.giver_id WHERE G.letter_id=? ORDER BY G.created_at DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($letter_id));
while ($good_user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $good_user_list[] = $good_user;
}
if (!isset($good_user_list)) {
    $good_user_list = null;
}

if ($good_user_list != null) {
    $is_follower = [];
    for ($i = 0; $i < count($good_user_list); $i++) {
        $sql = "SELECT id FROM follow WHERE follower_id=? and following_id=?";
        $stmt = $dbh->prepare($sql);
        $data = [];
        $data[] = $good_user_list[$i]['id'];
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
?>
<div class="modal-inner">
    <div class="modal-close"></div>
    <div id="modal-title">
        <div class="modal-cancel"></div>
        <h3 class="good-user">いいね！したユーザー</h3>
    </div>
    <ul class="good-user-list">
        <?php for ($i = 0; $i < count($good_user_list); $i++) : ?>
            <li>
                <?php if ($good_user_list[$i]['id'] == $id) { ?>
                    <a href="./mypage_top.php">
                        <div class="flex-item">
                            <div class="icon"><img src="./icon/<?= $good_user_list[$i]['icon'] ?>" alt="<?= $good_user_list[$i]['nickname'] ?>のアイコン"></div>
                            <?php if ($good_user_list[$i]['intro'] == "") { ?>
                                <div class="user-info">
                                    <div class="user-info-top">
                                        <div class="user-name">
                                            <div class="user-name-inner">
                                                <?php if ($good_user_list[$i]['hide'] == 2) : ?>
                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                <?php endif ?>
                                                <span class="nickname"><?= $good_user_list[$i]['nickname'] ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="user-info">
                                    <div class="user-info-top" style="margin-bottom: 8px;">
                                        <div class="user-name">
                                            <div class="user-name-inner">
                                                <?php if ($good_user_list[$i]['hide'] == 2) : ?>
                                                    <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                <?php endif ?>
                                                <span class="nickname"><?= $good_user_list[$i]['nickname'] ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="list-intro"><?= nl2br($good_user_list[$i]['intro']) ?></p>
                                </div>
                            <?php } ?>
                        </div>
                    </a>
                <?php } else { ?>
                    <?php if (in_array($good_user_list[$i]['id'], $mutual, true)) { ?>
                        <a href="./userpage_top.php?id=<?= $good_user_list[$i]['id'] ?>">
                            <div class="flex-item">
                                <div class="icon"><img src="./icon/<?= $good_user_list[$i]['icon'] ?>" alt="<?= $good_user_list[$i]['nickname'] ?>のアイコン"></div>
                                <?php if ($good_user_list[$i]['intro'] == "") { ?>
                                    <div class="user-info">
                                        <div class="user-info-top">
                                            <div class="user-name">
                                                <div class="user-name-inner">
                                                    <?php if ($good_user_list[$i]['hide'] == 2) : ?>
                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                    <?php endif ?>
                                                    <span class="nickname"><?= $good_user_list[$i]['nickname'] ?></span>
                                                </div>
                                                <?php if ($is_follower[$i] == 1) : ?>
                                                    <span class="my-follower">フォローされています</span>
                                                <?php endif ?>
                                            </div>
                                            <?php if ($good_user_list[$i]['hide'] == 2) { ?>
                                                <button class="list-hide-unfollow-confirm" data-id="<?= $good_user_list[$i]['id'] ?>" data-name="<?= $good_user_list[$i]['nickname'] ?>">
                                                    <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                    <span class="btn-text">フォロー解除</span>
                                                </button>
                                            <?php } else { ?>
                                                <button class="list-unfollow-confirm" data-id="<?= $good_user_list[$i]['id'] ?>" data-name="<?= $good_user_list[$i]['nickname'] ?>">
                                                    <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                    <span class="btn-text">フォロー解除</span>
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="user-info">
                                        <div class="user-info-top" style="margin-bottom: 8px;">
                                            <div class="user-name">
                                                <div class="user-name-inner">
                                                    <?php if ($good_user_list[$i]['hide'] == 2) : ?>
                                                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                    <?php endif ?>
                                                    <span class="nickname"><?= $good_user_list[$i]['nickname'] ?></span>
                                                </div>
                                                <?php if ($is_follower[$i] == 1) : ?>
                                                    <span class="my-follower">フォローされています</span>
                                                <?php endif ?>
                                            </div>
                                            <?php if ($good_user_list[$i]['hide'] == 2) { ?>
                                                <button class="list-hide-unfollow-confirm" data-id="<?= $good_user_list[$i]['id'] ?>" data-name="<?= $good_user_list[$i]['nickname'] ?>">
                                                    <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                    <span class="btn-text">フォロー解除</span>
                                                </button>
                                            <?php } else { ?>
                                                <button class="list-unfollow-confirm" data-id="<?= $good_user_list[$i]['id'] ?>" data-name="<?= $good_user_list[$i]['nickname'] ?>">
                                                    <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                                    <span class="btn-text">フォロー解除</span>
                                                </button>
                                            <?php } ?>
                                        </div>
                                        <p class="list-intro"><?= nl2br($good_user_list[$i]['intro']) ?></p>
                                    </div>
                                <?php } ?>
                            </div>
                        </a>
                    <?php } else { ?>
                        <?php if ($good_user_list[$i]['hide'] == 1) : ?>
                            <a href="./userpage_top.php?id=<?= $good_user_list[$i]['id'] ?>">
                                <div class="flex-item">
                                    <div class="icon"><img src="./icon/<?= $good_user_list[$i]['icon'] ?>" alt="<?= $good_user_list[$i]['nickname'] ?>のアイコン"></div>
                                    <?php if ($good_user_list[$i]['intro'] == "") { ?>
                                        <div class="user-info">
                                            <div class="user-info-top">
                                                <div class="user-name">
                                                    <div class="user-name-inner">
                                                        <?php if ($good_user_list[$i]['hide'] == 2) : ?>
                                                            <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                        <?php endif ?>
                                                        <span class="nickname"><?= $good_user_list[$i]['nickname'] ?></span>
                                                    </div>
                                                </div>
                                                <button class="list-follow-btn" data-id="<?= $good_user_list[$i]['id'] ?>" data-name="<?= $good_user_list[$i]['nickname'] ?>">
                                                    <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                </button>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="user-info">
                                            <div class="user-info-top" style="margin-bottom: 8px;">
                                                <div class="user-name">
                                                    <div class="user-name-inner">
                                                        <?php if ($good_user_list[$i]['hide'] == 2) : ?>
                                                            <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                                                        <?php endif ?>
                                                        <span class="nickname"><?= $good_user_list[$i]['nickname'] ?></span>
                                                    </div>
                                                </div>
                                                <button class="list-follow-btn" data-id="<?= $good_user_list[$i]['id'] ?>" data-name="<?= $good_user_list[$i]['nickname'] ?>">
                                                    <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                                                </button>
                                            </div>
                                            <p class="list-intro"><?= nl2br($good_user_list[$i]['intro']) ?></p>
                                        </div>
                                    <?php } ?>
                                </div>
                            </a>
                        <?php endif ?>
                    <?php } ?>
                <?php } ?>
            </li>
        <?php endfor ?>
    </ul>
</div>