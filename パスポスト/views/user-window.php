<div id="user-window" class="close">
    <ul>
        <li>
            <a href="./mypage_top.php">
                <div class="btn-inner mypage">
                    <div class="icon"><img src="./icon/<?= $icon ?>" alt="<?= $nickname ?>のアイコン"></div>
                    <div class="user-info">
                        <p class="nickname"><?= $nickname ?></p>
                        <p><img src="./img/heart-after.svg" alt="いいね！マーク"><?= number_unit($total['good']) ?></p>
                    </div>
                    <div class="bb"></div>
                </div>
            </a>
        </li>
        <li>
            <a href="./configuration.php">
                <div class="btn-inner">
                    <p>設定</p>
                    <div class="bb"></div>
                </div>
            </a>
        </li>
        <li>
            <button class="logout">
                <div class="btn-inner">ログアウト</div>
            </button>
        </li>
    </ul>
</div>
<div class="modal-bg close">
    <div class="modal-logout modal">
        <p class="header">ログアウトしますか？</p>
        <div>
            <button class="modal-cancel">キャンセル</button>
            <a href="./logout.php" id="logout-btn">ログアウトする</a>
        </div>
    </div>
</div>