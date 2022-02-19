<header id="header">
    <?php
    $hostname = $_SERVER['HTTP_HOST'];
    if (!empty($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'], $hostname) !== false)) {
        echo '<button onclick="history.back()" class="sp-back-btn"></button>';
    }
    ?>
    <div class="sp-header">
        <button class="sp-search-close"></button>
        <form action="./user_search.php" method="get" id="sp-search-form">
            <div class="text-input">
                <input type="text" class="input" class="search" name="name" placeholder="ユーザーを探す">
                <button type="submit" class="submit-btn"></button>
            </div>
        </form>
        <div class="bb"></div>
    </div>
    <div class="sp-search-btn"></div>
    <div class="flex-header wrapper">
        <div class="flex-left">
            <h1 class="icon-paspost"><a href="index.php"><img src="./img/icon.svg" alt="パスポストのアイコン"></a></h1>
            <form action="./user_search.php" method="get" id="search-form">
                <div class="text-input">
                    <input type="text" class="input" class="search" name="name" placeholder="ユーザーを探す">
                    <button type="submit" class="submit-btn"></button>
                </div>
            </form>
        </div>
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