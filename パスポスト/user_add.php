<!DOCTYPE html>
<html lang="ja">

<head>
    <title>ユーザ登録 -パスポスト</title>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/form.css">
</head>

<body>
    <?php include_once('./views/header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <div id="form" class="form-wrapper">
            <h2>ユーザ登録</h2>
            <!-- <div class="form-contents"> -->
            <form class="form-contents" onsubmit="return useradd()">
                <dl>
                    <dt><label for="name">ユーザー名</label></dt>
                    <dd>
                        <div class="text-input">
                            <input type="text" name="name" class="input" id="name" placeholder="パスポスト内でのユーザ名">
                            <i class="fas fa-times-circle name none"></i>
                        </div>
                        <div class="input-assistance">
                            <p class="alert-text"></p>
                            <span class="name-length">0/15</span>
                        </div>
                    </dd>
                    <dt><label for="email">メールアドレス</label></dt>
                    <dd>
                        <div class="text-input">
                            <input type="email" name="email" class="input" id="email" placeholder="例）suita.kyoko@example.com">
                            <i class="fas fa-times-circle email none"></i>
                        </div>
                        <p class="alert-text"></p>
                    </dd>
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
                            <input type="password" class="input" id="re_pass">
                            <i class="far fa-eye-slash re_pass"></i>
                        </div>
                        <p class="limit">※7文字以上の半角英数字</p>
                        <p class="alert-text"></p>
                    </dd>
                </dl>
                <input type="submit" value="登録" id="button">
                <p class="privacy">※本入力フォームにご登録いただいた個人情報につきましては、厳重に管理を行っております。<br>
                    法令等に基づき正規の手続きによって司法捜査機関 による開示要求が行われた場合を除き、第三者に開示もしくは、提供することはございません。</p>
                <div class="bb"></div>
            </form>
            <div class="for-user">
                <p>アカウントをお持ちの方</p>
                <a href="./login.php" class="login-btn">ログイン</a>
            </div>
        </div>
        <div class="modal-bg close">
            <div class="success-modal modal">
                <p class="header">メールを確認してください</p>
                <p class="content"></p>
                <button class="modal-cancel">OK</button>
            </div>
        </div>
    </main>
    <?php include_once('./views/footer.html') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/click-toggle.js"></script>
    <script src="./js/function.js"></script>
    <script src="./js/modal.js"></script>
    <script src="./js/input-focus.js"></script>
    <script>
        'use strict';
        const name = document.querySelector('#name');
        const email = document.querySelector('#email');
        const pass = document.querySelector('#pass');
        const rePass = document.querySelector('#re_pass');
        const button = document.querySelector('#button');
        name.onfocus = gotfocus(name);
        name.onblur = lostfocus;
        email.onfocus = gotfocus(email);
        email.onblur = lostfocus;

        inputlength(name, '.name-length', 15);

        $('#name').focus(function() {
            if (this.value.length > 0) {
                $(this).next().removeClass('none');
            }
        })

        $('#email').focus(function() {
            if (this.value.length > 0) {
                $(this).next().removeClass('none');
            }
        })

        $('.fa-times-circle').click(function() {
            $(this).prev().val('');
            $(this).addClass('none');
            $(this).parent().next().children('.name-length').text('0/15');
        })

        $('.fa-eye-slash.pass').clickToggle(function() {
            $('#pass').attr('type', 'text');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        }, function() {
            $('#pass').attr('type', 'password');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        })

        $('.fa-eye-slash.re_pass').clickToggle(function() {
            $('#re_pass').attr('type', 'text');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        }, function() {
            $('#re_pass').attr('type', 'password');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        })

        // button.addEventListener('click', (event) => {
        function useradd() {
            const alert_text = document.querySelectorAll('.alert-text');
            let flg = false;

            if (!name.value.match(/[\S]+/)) {
                alert_text[0].textContent = 'ユーザ名を入力してください';
                inputs[0].parentNode.style.border = '1px solid #e33339';
                flg = true;
            } else {
                if (name.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                    alert_text[0].textContent = '特殊文字は入力できません';
                    inputs[0].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (name.value.length > 15) {
                        alert_text[0].textContent = '15文字以下で入力してください';
                        inputs[0].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }
                }
            }

            if (email.value.match(/[\S]+/)) {
                if (!email.value.match(/^[\w\-\.]+\@[\w\-\.]+\.([a-z]+)$/)) {
                    alert_text[1].textContent = 'メールアドレスを正しく入力してください';
                    inputs[1].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (email.value.length > 255) {
                        alert_text[1].textContent = '255文字以下で入力してください';
                        inputs[1].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }
                }
            } else {
                alert_text[1].textContent = 'メールアドレスを入力してください';
                inputs[1].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (pass.value.match(/[\S]+/)) {
                if (pass.value.match(/[^a-zA-Z0-9]+/)) {
                    alert_text[2].textContent = '半角英数字で入力してください';
                    inputs[2].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (pass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                        if (pass.value.length < 7) {
                            alert_text[2].textContent = '7文字以上で入力してください';
                            inputs[2].parentNode.style.border = '1px solid #e33339';
                            flg = true;
                        } else {
                            if (pass.value.length > 128) {
                                alert_text[2].textContent = '128文字以下で入力してください';
                                inputs[2].parentNode.style.border = '1px solid #e33339';
                                flg = true;
                            }
                        }
                    } else {
                        alert_text[2].textContent = '文字と数字を組み合わせてください';
                        inputs[2].parentNode.style.border = '1px solid #e33339';
                        flg = true;
                    }

                }
            } else {
                alert_text[2].textContent = 'パスワードを入力してください';
                inputs[2].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (rePass.value.match(/[\S]+/)) {
                if (pass.value !== rePass.value) {
                    alert_text[3].textContent = '新しいパスワードと新しいパスワード（確認）が一致していません';
                    inputs[3].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                }
            } else {
                alert_text[3].textContent = 'パスワード（確認用）を入力してください';
                inputs[3].parentNode.style.border = '1px solid #e33339';
                flg = true;
            }

            if (flg) {
                event.preventDefault();

                $('.fa-times-circle.name').click(function() {
                    alert_text[0].textContent = 'ユーザ名を入力してください';
                    inputs[0].parentNode.style.border = '1px solid #e33339';
                })
                $('.fa-times-circle.email').click(function() {
                    alert_text[1].textContent = 'メールアドレスを入力してください';
                    inputs[1].parentNode.style.border = '1px solid #e33339';
                })

                name.addEventListener('input', () => {
                    if (!name.value.match(/[\S]+/)) {
                        alert_text[0].textContent = 'ユーザ名を入力してください';
                        inputs[0].parentNode.style.border = '1px solid #e33339';
                    } else {
                        if (name.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                            alert_text[0].textContent = '特殊文字は入力できません';
                            inputs[0].parentNode.style.border = '1px solid #e33339';
                        } else {
                            if (name.value.length > 15) {
                                alert_text[0].textContent = '15文字以下で入力してください';
                                inputs[0].parentNode.style.border = '1px solid #e33339';
                            } else {
                                alert_text[0].textContent = '';
                                inputs[0].parentNode.style.border = '1px solid #696969';
                            }
                        }
                    }
                })

                email.addEventListener('input', () => {
                    if (email.value.match(/[\S]+/)) {
                        if (email.value.match(/^[\w\-\.]+\@[\w\-\.]+\.([a-z]+)$/)) {
                            if (email.value.length > 255) {
                                alert_text[1].textContent = '255文字以下で入力してください';
                                inputs[1].parentNode.style.border = '1px solid #e33339';
                            } else {
                                alert_text[1].textContent = '';
                                inputs[1].parentNode.style.border = '1px solid #696969';
                            }
                        } else {
                            alert_text[1].textContent = 'メールアドレスを正しく入力してください';
                            inputs[1].parentNode.style.border = '1px solid #e33339';
                        }
                    } else {
                        alert_text[1].textContent = 'メールアドレスを入力してください';
                        inputs[1].parentNode.style.border = '1px solid #e33339';
                    }
                })

                pass.addEventListener('input', () => {
                    if (pass.value.match(/[\S]+/)) {
                        if (pass.value.match(/[^a-zA-Z0-9]+/)) {
                            alert_text[2].textContent = '半角英数字で入力してください';
                            inputs[2].parentNode.style.border = '1px solid #e33339';
                        } else {
                            if (pass.value.match(/([a-zA-Z][0-9]|[0-9][a-zA-Z])/)) {
                                if (pass.value.length < 7) {
                                    alert_text[2].textContent = '7文字以上で入力してください';
                                    inputs[2].parentNode.style.border = '1px solid #e33339';
                                } else {
                                    if (pass.value.length > 128) {
                                        alert_text[2].textContent = '128文字以下で入力してください';
                                        inputs[2].parentNode.style.border = '1px solid #e33339';
                                    } else {
                                        alert_text[2].textContent = '';
                                        inputs[2].parentNode.style.border = '1px solid #696969';
                                    }
                                }
                            } else {
                                alert_text[2].textContent = '文字と数字を組み合わせてください';
                                inputs[2].parentNode.style.border = '1px solid #e33339';
                            }

                        }
                    } else {
                        alert_text[2].textContent = 'パスワードを入力してください';
                        inputs[2].parentNode.style.border = '1px solid #e33339';
                    }
                })

                rePass.addEventListener('input', () => {
                    if (rePass.value.match(/[\S]+/)) {
                        if (pass.value !== rePass.value) {
                            alert_text[3].textContent = '新しいパスワードと新しいパスワード（確認）が一致していません';
                            inputs[3].parentNode.style.border = '1px solid #e33339';
                        } else {
                            alert_text[3].textContent = '';
                            inputs[3].parentNode.style.border = '1px solid #696969';
                        }
                    } else {
                        alert_text[3].textContent = 'パスワード（確認用）を入力してください';
                        inputs[3].parentNode.style.border = '1px solid #e33339';
                    }
                })
            } else {
                var replaceName = name.value.replace(/^\s+/g, '');
                $.ajax({
                    type: "POST",
                    url: "ajax_user_mail.php",
                    datatype: "json",
                    data: {
                        "email": $('#email').val(),
                        "name": replaceName,
                        "pass": $('#pass').val(),
                    }
                }).done((data) => {
                    if (data.check == 1) {
                        $('.content').text(`${data.email}にメールアドレス確認用のメールを送信しました`);
                        modalBg[0].classList.remove('close');
                        modalCan[0].addEventListener('click', () => {
                            location.href = './index.php';
                        })
                    } else {
                        alert_text[1].textContent = 'ご入力されたメールアドレスは既にご登録されています';
                    }
                });
                return false;
            }
        }
        // })
    </script>
</body>

</html>