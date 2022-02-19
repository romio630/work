<?php
session_start();
session_regenerate_id(true);

if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/dbconnect.php');
    require_once('./common/function.php');
    $login = 1;
    $id = $_SESSION['id'];
    $id_json = json_encode($id);
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
    <link rel="stylesheet" href="./css/userpage.css">
    <link rel="stylesheet" href="./css/form.css">
    <title>退会する -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div id="form" class="form-wrapper">
                <h2>退会する</h2>
                <form class="form-contents" onsubmit="return withdrawal()">
                    <p class="form-text">上記のパスポストアカウントを削除します。ユーザー情報、投稿した手紙はすべて消滅します。退会は即時反映され、一度退会すると削除した情報は戻すことができません。</p>
                    <dl>
                        <dt><label for="pass">パスワード</label></dt>
                        <dd>
                            <div class="text-input">
                                <input type="password" name="pass" class="input" id="pass">
                                <i class="far fa-eye"></i>
                            </div>
                            <p class="alert-text"></p>
                        </dd>
                    </dl>
                    <button id="button">退会する</button>
                    <a href="./password_forget.php" class="pass-forget-btn">パスワードを忘れた方はこちら</a>
                </form>
                <?php include_once('./views/user-window.php') ?>
                <div class="modal-bg close">
                    <div class="modal-confirm modal">
                        <p>本当に退会してもよろしいですか？</p>
                        <div>
                            <button class="modal-cancel">キャンセル</button>
                            <button id="withdrawal-btn">退会する</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <?php include_once('./views/notlogin.html') ?>
        <?php } ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/click-toggle.js"></script>
    <script src="./js/input-focus.js"></script>
    <script src="./js/common.js"></script>
    <script>
        'use strict';
        const login = '<?= $login ?>';
        var userid = JSON.parse('<?= $id_json ?>');
        const button = document.querySelector('#button');

        $('.fa-eye').clickToggle(function() {
            $('#pass').attr('type', 'text');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        }, function() {
            $('#pass').attr('type', 'password');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        })

        function withdrawal() {
            const alert_text = document.querySelectorAll('.alert-text');
            const pass = document.querySelector('#pass');
            let flg = false;

            if (pass.value.match(/[\S]+/)) {
                if (pass.value.match(/[^a-zA-Z0-9]+/)) {
                    alert_text[0].textContent = '半角英数字で入力してください';
                    inputs[2].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (pass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                        if (pass.value.length < 7) {
                            alert_text[0].textContent = '7文字以上で入力してください';
                            inputs[2].parentNode.style.border = '1px solid #e33339';
                            flg = true;
                        } else {
                            if (pass.value.length > 128) {
                                alert_text[0].textContent = '128文字以下で入力してください';
                                inputs[2].parentNode.style.border = '1px solid #e33339';
                                flg = true;
                            }
                        }
                    } else {
                        alert_text[0].textContent = '文字と数字を組み合わせてください';
                        inputs[2].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }

                }
            } else {
                alert_text[0].textContent = 'パスワードを入力してください';
                inputs[2].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (flg) {
                event.preventDefault();
                pass.addEventListener('input', () => {
                    if (pass.value.match(/[\S]+/)) {
                        if (pass.value.match(/[^a-zA-Z0-9]+/)) {
                            alert_text[0].textContent = '半角英数字で入力してください';
                            inputs[2].parentNode.style.border = '1px solid #e33339';
                        } else {
                            if (pass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                                if (pass.value.length < 7) {
                                    alert_text[0].textContent = '7文字以上で入力してください';
                                    inputs[2].parentNode.style.border = '1px solid #e33339';
                                } else {
                                    if (pass.value.length > 128) {
                                        alert_text[0].textContent = '128文字以下で入力してください';
                                        inputs[2].parentNode.style.border = '1px solid #e33339';
                                    } else {
                                        alert_text[0].textContent = '';
                                        inputs[2].parentNode.style.border = '1px solid #696969';
                                    }
                                }
                            } else {
                                alert_text[0].textContent = '文字と数字を組み合わせてください';
                                inputs[2].parentNode.style.border = '1px solid #e33339';
                            }

                        }
                    } else {
                        alert_text[0].textContent = 'パスワードを入力してください';
                        inputs[2].parentNode.style.border = '1px solid #e33339';
                    }
                })

            } else {
                $.ajax({
                    type: "POST",
                    url: "ajax_withdrawal_check.php",
                    datatype: "json",
                    data: {
                        "id": userid,
                        "pass": $('#pass').val(),
                    }
                }).done((data) => {
                    if (data == 0) {
                        alert_text[0].textContent = 'パスワードが違います';
                    } else {
                        modalBg[1].classList.remove('close');
                    }
                });

                return false;
            }
        }

        $('#withdrawal-btn').click(() => {
            $.ajax({
                type: "POST",
                url: "ajax_withdrawal_done.php",
                datatype: "json",
                data: {
                    "id": userid,
                }
            }).fail((data) => {
                location.href = './index.php'
            });

            return false;
        })
    </script>
    <script src="./js/index.js"></script>
</body>

</html>