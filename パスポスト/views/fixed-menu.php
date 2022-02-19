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