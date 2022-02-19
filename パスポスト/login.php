<!DOCTYPE html>
<html lang="ja">

<head>
    <title>ログイン -パスポスト</title>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/form.css">
</head>

<body>
    <?php include_once('./views/header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <div id="form" class="form-wrapper">
            <h2>ログイン</h2>
            <form class="form-contents" onsubmit="return login()">
                <dl>
                    <dt><label for="email">メールアドレス</label></dt>
                    <dd>
                        <div class="text-input">
                            <input type="email" name="email" class="input" id="email" placeholder="例）suita.kyoko@example.com">
                            <i class="fas fa-times-circle none"></i>
                        </div>
                        <p class="alert-text"></p>
                    </dd>
                    <dt><label for="pass">パスワード</label></dt>
                    <dd>
                        <div class="text-input">
                            <input type="password" name="pass" class="input" id="pass">
                            <i class="far fa-eye-slash"></i>
                        </div>
                        <p class="alert-text"></p>
                    </dd>
                </dl>
                <input type="submit" value="ログイン" id="button">
                <a href="./password_forget.php" class="pass-forget-btn">パスワードを忘れた方はこちら</a>
                <div class="bb"></div>
            </form>
            <div class="for-not-user">
                <p>アカウントをお持ちでない方</p>
                <a href="./user_add.php" class="user-add-btn">ユーザー登録</a>
            </div>
        </div>
    </main>
    <?php include_once('./views/footer.html') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/function.js"></script>
    <script src="./js/click-toggle.js"></script>
    <script src="./js/input-focus.js"></script>
    <script>
        'use strict';
        const email = document.querySelector('#email');
        const pass = document.querySelector('#pass');
        const button = document.querySelector('#button');
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

        $('.fa-eye-slash').clickToggle(function() {
            $('#pass').attr('type', 'text');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        }, function() {
            $('#pass').attr('type', 'password');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        })

        function login() {
            const alert_text = document.querySelectorAll('.alert-text');
            let flg = false;

            if (email.value.match(/[\S]+/)) {
                if (!email.value.match(/^[\w\-\.]+\@[\w\-\.]+\.([a-z]+)$/)) {
                    alert_text[0].textContent = 'メールアドレスを正しく入力してください';
                    inputs[0].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (email.value.length > 255) {
                        alert_text[0].textContent = '255文字以下で入力してください';
                        inputs[0].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }
                }
            } else {
                alert_text[0].textContent = 'メールアドレスを入力してください';
                inputs[0].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (pass.value.match(/[\S]+/)) {
                if (pass.value.match(/[^a-zA-Z0-9]+/)) {
                    alert_text[1].textContent = '半角英数字で入力してください';
                    inputs[1].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (pass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                        if (pass.value.length < 7) {
                            alert_text[1].textContent = '7文字以上で入力してください';
                            inputs[1].parentNode.style.border = '1px solid #e33339';
                            flg = true;
                        } else {
                            if (pass.value.length > 128) {
                                alert_text[1].textContent = '128文字以下で入力してください';
                                inputs[1].parentNode.style.border = '1px solid #e33339';
                                flg = true;
                            }
                        }
                    } else {
                        alert_text[1].textContent = '文字と数字を組み合わせてください';
                        inputs[1].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }

                }
            } else {
                alert_text[1].textContent = 'パスワードを入力してください';
                inputs[1].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (flg) {
                event.preventDefault();

                $('.fa-times-circle').click(function() {
                    alert_text[0].textContent = 'メールアドレスを入力してください';
                    inputs[0].parentNode.style.border = '1px solid #e33339';
                })
                pass.addEventListener('input', () => {
                    if (pass.value.match(/[\S]+/)) {
                        if (pass.value.match(/[^a-zA-Z0-9]+/)) {
                            alert_text[1].textContent = '半角英数字で入力してください';
                            inputs[1].parentNode.style.border = '1px solid #e33339';
                        } else {
                            if (pass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                                if (pass.value.length < 7) {
                                    alert_text[1].textContent = '7文字以上で入力してください';
                                    inputs[1].parentNode.style.border = '1px solid #e33339';
                                } else {
                                    if (pass.value.length > 128) {
                                        alert_text[1].textContent = '128文字以下で入力してください';
                                        inputs[1].parentNode.style.border = '1px solid #e33339';
                                    } else {
                                        alert_text[1].textContent = '';
                                        inputs[1].parentNode.style.border = '1px solid #696969';
                                    }
                                }
                            } else {
                                alert_text[1].textContent = '文字と数字を組み合わせてください';
                                inputs[1].parentNode.style.border = '1px solid #e33339';
                            }

                        }
                    } else {
                        alert_text[1].textContent = 'パスワードを入力してください';
                        inputs[1].parentNode.style.border = '1px solid #e33339';
                    }
                })

                email.addEventListener('input', () => {
                    if (email.value.match(/[\S]+/)) {
                        if (email.value.match(/^[\w\-\.]+\@[\w\-\.]+\.([a-z]+)$/)) {
                            if (email.value.length > 255) {
                                alert_text[0].textContent = '255文字以下で入力してください';
                                inputs[0].parentNode.style.border = '1px solid #e33339';
                            } else {
                                alert_text[0].textContent = '';
                                inputs[0].parentNode.style.border = '1px solid #696969';
                            }
                        } else {
                            alert_text[0].textContent = 'メールアドレスを正しく入力してください';
                            inputs[0].parentNode.style.border = '1px solid #e33339';
                        }
                    } else {
                        alert_text[0].textContent = 'メールアドレスを入力してください';
                        inputs[0].parentNode.style.border = '1px solid #e33339';
                    }
                })
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajax_login_check.php",
                    datatype: "json",
                    data: {
                        "email": email.value,
                        "pass": $('#pass').val(),
                    }
                }).done((data) => {
                    if (data == 1) {
                        alert_text[0].textContent = 'メールアドレスが違います';
                        inputs[0].parentNode.style.border = '1px solid #e33339';
                    } else if (data == 2) {
                        alert_text[1].textContent = 'パスワードが違います';
                        inputs[1].parentNode.style.border = '1px solid #e33339';
                    } else {
                        location.href = 'index.php';
                    }
                });
                return false;
            }
        }
    </script>
</body>

</html>