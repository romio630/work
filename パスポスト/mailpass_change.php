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
    <link rel="stylesheet" href="./css/form.css">
    <title>メール・パスワード変更 -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div id="form" class="form-wrapper">
                <h2>メール・パスワードを変更する</h2>
                <ul class="tab-list">
                    <li class="tab active" data-left="0">メールアドレス</li>
                    <li class="tab" data-left="50%">パスワード</li>
                    <div class="active-bar"></div>
                    <div class="bb"></div>
                </ul>
                <div class="form-list">
                    <form class="form-contents panel show" onsubmit="return emailchange()">
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
                        <p class="form-text">メールアドレスを変更すると確認メールが送信されます。メール内のURLをクリックすると変更完了です。</p>
                        <input type="submit" value="変更する" id="email-change-btn">
                    </form>
                    <form class="form-contents panel" onsubmit="return passchange()">
                        <dl>
                            <dt><label for="pass">現在のパスワード</label></dt>
                            <dd>
                                <div class="text-input">
                                    <input type="password" name="pass" class="input" id="cur-pass">
                                    <i class="far fa-eye-slash cur-pass"></i>
                                </div>
                                <div class="input-assistance">
                                    <p class="alert-text"></p>
                                    <a href="./password_forget.php" class="pass-forget-btn">パスワードを忘れた方はこちら</a>
                                </div>
                            </dd>
                            <dt><label for="pass">新しいパスワード</label></dt>
                            <dd>
                                <div class="text-input">
                                    <input type="password" name="pass" class="input" id="pass">
                                    <i class="far fa-eye-slash pass"></i>
                                </div>
                                <p class="alert-text"></p>
                            </dd>
                            <dt><label for="re_pass">パスワード（確認用）</label></dt>
                            <dd>
                                <div class="text-input">
                                    <input type="password" name="re_pass" class="input" id="re-pass">
                                    <i class="far fa-eye-slash re-pass"></i>
                                </div>
                                <p class="alert-text"></p>
                            </dd>
                        </dl>
                        <p class="form-text">パスワードを設定したい場合は上記をすべて入力してください。</p>
                        <input type="submit" value="変更する" id="pass-change-btn">
                    </form>
                </div>
                <?php include_once('./views/user-window.php') ?>
                <div class="modal-bg close">
                    <div class="success-modal modal">
                        <p class="header">メールを確認してください</p>
                        <p class="modal-text"></p>
                        <button class="modal-cancel">OK</button>
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
    <script src="./js/function.js"></script>
    <script src="./js/input-focus-epass.js"></script>
    <script src="./js/common.js"></script>
    <script>
        const login = '<?= $login ?>';
        var userid = JSON.parse('<?= $id_json ?>');
        const email = document.querySelector('#email');
        const curPass = document.querySelector('#cur-pass');
        const pass = document.querySelector('#pass');
        const rePass = document.querySelector('#re-pass');
        const alert_text = document.querySelectorAll('.alert-text');
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

        $('.fa-eye-slash.cur-pass').clickToggle(function() {
            $('#cur-pass').attr('type', 'text');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        }, function() {
            $('#cur-pass').attr('type', 'password');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        })

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

        modalCan[1].addEventListener('click', () => {
            location.href = './configuration.php';
        })

        function passchange() {
            let flg = false;

            if (curPass.value.match(/[\S]+/)) {
                if (curPass.value.match(/[^a-zA-Z0-9]+/)) {
                    alert_text[1].textContent = '半角英数字で入力してください';
                    inputs[3].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (curPass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                        if (curPass.value.length < 7) {
                            alert_text[1].textContent = '7文字以上で入力してください';
                            inputs[3].parentNode.style.border = '1px solid #e33339';
                            flg = true;
                        } else {
                            if (curPass.value.length > 128) {
                                alert_text[1].textContent = '128文字以下で入力してください';
                                inputs[3].parentNode.style.border = '1px solid #e33339';
                                flg = true;
                            }
                        }
                    } else {
                        alert_text[1].textContent = '文字と数字を組み合わせてください';
                        inputs[3].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }

                }
            } else {
                alert_text[1].textContent = '現在のパスワードを入力してください';
                inputs[3].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (pass.value.match(/[\S]+/)) {
                if (pass.value.match(/[^a-zA-Z0-9]+/)) {
                    alert_text[2].textContent = '半角英数字で入力してください';
                    inputs[4].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (pass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                        if (pass.value.length < 7) {
                            alert_text[2].textContent = '7文字以上で入力してください';
                            inputs[4].parentNode.style.border = '1px solid #e33339';
                            flg = true;
                        } else {
                            if (pass.value.length > 128) {
                                alert_text[2].textContent = '128文字以下で入力してください';
                                inputs[4].parentNode.style.border = '1px solid #e33339';
                                flg = true;
                            }
                        }
                    } else {
                        alert_text[2].textContent = '文字と数字を組み合わせてください';
                        inputs[4].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }

                }
            } else {
                alert_text[2].textContent = '新しいパスワードを入力してください';
                inputs[4].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (rePass.value.match(/[\S]+/)) {
                if (pass.value !== rePass.value) {
                    alert_text[3].textContent = '新しいパスワードと新しいパスワード（確認）が一致していません';
                    inputs[5].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                }
            } else {
                alert_text[3].textContent = 'パスワード（確認用）を入力してください';
                inputs[5].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (flg) {
                event.preventDefault();
                curPass.addEventListener('input', () => {
                    if (curPass.value.match(/./)) {
                        if (curPass.value.match(/[^a-zA-Z0-9]+/)) {
                            alert_text[1].textContent = '半角英数字で入力してください';
                            inputs[3].parentNode.style.border = '1px solid #e33339';
                        } else {
                            if (curPass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                                if (curPass.value.length < 7) {
                                    alert_text[1].textContent = '7文字以上で入力してください';
                                    inputs[3].parentNode.style.border = '1px solid #e33339';
                                } else {
                                    if (curPass.value.length > 128) {
                                        alert_text[1].textContent = '128文字以下で入力してください';
                                        inputs[3].parentNode.style.border = '1px solid #e33339';
                                    } else {
                                        alert_text[1].textContent = '';
                                        inputs[3].parentNode.style.border = '1px solid #696969';
                                    }
                                }
                            } else {
                                alert_text[1].textContent = '文字と数字を組み合わせてください';
                                inputs[3].parentNode.style.border = '1px solid #e33339';
                            }

                        }
                    } else {
                        alert_text[1].textContent = '現在のパスワードを入力してください';
                        inputs[3].parentNode.style.border = '1px solid #e33339';
                    }
                })

                pass.addEventListener('input', () => {
                    if (pass.value.match(/./)) {
                        if (pass.value.match(/[^a-zA-Z0-9]+/)) {
                            alert_text[2].textContent = '半角英数字で入力してください';
                            inputs[4].parentNode.style.border = '1px solid #e33339';
                        } else {
                            if (pass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                                if (pass.value.length < 7) {
                                    alert_text[2].textContent = '7文字以上で入力してください';
                                    inputs[4].parentNode.style.border = '1px solid #e33339';
                                } else {
                                    if (pass.value.length > 128) {
                                        alert_text[2].textContent = '128文字以下で入力してください';
                                        inputs[4].parentNode.style.border = '1px solid #e33339';
                                    } else {
                                        alert_text[2].textContent = '';
                                        inputs[4].parentNode.style.border = '1px solid #696969';
                                    }
                                }
                            } else {
                                alert_text[2].textContent = '文字と数字を組み合わせてください';
                                inputs[4].parentNode.style.border = '1px solid #e33339';
                            }

                        }
                    } else {
                        alert_text[2].textContent = '新しいパスワードを入力してください';
                        inputs[4].parentNode.style.border = '1px solid #e33339';
                    }
                })

                rePass.addEventListener('input', () => {
                    if (rePass.value.match(/./)) {
                        if (pass.value !== rePass.value) {
                            alert_text[3].textContent = '新しいパスワードと新しいパスワード（確認）が一致していません';
                            inputs[5].parentNode.style.border = '1px solid #e33339';
                        } else {
                            alert_text[3].textContent = '';
                            inputs[5].parentNode.style.border = '1px solid #696969';
                        }
                    } else {
                        alert_text[3].textContent = 'パスワード（確認用）を入力してください';
                        inputs[5].parentNode.style.border = '1px solid #e33339';
                    }
                })

            } else {
                $.ajax({
                    type: "POST",
                    url: "ajax_password_change_done.php",
                    datatype: "json",
                    data: {
                        "id": userid,
                        "pass": $('#cur-pass').val(),
                        "newpass": $('#pass').val(),
                    }
                }).done((data) => {
                    if (data == 0) {
                        alert_text[1].textContent = '入力されたパスワードが間違っています';
                    } else {
                        location.href = './configuration.php';
                    }
                });
                return false;
            }
        }

        function emailchange() {
            let flg = false;

            if (email.value.match(/[\S]+/)) {
                if (!email.value.match(/^[\w\-\.]+\@[\w\-\.]+\.([a-z]+)$/)) {
                    alert_text[0].textContent = 'メールアドレスを正しく入力してください';
                    inputs[2].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (email.value.length > 255) {
                        alert_text[0].textContent = '255文字以下で入力してください';
                        inputs[2].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }
                }
            } else {
                alert_text[0].textContent = 'メールアドレスを入力してください';
                inputs[2].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (flg) {
                event.preventDefault();
                $('.fa-times-circle').click(function() {
                    alert_text[0].textContent = 'メールアドレスを入力してください';
                    inputs[2].parentNode.style.border = '1px solid #e33339';
                })
                email.addEventListener('input', () => {
                    if (email.value.match(/[\S]+/)) {
                        if (email.value.match(/^[\w\-\.]+\@[\w\-\.]+\.([a-z]+)$/)) {
                            if (email.value.length > 255) {
                                alert_text[0].textContent = '255文字以下で入力してください';
                                inputs[2].parentNode.style.border = '1px solid #e33339';
                            } else {
                                alert_text[0].textContent = '';
                                inputs[2].parentNode.style.border = '1px solid #696969';
                            }
                        } else {
                            alert_text[0].textContent = 'メールアドレスを正しく入力してください';
                            inputs[2].parentNode.style.border = '1px solid #e33339';
                        }
                    } else {
                        alert_text[0].textContent = 'メールアドレスを入力してください';
                        inputs[2].parentNode.style.border = '1px solid #e33339';
                    }
                })

            } else {
                $.ajax({
                    type: "POST",
                    url: "ajax_email_change_check.php",
                    datatype: "json",
                    data: {
                        "id": userid,
                        "email": email.value,
                    }
                }).done((data) => {
                    if (data == 0) {
                        alert_text[0].textContent = 'メールアドレスに誤りがありますご確認ください';
                    } else {
                        modalBg[1].classList.remove('close');
                        $('.modal-text').text(`${data}にパスワード確認のメールを送信しました。メール内のURLをクリックして変更を完了してください。`)
                    }
                });
                return false;
            }
        }
    </script>
    <script src="./js/index.js"></script>
</body>

</html>