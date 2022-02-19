<header id="header">
    <?php
    if ($_SERVER['REQUEST_URI'] !== '/letter/index.php') {
        $hostname = $_SERVER['HTTP_HOST'];
        if (!empty($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'], $hostname) !== false)) {
            echo '<button onclick="history.back()" class="sp-back-btn"></button>';
        }
    }
    ?>
    <div class="flex-header wrapper">
        <h1 class="icon-paspost"><a href="index.php"><img src="./img/icon.svg" alt="パスポスト"></a></h1>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <nav>
                <ul>
                    <li class="notifications">
                        <a href="./notifications.php">お知らせ</a>
                        <?php if (isset($unread) && $unread['cnt'] != 0) { ?>
                            <span class="notifications-cnt"><?= $unread['cnt'] ?></span>
                        <?php } elseif (isset($unread) && $unread['cnt'] > 99) { ?>
                            <span class="notifications-cnt">99<sup>+</sup></span>
                        <?php } ?>
                    </li>
                    <li><button id="account">アカウント</button></li>
                    <li><a class="post-btn" href="posting_top.php">投函する</a></li>
                </ul>
            </nav>
        <?php } else { ?>
            <nav>
                <ul>
                    <li><a href="login.php">ログイン</a></li>
                    <li><a href="user_add.php">ユーザー登録</a></li>
                </ul>
            </nav>
        <?php } ?>
    </div>
    <div class="bb"></div>
</header>