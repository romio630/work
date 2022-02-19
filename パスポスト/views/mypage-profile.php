<div class="user-profile">
    <div class="user">
        <div class="user-info">
            <div class="top-icon"><img src="./icon/<?= $icon ?>" alt="<?= $nickname ?>のアイコン"></div>
            <div class="user-name">
                <div class="nickname" style="margin-bottom: 1.563vw;">
                    <?php if ($rec4['hide'] == 2) : ?>
                        <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                    <?php endif ?>
                    <span><?= $nickname ?></span>
                </div>
                <p class="good"><img src="./img/heart-after.svg" alt="いいね！マーク"><?= number_unit($total['good']) ?></p>
                <div class="sp-user-btn">
                    <a href="./configuration.php" class="configuration">
                        <div class="configuration-icon"><img src="./img/configuration.svg"></div>
                        <span>設定</span>
                    </a>
                    <div class="user-menu-btn"></div>
                </div>
            </div>
        </div>
        <div class="user-btn">
            <a href="./configuration.php" class="configuration">
                <div class="configuration-icon"><img src="./img/configuration.svg"></div>
                <span>設定</span>
            </a>
            <div class="user-menu-btn"></div>
        </div>
    </div>
    <ul>
        <li><a href="./mypage_top.php"><span><?= number_unit($rec1['lt_count']) ?></span>投函数</a></li>
        <li>
            <a href="mypage_follower.php"><span><?= number_unit($rec2['follower_count']) ?></span>フォロワー</a>
            <div class="bl"></div>
        </li>
        <li>
            <a href="mypage_following.php"><span><?= number_unit($rec3['following_count']) ?></span>フォロー中</a>
            <div class="bl"></div>
        </li>
    </ul>
    <?php if ($rec4['intro'] == null) { ?>
        <p class="intro">自己紹介文は登録されてません</p>
    <?php } else { ?>
        <p class="intro"><?= nl2br($rec4['intro']) ?></p>
    <?php } ?>
</div>