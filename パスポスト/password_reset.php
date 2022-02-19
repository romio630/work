<?php
require_once('./common/sanitize.php');
require_once('./common/function.php');
$request = sanitize($_REQUEST);
$id = $request['id'];

if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/dbconnect.php');
    session_start();
    session_regenerate_id(true);
    $login = 1;
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];

    require_once('./common/sql.php');

    $stmt = null;
    $dbh = null;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/form.css">
    <title>新しいパスワードの再設定 -パスポスト</title>
</head>

<body>
    <?php include_once('./views/header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <div id="form" class="form-wrapper">
            <h2>新しいパスワードの設定</h2>
            <form action="password_reset_check.php" method="post" class="form-contents" onsubmit="return formCheck()">
                <dl>
                    <dt><label for="pass">パスワード</label></dt>
                    <dd>
                        <div class="text-input">
                            <input type="password" name="pass" class="input" id="pass">
                            <i class="far fa-eye-slash pass"></i>
                        </div>
                        <p class="limit">※7文字以上の半角英数字</p>
                        <p class="alert-text"></p>
                    </dd>
                    <dt><label for="re_pass">パスワード（確認用）</label></dt>
                    <dd>
                        <div class="text-input">
                            <input type="password" name="re_pass" class="input" id="re-pass">
                            <i class="far fa-eye-slash re-pass"></i>
                        </div>
                        <p class="limit">※7文字以上の半角英数字</p>
                        <p class="alert-text"></p>
                    </dd>
                </dl>
                <input type="hidden" value="<?= $id ?>" name="id">
                <input type="submit" value="設定する" id="button">
            </form>
        </div>
        <?php if (isset($_COOKIE['paspost_id'])) : ?>
            <?php include_once('./views/user-window.php') ?>
        <?php endif ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/click-toggle.js"></script>
    <script src="./js/input-focus.js"></script>
    <script src="./js/common.js"></script>
    <script>
        'use strict';
        const login = '<?= $login ?>';
        $('.fa-eye-slash.pass').clickToggle(function() {
            $('#pass').attr('type', 'text');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        }, function() {
            $('#pass').attr('type', 'password');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        })

        $('.fa-eye-slash.re-pass').clickToggle(function() {
            $('#re-pass').attr('type', 'text');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        }, function() {
            $('#re-pass').attr('type', 'password');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        })

        function formCheck() {
            const alert_text = document.querySelectorAll('.alert-text');
            let flg = false;
            const pass = document.querySelector('#pass');
            const re_pass = document.querySelector('#re-pass');
            if (pass.value.match(/[\S]+/)) {
                if (pass.value.match(/[^a-zA-Z0-9]+/)) {
                    alert_text[0].textContent = '半角英数字で入力してください';
                    inputs[0].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (pass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                        if (pass.value.length < 7) {
                            alert_text[0].textContent = '7文字以上で入力してください';
                            inputs[0].parentNode.style.border = '1px solid #e33339';
                            flg = true;
                        } else {
                            if (pass.value.length > 128) {
                                alert_text[0].textContent = '128文字以下で入力してください';
                                inputs[0].parentNode.style.border = '1px solid #e33339';
                                flg = true;
                            }
                        }
                    } else {
                        alert_text[0].textContent = '文字と数字を組み合わせてください';
                        inputs[0].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }

                }
            } else {
                alert_text[0].textContent = 'パスワードを入力してください';
                inputs[0].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (re_pass.value.match(/[\S]+/)) {
                if (pass.value !== re_pass.value) {
                    alert_text[1].textContent = '新しいパスワードと新しいパスワード（確認）が一致していません';
                    inputs[1].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                }
            } else {
                alert_text[1].textContent = 'パスワード（確認用）を入力してください';
                inputs[1].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (flg) {
                pass.addEventListener('input', () => {
                    if (pass.value.match(/[\S]+/)) {
                        if (pass.value.match(/[^a-zA-Z0-9]+/)) {
                            alert_text[0].textContent = '半角英数字で入力してください';
                            inputs[0].parentNode.style.border = '1px solid #e33339';
                        } else {
                            if (pass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                                if (pass.value.length < 7) {
                                    alert_text[0].textContent = '7文字以上で入力してください';
                                    inputs[0].parentNode.style.border = '1px solid #e33339';
                                } else {
                                    if (pass.value.length > 128) {
                                        alert_text[0].textContent = '128文字以下で入力してください';
                                        inputs[0].parentNode.style.border = '1px solid #e33339';
                                    } else {
                                        alert_text[0].textContent = '';
                                        inputs[0].parentNode.style.border = '1px solid #696969';
                                    }
                                }
                            } else {
                                alert_text[0].textContent = '文字と数字を組み合わせてください';
                                inputs[0].parentNode.style.border = '1px solid #e33339';
                            }

                        }
                    } else {
                        alert_text[0].textContent = 'パスワードを入力してください';
                        inputs[0].parentNode.style.border = '1px solid #e33339';
                    }
                })

                re_pass.addEventListener('input', () => {
                    if (re_pass.value.match(/[\S]+/)) {
                        if (pass.value !== re_pass.value) {
                            alert_text[1].textContent = '新しいパスワードと新しいパスワード（確認）が一致していません';
                            inputs[1].parentNode.style.border = '1px solid #e33339';
                        } else {
                            alert_text[1].textContent = '';
                            inputs[1].parentNode.style.border = '1px solid #696969';
                        }
                    } else {
                        alert_text[1].textContent = 'パスワード（確認用）を入力してください';
                        inputs[1].parentNode.style.border = '1px solid #e33339';
                    }
                })
                return false;
            }
        }
    </script>
    <script src="./js/index.js"></script>
</body>

</html>