<?php
session_start();
session_regenerate_id(true);

if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/dbconnect.php');
    require_once('./common/function.php');
    $login = 1;
    $id = $_SESSION['id'];
    $nickname = $_SESSION['nickname'];
    $icon = $_SESSION['icon'];
    $id_json = json_encode($id);

    require_once('./common/sql.php');

    $sql = "SELECT * from questionnaire WHERE user_id=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    $already_write = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = null;
    $dbh = null;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>アンケート -パスポスト</title>
    <?php include_once('./views/head.html') ?>
    <link rel="stylesheet" href="./css/form.css">
</head>

<body>
    <?php include_once('./views/header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div id="form" class="form-wrapper">
                <h2>アンケート</h2>
                <div class="form-contents">
                    <?php if ($already_write) { ?>
                        <div class="thanks">
                            <div><img src="./img/logo.svg" alt="パスポストのロゴ"></div>
                            <p>アンケートにご協力ありがとうございました。</p>
                        </div>
                    <?php } else { ?>
                        <dl>
                            <dt><label for="name">お名前</label></dt>
                            <dd>
                                <div class="text-input readonly">
                                    <input type="text" name="nickname" id="nickname" value="<?= $nickname ?>" readonly>
                                </div>
                            </dd>
                            <dt><label for="contents">パスポストに対する感想、バグの発見、実装してほしい機能など</label><span class="option">任意</span></dt>
                            <dd>
                                <textarea name="contents" id="contents"></textarea>
                                <div class="input-assistance">
                                    <p class="alert-text"></p>
                                    <span class="contents-length">0/1000</span>
                                </div>
                            </dd>

                            <dt class="question-star-title">このサイトを評価してください<span class="mandatory">必須</span></dt>
                            <dd class="question-star-content">
                                <input type="radio" value="1" name="star" id="1"><label for="1"></label>
                                <input type="radio" value="2" name="star" id="2"><label for="2"></label>
                                <input type="radio" value="3" name="star" id="3"><label for="3"></label>
                                <input type="radio" value="4" name="star" id="4"><label for="4"></label>
                                <input type="radio" value="5" name="star" id="5"><label for="5"></label>
                                <p class="alert-text"></p>
                            </dd>
                        </dl>
                        <input type="submit" value="送信する" id="button">
                    <?php } ?>
                </div>
                <?php include_once('./views/user-window.php') ?>
                <div class="modal-bg close">
                    <div class="success-modal modal">
                        <p class="header">ご協力いただきありがとうございました</p>
                        <p>いただいたアンケートは確認させていただき、パスポストのサービス向上に役立たせていただきます。</p>
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
    <script src="./js/function.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/input-focus-nobtn.js"></script>
    <script>
        'use strict';
        const login = '<?= $login ?>';
        const jsonId = JSON.parse('<?= $id_json ?>');
        const contents = document.querySelector('#contents');
        const star = document.getElementsByName('star');
        inputlength(contents, '.contents-length', 1000);

        $('input[name="star"]').on('click', () => {
            $('.checked').removeClass('checked');
            for (let i = 0; i < star.length; i++) {
                if (star[i].checked) {
                    for (let j = i; j >= 0; j--) {
                        star[j].classList.add('checked');
                    }
                    break;
                }
            }
        })

        const button = document.querySelector('#button');
        button.addEventListener('click', (event) => {
            const alert_text = document.querySelectorAll('.alert-text');
            let flg = false;

            if (contents.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                alert_text[0].textContent = '特殊文字は入力できません';
                textArea[0].style.border = '1px solid #e33339';
                flg = true;
            } else {
                if (contents.value.length > 1000) {
                    alert_text[0].textContent = '1000文字以下で入力してください';
                    textArea[0].style.border = '1px solid #e33339';
                    flg = true;
                }
            }

            for (let i = 0; i < star.length; i++) {
                if (star[i].checked) {
                    var starValue = star[i].value;
                    break;
                }
            }
            if (starValue == undefined) {
                alert_text[1].textContent = '評価してもらえると飛んで喜びます';
                flg = true;
            }

            if (flg) {
                event.preventDefault();

                contents.addEventListener('input', () => {
                    if (contents.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                        alert_text[0].textContent = '特殊文字は入力できません';
                        textArea[0].style.border = '1px solid #e33339';
                    } else {
                        if (contents.value.length > 1000) {
                            alert_text[0].textContent = '1000文字以下で入力してください';
                            textArea[0].style.border = '1px solid #e33339';
                        } else {
                            alert_text[0].textContent = '';
                            textArea[0].style.border = '1px solid #696969';
                        }
                    }
                })
            } else {
                const star = document.getElementsByName('star');
                for (let i = 0; i < star.length; i++) {
                    if (star[i].checked) {
                        var starValue = star[i].value;
                        break;
                    }
                }

                var replaceCon3 = contents.value.replace(/^[^\S\n]+/g, "");
                var replaceCon2 = replaceCon3.replace(/\n{3,}/g, "\n\n");
                var replaceCon = replaceCon2.replace(/^[\n]+/g, "");
                $.ajax({
                    type: "POST",
                    url: "ajax_questionnaire_done.php",
                    datatype: "json",
                    data: {
                        "id": jsonId,
                        "nickname": $('#nickname').val(),
                        "contents": replaceCon,
                        "star": starValue,
                    }
                }).fail((data) => {
                    modalBg[1].classList.remove('close');
                    modalCan[1].addEventListener('click', () => {
                        location.href = './index.php';
                    })
                });

                return false;
            }

        })
    </script>
    <script src="./js/index.js"></script>
</body>

</html>