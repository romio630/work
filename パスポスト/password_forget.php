<?php
if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/dbconnect.php');
    require_once('./common/function.php');
    session_start();
    session_regenerate_id(true);
    $login = 1;
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];

    require_once('./common/sql.php');

    $stmt = null;
    $dbh = null;
} else {
    $login = 0;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <title>パスワードを忘れた方 -パスポスト</title>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/form.css">
</head>

<body>
    <?php include_once('./views/header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <div id="form" class="form-wrapper">
            <h2>パスワードを忘れた方</h2>
            <form class="form-contents" onsubmit="return passforget()">
                <dl>
                    <dt><label for="email">メールアドレス</label></dt>
                    <dd>
                        <div class="text-input">
                            <input type="email" name="email" class="input" id="email" placeholder="例）suita.kyoko@example.com">
                            <i class="fas fa-times-circle none"></i>
                        </div>
                        <p class="alert-text"></p>
                    </dd>
                </dl>
                <p class="form-text">ご登録されたメールアドレスにパスワード再設定用のご案内が送信されます。</p>
                <input type="submit" value="パスワードをリセットする" id="button">
            </form>
            <?php if (isset($_COOKIE['paspost_id'])) : ?>
                <?php include_once('./views/user-window.php') ?>
            <?php endif ?>
            <div class="modal-bg close">
                <div class="success-modal modal">
                    <p class=" header">メールを確認してください</p>
                    <p class="content"></p>
                    <button class="modal-cancel">OK</button>
                </div>
            </div>
        </div>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/function.js"></script>
    <script src="./js/input-focus.js"></script>
    <script src="./js/common.js"></script>
    <script>
        'use strict';
        const login = '<?= $login ?>';
        const email = document.querySelector('#email');
        email.onfocus = gotfocus(email);
        email.onblur = lostfocus;

        $('#email').focus(function() {
            if (this.value.length > 0) {
                $(this).next().removeClass('none');
            }
        })

        $('.fa-times-circle').click(function() {
            $(this).prev().val('');
            $(this).addClass('none');
        })

        function passforget() {
            const alert_text = document.querySelectorAll('.alert-text');
            let flg = false;

            if (email.value.match(/[\S]+/)) {
                if (!email.value.match(/^[\w\-\.]+\@[\w\-\.]+\.([a-z]+)$/)) {
                    alert_text[0].textContent = 'メールアドレスを正しく入力してください。';
                    inputs[0].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (email.value.length > 255) {
                        alert_text[0].textContent = '255文字以下で入力してください。';
                        inputs[0].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }
                }
            } else {
                alert_text[0].textContent = 'メールアドレスを入力してください。';
                inputs[0].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (flg) {
                event.preventDefault();
                $('.fa-times-circle').click(function() {
                    alert_text[0].textContent = 'メールアドレスを入力してください。';
                    inputs[0].parentNode.style.border = '1px solid #e33339';
                })
                email.addEventListener('input', () => {
                    if (email.value.match(/[\S]+/)) {
                        if (email.value.match(/^[\w\-\.]+\@[\w\-\.]+\.([a-z]+)$/)) {
                            if (email.value.length > 255) {
                                alert_text[0].textContent = '255文字以下で入力してください。';
                                inputs[0].parentNode.style.border = '1px solid #e33339';
                            } else {
                                alert_text[0].textContent = '';
                                inputs[0].parentNode.style.border = '1px solid #696969';
                            }
                        } else {
                            alert_text[0].textContent = 'メールアドレスを正しく入力してください。';
                            inputs[0].parentNode.style.border = '1px solid #e33339';
                        }
                    } else {
                        alert_text[0].textContent = 'メールアドレスを入力してください。';
                        inputs[0].parentNode.style.border = '1px solid #e33339';
                    }
                })

            } else {
                $.ajax({
                    type: "POST",
                    url: "ajax_password_forget.php",
                    datatype: "json",
                    data: {
                        "email": email.value,
                    }
                }).done((data) => {
                    $('.content').text(`${data}にパスワード再設定用のメールを送信しました。`);
                    if (login == 1) {
                        modalBg[1].classList.remove('close');
                        modalCan[1].addEventListener('click', () => {
                            location.href = './login.php';
                        })
                    } else {
                        modalBg[0].classList.remove('close');
                        modalCan[0].addEventListener('click', () => {
                            location.href = './login.php';
                        })
                    }
                });
                return false;
            }
        }
    </script>
    <script src="./js/index.js"></script>
</body>

</html>