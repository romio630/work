<div class="user-profile">
    <div class="user">
        <div class="user-info">
            <div class="top-icon"><img src="./icon/<?= $rec4['icon'] ?>" alt="<?= $rec4['nickname'] ?>のアイコン"></div>
            <div class="user-name">
                <?php if ($rec5['id'] != 0) { ?>
                    <div class="nickname">
                        <?php if ($rec4['hide'] == 2) : ?>
                            <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                        <?php endif ?>
                        <span><?= $rec4['nickname'] ?></span>
                    </div>
                    <span class="my-follower">フォローされています</span>
                <?php } else { ?>
                    <div class="nickname" style="margin-bottom: 20px;">
                        <?php if ($rec4['hide'] == 2) : ?>
                            <div class="hide-key"><img src="./img/hide-key.svg" alt="鍵マーク"></div>
                        <?php endif ?>
                        <span><?= $rec4['nickname'] ?></span>
                    </div>
                <?php } ?>
                <p class="good"><img src="./img/heart-after.svg" alt="いいね！マーク"><?= number_unit($user_total['good']) ?></p>
                <div class="sp-user-btn">
                    <?php if ($rec4['hide'] == 2) { ?>
                        <?php if (isset($hide['status'])) { ?>
                            <?php if ($hide['status'] == 0) { ?>
                                <button class="unapproved-btn">
                                    <span class="btn-text">未承認</span>
                                    <span class="btn-text">キャンセル</span>
                                </button>
                            <?php } else { ?>
                                <button class="hide-unfollow-confirm">
                                    <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                    <span class="btn-text">フォロー解除</span>
                                </button>
                            <?php } ?>
                        <?php } else { ?>
                            <button class="follow-request-btn" data-name="<?= $rec4['nickname'] ?>">
                                <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                            </button>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if (in_array($user_id, $mutual)) { ?>
                            <button class="unfollow-confirm">
                                <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                                <span class="btn-text">フォロー解除</span>
                            </button>
                        <?php } else { ?>
                            <button class="follow-btn">
                                <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                            </button>
                        <?php } ?>
                    <?php } ?>
                    <div class="user-menu-btn"></div>
                    <button class="report-user-modal-btn">このユーザーを通報する</button>
                </div>
            </div>
        </div>
        <div class="user-btn">
            <?php if ($rec4['hide'] == 2) { ?>
                <?php if (isset($hide['status'])) { ?>
                    <?php if ($hide['status'] == 0) { ?>
                        <button class="unapproved-btn">
                            <span class="btn-text">未承認</span>
                            <span class="btn-text">キャンセル</span>
                        </button>
                    <?php } else { ?>
                        <button class="hide-unfollow-confirm">
                            <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                            <span class="btn-text">フォロー解除</span>
                        </button>
                    <?php } ?>
                <?php } else { ?>
                    <button class="follow-request-btn">
                        <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                    </button>
                <?php } ?>
            <?php } else { ?>
                <?php if (in_array($user_id, $mutual)) { ?>
                    <button class="unfollow-confirm">
                        <span class="btn-text"><img src="./img/check.svg">フォロー中</span>
                        <span class="btn-text">フォロー解除</span>
                    </button>
                <?php } else { ?>
                    <button class="follow-btn">
                        <span class="btn-text"><img src="./img/plus.svg">フォロー</span>
                    </button>
                <?php } ?>
            <?php } ?>
            <div class="user-menu-btn"></div>
            <button class="report-user-modal-btn">このユーザーを通報する</button>
        </div>
    </div>
    <ul>
        <li><a href="userpage_top.php?id=<?= $user_id ?>"><span><?= number_unit($rec1['lt_count']) ?></span>投函数</a></li>
        <li>
            <a href="userpage_follower.php?id=<?= $user_id ?>"><span><?= number_unit($rec2['follower_count']) ?></span>フォロワー</a>
            <div class="bl"></div>
        </li>
        <li>
            <a href="userpage_following.php?id=<?= $user_id ?>"><span><?= number_unit($rec3['following_count']) ?></span>フォロー中</a>
            <div class=" bl">
            </div>
        </li>
    </ul>
    <?php if ($rec4['intro'] == null) { ?>
        <p class="intro">自己紹介文は登録されてません</p>
    <?php } else { ?>
        <p class="intro"><?= nl2br($rec4['intro']) ?></p>
    <?php } ?>
</div>