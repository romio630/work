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
    <title>お問い合わせ -パスポスト</title>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/form.css">
</head>

<body>
    <?php include_once('./views/header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <div id="form" class="form-wrapper">
            <h2>お問い合わせ</h2>
            <form class="form-contents" onsubmit="return contact()">
                <dl>
                    <dt><label for="name">お名前<span class="mandatory">必須</span></label></dt>
                    <dd>
                        <div class="text-input">
                            <input type="text" name="name" class="input" id="name">
                            <i class="fas fa-times-circle none name"></i>
                        </div>
                        <p class="alert-text"></p>
                    </dd>
                    <dt><label for="email">メールアドレス<span class="mandatory">必須</span></label></dt>
                    <dd>
                        <div class="text-input">
                            <input type="text" name="email" class="input" id="email" placeholder="例）suita.kyoko@example.com">
                            <i class="fas fa-times-circle none email"></i>
                        </div>
                        <p class="alert-text"></p>
                    </dd>
                    <dt><label for="contents">お問い合わせ内容<span class="mandatory">必須</span></label></dt>
                    <dd>
                        <textarea name="contents" id="contents"></textarea>
                        <div class="input-assistance">
                            <p class="alert-text"></p>
                            <span class="contents-length">0/1000</span>
                        </div>
                    </dd>
                </dl>
                <input type="submit" value="送信する" id="button">
                <p class="privacy">※本入力フォームにご入力いただいた個人情報につきましては、厳重に管理を行っております。<br>
                    法令等に基づき正規の手続きによって司法捜査機関 による開示要求が行われた場合を除き、第三者に開示もしくは、提供することはございません。</p>
            </form>
        </div>
        <?php if (isset($_COOKIE['paspost_id'])) : ?>
            <?php include_once('./views/user-window.php') ?>
        <?php endif ?>
        <div class="modal-bg close">
            <div class="success-modal modal">
                <p class="header">お問い合わせありがとうございます</p>
                <p class="content"></p>
                <button class="modal-cancel">閉じる</button>
            </div>
        </div>
    </main>
    <?php include_once('./views/footer.html') ?>
    <?php include_once('./views/fixed-menu.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/function.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/input-focus.js"></script>
    <script>
        'use strict';
        const login = '<?= $login ?>';
        const name = document.querySelector('#name');
        const email = document.querySelector('#email');
        const contents = document.querySelector('#contents');
        const button = document.querySelector('#button');
        name.onfocus = gotfocus(name);
        name.onblur = lostfocus;
        email.onfocus = gotfocus(email);
        email.onblur = lostfocus;

        inputlength(contents, '.contents-length', 1000);

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
        })

        function contact() {
            const alert_text = document.querySelectorAll('.alert-text');
            let flg = false;

            if (!name.value.match(/[\S]+/)) {
                alert_text[0].textContent = 'お名前を入力してください';
                inputs[0].parentNode.style.border = '1px solid #e33339';
                flg = true;
            } else {
                if (name.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                    alert_text[0].textContent = '特殊文字は入力できません';
                    inputs[0].parentNode.style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (name.value.length > 60) {
                        alert_text[0].textContent = '60文字以下で入力してください';
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

            if (!contents.value.match(/[\S]+/)) {
                alert_text[2].textContent = 'お問い合わせ内容を入力してください';
                textArea[0].style.border = '1px solid #e33339';
                flg = true;
            } else {
                if (contents.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                    alert_text[2].textContent = '特殊文字は入力できません';
                    textArea[0].style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (contents.value.length > 1000) {
                        alert_text[2].textContent = '1000文字以下で入力してください';
                        textArea[0].style.border = '1px solid #e33339';
                        flg = true;
                    }
                }
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
                        alert_text[0].textContent = 'お名前を入力してください';
                        inputs[0].parentNode.style.border = '1px solid #e33339';
                    } else {
                        if (name.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                            alert_text[0].textContent = '特殊文字は入力できません';
                            inputs[0].parentNode.style.border = '1px solid #e33339';
                        } else {
                            if (name.value.length > 60) {
                                alert_text[0].textContent = '60文字以下で入力してください';
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

                contents.addEventListener('input', () => {
                    if (!contents.value.match(/[\S]+/)) {
                        alert_text[2].textContent = 'お問い合わせ内容を入力してください';
                        textArea[0].style.border = '1px solid #e33339';
                    } else {
                        if (contents.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                            alert_text[2].textContent = '特殊文字は入力できません';
                            textArea[0].style.border = '1px solid #e33339';
                        } else {
                            if (contents.value.length > 1000) {
                                alert_text[2].textContent = '1000文字以下で入力してください';
                                textArea[0].style.border = '1px solid #e33339';
                            } else {
                                alert_text[2].textContent = '';
                                textArea[0].style.border = '1px solid #696969';
                            }
                        }
                    }
                })
            } else {
                var replaceCon3 = contents.value.replace(/[^\S\n]+/g, '');
                var replaceCon2 = replaceCon3.replace(/\n{3,}/g, "\n\n");
                var replaceCon = replaceCon2.replace(/^[\n]+/g, "");
                var replaceName = name.value.replace(/^\s+/g, '');
                $.ajax({
                    type: "POST",
                    url: "ajax_contact_done.php",
                    datatype: "json",
                    data: {
                        "email": email.value,
                        "name": replaceName,
                        "contents": replaceCon,
                    }
                }).done((data) => {
                    $('.content').text(`${data}にお問い合わせ完了のメールを送信しました`);
                    modalBg[1].classList.remove('close');
                    modalCan[1].addEventListener('click', () => {
                        location.href = './index.php';
                    })
                });

                return false;
            }
        }
    </script>
    <script src="./js/index.js"></script>
</body>

</html>