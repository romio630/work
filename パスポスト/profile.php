<?php
session_start();
session_regenerate_id(true);

if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/sanitize.php');
    require_once('./common/dbconnect.php');
    require_once('./common/function.php');
    if (isset($_REQUEST['n'])) {
        $dec_nickname = openssl_decrypt($_REQUEST['n'], 'aes-256-ecb', 'hogehoge');
    }
    if (isset($_REQUEST['i'])) {
        $dec_intro = openssl_decrypt($_REQUEST['i'], 'aes-256-ecb', 'hogehoge');
    }

    if (isset($_REQUEST['e'])) {
        switch ($_REQUEST['e']) {
            case 1:
                $err_icon = "画像のサイズが大きすぎます";
                break;
            case 2:
                $err_icon = "画像のサイズが大きすぎます";
                $err_nickname = "不適切な言葉が含まれている可能性があります";
                break;
            case 3:
                $err_icon = "画像のサイズが大きすぎます";
                $err_intro = "不適切な言葉が含まれている可能性があります";
                break;
            case 4:
                $err_icon = "画像のサイズが大きすぎます";
                $err_nickname = "不適切な言葉が含まれている可能性があります";
                $err_intro = "不適切な言葉が含まれている可能性があります";
                break;
            case 5:
                $err_nickname = "不適切な言葉が含まれている可能性があります";
                break;
            case 6:
                $err_intro = "不適切な言葉が含まれている可能性があります";
                break;
            case 7:
                $err_nickname = "不適切な言葉が含まれている可能性があります";
                $err_intro = "不適切な言葉が含まれている可能性があります";
                break;
        }
    }
    $login = 1;
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];

    require_once('./common/sql.php');

    $sql = 'SELECT intro,hide FROM pp_user WHERE id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = null;
    $dbh = null;

    $name_length = mb_strlen($nickname);
    $intro_length = mb_strlen($rec['intro']);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/form.css">
    <title>プロフィール設定 -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div id="profile" class="form-wrapper">
                <h2>プロフィール設定</h2>
                <form action="./profile_check.php" method="post" enctype="multipart/form-data" class="form-contents" onsubmit="return profileCheck()">
                    <dl>
                        <dt>画像</dt>
                        <dd>
                            <div id="flex-file">
                                <div><img src="./icon/<?= $icon ?>" class="cur-icon"></div>
                                <label>
                                    <span class="filelabel">画像を選択</span>
                                    <input type="file" name="icon" id="file-input" tabindex="-1" accept="image/*">
                                </label>
                            </div>
                            <p class="limit">※1MB以下の画像</p>
                            <?php if (isset($err_icon)) : ?>
                                <p class="err-text"><?= $err_icon ?></p>
                            <?php endif ?>
                        </dd>
                        <dt>公開設定</dt>
                        <dd>
                            <?php if ($rec['hide'] == 2) { ?>
                                <input type="checkbox" id="hide-status" name="hide-status" value="2" checked>
                                <label for="hide-status">非公開にする</label>
                            <?php } else { ?>
                                <input type="checkbox" id="hide-status" name="hide-status" value="2">
                                <label for="hide-status">非公開にする</label>
                            <?php } ?>
                            <p class="limit">※オンにすると、手紙と他のアカウント情報があなたをフォローしているアカウントにのみ表示されます。</p>
                        </dd>
                        <dt><label for="nickname">ユーザー名</label></dt>
                        <dd>
                            <div class="text-input">
                                <?php if (isset($dec_nickname)) { ?>
                                    <input type="text" class="input ng-name" id="nickname" name="nickname" value="<?= $dec_nickname ?>">
                                <?php } else { ?>
                                    <input type="text" class="input" id="nickname" name="nickname" value="<?= $nickname ?>">
                                <?php } ?>
                                <i class="fas fa-times-circle none"></i>
                            </div>
                            <div class="input-assistance">
                                <p class="alert-text">
                                    <?php if (isset($err_nickname)) : ?>
                                        <?= $err_nickname ?>
                                    <?php endif ?>
                                </p>
                                <span class="name-length"><?= $name_length ?>/15</span>
                            </div>
                        </dd>
                        <dt><label for="intro">自己紹介文</label></dt>
                        <dd>
                            <?php if (isset($dec_intro)) { ?>
                                <textarea name="intro" class="ng-intro" id="intro"><?= $dec_intro ?></textarea>
                            <?php } else { ?>
                                <textarea name="intro" id="intro"><?= $rec['intro'] ?></textarea>
                            <?php } ?>
                            <div class="input-assistance">
                                <p class="alert-text">
                                    <?php if (isset($err_intro)) : ?>
                                        <?= $err_intro ?>
                                    <?php endif ?>
                                </p>
                                <span class="intro-length"><?= $intro_length ?>/300</span>
                            </div>
                        </dd>
                    </dl>
                    <input type="submit" id="button" value="変更する">
                </form>
                <?php include_once('./views/user-window.php') ?>
            </div>
        <?php } else { ?>
            <?php include_once('./views/notlogin.html') ?>
        <?php } ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/click-toggle.js"></script>
    <script src="./js/function.js"></script>
    <script src="./js/input-focus.js"></script>
    <script src="./js/common.js"></script>
    <script>
        'use strict';
        const login = '<?= $login ?>';

        $('#file-input').on('change', function(e) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $(".cur-icon").attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        });

        $('.ng-name').parent().css('border', '1px solid #e33339');
        $('.ng-intro').css('border', '1px solid #e33339');

        const name = document.querySelector('#nickname');
        const intro = document.querySelector('#intro');
        name.onfocus = gotfocus(name);
        name.onblur = lostfocus;

        inputlength(name, '.name-length', 15);
        inputlength(intro, '.intro-length', 300);

        $('#nickname').focus(function() {
            if (this.value.length > 0) {
                $(this).next().removeClass('none');
            }
        })

        $('.fa-times-circle').click(function() {
            $(this).prev().val('');
            $(this).addClass('none');
            $(this).parent().next().children('.name-length').text('0/15');
        })

        intro.addEventListener('input', function() {
            let lines = intro.value.split("\n");
            if (lines.length > 8) {
                let result = "";
                for (var i = 0; i < 8; i++) {
                    result += lines[i] + "\n";
                }
                intro.value = result;
            }
        }, false);

        const button = document.querySelector('#button');
        button.addEventListener('click', (event) => {
            const alert_text = document.querySelectorAll('.alert-text');
            let flg = false;

            if (!name.value.match(/[\S]+/)) {
                alert_text[0].textContent = 'ユーザ名を入力してください';
                inputs[2].parentNode.style.border = '1px solid #e33339';
                flg = true;
            } else {
                if (name.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                    alert_text[0].textContent = '特殊文字は入力できません';
                    inputs[2].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (name.value.length > 15) {
                        alert_text[0].textContent = '15文字以下で入力してください';
                        inputs[2].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }
                }
            }
            if (intro.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                alert_text[1].textContent = '特殊文字は入力できません';
                textArea[0].style.border = '1px solid #e33339';
                flg = true;
            } else {
                if (intro.value.length > 300) {
                    alert_text[1].textContent = '300文字以下で入力してください';
                    textArea[0].style.border = '1px solid #e33339';
                    flg = true;
                }
            }

            if (flg) {
                event.preventDefault();

                $('.fa-times-circle').click(function() {
                    alert_text[0].textContent = 'ユーザ名を入力してください';
                    inputs[2].parentNode.style.border = '1px solid #e33339';
                })

                name.addEventListener('input', () => {
                    if (!name.value.match(/[\S]+/)) {
                        alert_text[0].textContent = 'ユーザ名を入力してください';
                        inputs[2].parentNode.style.border = '1px solid #e33339';
                    } else {
                        if (name.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                            alert_text[0].textContent = '特殊文字は入力できません';
                            inputs[2].parentNode.style.border = '1px solid #e33339';
                        } else {
                            if (name.value.length > 15) {
                                alert_text[0].textContent = '15文字以下で入力してください';
                                inputs[2].parentNode.style.border = '1px solid #e33339';
                            } else {
                                alert_text[0].textContent = '';
                                inputs[2].parentNode.style.border = '1px solid #696969';
                            }
                        }
                    }
                })

                intro.addEventListener('input', () => {
                    if (intro.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                        alert_text[1].textContent = '特殊文字は入力できません';
                        textArea[0].style.border = '1px solid #e33339';
                    } else {
                        if (intro.value.length > 300) {
                            alert_text[1].textContent = '300文字以下で入力してください';
                            textArea[0].style.border = '1px solid #e33339';
                        } else {
                            alert_text[1].textContent = '';
                            textArea[0].style.border = '1px solid #696969';
                        }
                    }
                })
            }
        })
    </script>
    <script src="./js/index.js"></script>
</body>

</html>