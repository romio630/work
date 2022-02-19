<?php
session_start();
session_regenerate_id(true);

if (isset($_COOKIE['paspost_id'])) {
    require_once('./common/category.php');
    require_once('./common/dbconnect.php');
    require_once('./common/function.php');
    $login = 1;
    $id = $_SESSION['id'];
    $id_json = json_encode($id);
    $nickname = $_SESSION['nickname'];
    $nickname_json = json_encode($nickname);
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
    <title>手紙の投函 -パスポスト</title>
</head>

<body>
    <?php include_once('./views/search-header.php') ?>
    <main>
        <?php include_once('./common/back-btn.php') ?>
        <?php if (isset($_COOKIE['paspost_id'])) { ?>
            <div id="posting" class="form-wrapper">
                <h2>手紙の投函</h2>
                <div class="form-contents">
                    <dl>
                        <dt>年齢</dt>
                        <dd class="age">
                            <div class="age-content">
                                <div class="age-select">
                                    <select id="cur-age" name="cur-age">
                                        <option selected disabled>現在</option>
                                        <?php for ($i = 16; $i <= 120; $i++) { ?>
                                            <option value="<?= $i ?>"><?= $i ?>歳</option>
                                        <?php } ?>
                                        <option value="200">非公表</option>
                                    </select>
                                </div>
                                <div class="mail-to">
                                    <span class="send"><img src="./img/send.svg"></span>
                                </div>
                                <div class="age-select">
                                    <select id="pos-age" name="pos-age">
                                        <option selected disabled>過去</option>
                                        <?php for ($i = 10; $i <= 120; $i++) { ?>
                                            <option value="<?= $i ?>"><?= $i ?>歳</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <p class="alert-text"></p>
                        </dd>
                        <dt>ジャンル</dt>
                        <dd>
                            <ul>
                                <?php for ($i = 0; $i < count($category_list); $i++) : ?>
                                    <li>
                                        <input type="radio" id="<?= $en_category_list[$i] ?>" name="category" value="<?= $i ?>"><label for="<?= $en_category_list[$i] ?>"><?= $category_list[$i] ?></label>
                                    </li>
                                <?php endfor ?>
                            </ul>
                            <p class="alert-text"></p>
                        </dd>
                        <dt>本文</dt>
                        <dd>
                            <textarea name="message" id="message"></textarea>
                            <div class="input-assistance">
                                <p class="alert-text"></p>
                                <span class="message-length">0/1000</span>
                            </div>
                        </dd>
                    </dl>
                    <input type="submit" id="posting-btn" value="投函する">
                    <button id="draft-add-btn">下書きに保存する</button>
                </div>
                <?php include_once('./views/user-window.php') ?>
                <div class="modal-bg close">
                    <div class="success-modal modal">
                        <p class="header">投函が完了しました</p>
                        <p>投函した手紙は「<a href="./mypage_top.php">マイページ</a>」からいつでも確認することができます</p>
                        <button class="modal-cancel">トップに戻る</button>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <?php include_once('./views/notlogin.html') ?>
        <?php } ?>
    </main>
    <?php include_once('./views/footer.html') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="./js/function.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/input-focus.js"></script>
    <script>
        const login = '<?= $login ?>';
        var userid = JSON.parse('<?= $id_json ?>');
        var nickname = JSON.parse('<?= $nickname_json ?>');
        const message = document.querySelector('#message');
        const curAge = document.querySelector('#cur-age');
        const posAge = document.querySelector('#pos-age');
        const category = document.getElementsByName('category');
        const button = document.querySelector('#posting-btn');

        inputlength(message, '.message-length', 1000);

        modalCan[1].addEventListener('click', () => {
            location.href = './index.php';
        })

        $('#draft-add-btn').on('click', function() {
            var replaceMes = message.value.replace(/[^\S\n]+/g, '');
            for (let i = 0; i < category.length; i++) {
                if (category[i].checked) {
                    var categoryValue = category[i].value;
                    break;
                }
            }
            if (categoryValue == undefined) {
                var categoryValue = 8;
            }
            $.ajax({
                type: "POST",
                url: "ajax_draft_add.php",
                datatype: "json",
                data: {
                    "userid": userid,
                    "nickname": nickname,
                    "message": replaceMes,
                    "cur_age": $('#cur-age').val(),
                    "pos_age": $('#pos-age').val(),
                    "category": categoryValue,
                }
            }).fail((data) => {
                location.href = `./draft_top.php?id=${userid}`;
            });

            return false;
        });

        button.addEventListener('click', (event) => {
            const alert_text = document.querySelectorAll('.alert-text');
            let flg = false;

            if (curAge.value == '現在' || posAge.value == '過去') {
                alert_text[0].textContent = '年齢を選択してください';
                flg = true;
            } else {
                if (Number(curAge.value) <= Number(posAge.value)) {
                    alert_text[0].textContent = '過去の自分へ手紙を書いてください';
                    flg = true;
                }
            }

            for (let i = 0; i < category.length; i++) {
                if (category[i].checked) {
                    var categoryValue = category[i].value;
                    break;
                }
            }
            if (categoryValue == undefined) {
                alert_text[1].textContent = 'ジャンルを選んでください';
                flg = true;
            }

            if (!message.value.match(/[\S]+/)) {
                alert_text[2].textContent = '本文を入力してください';
                textArea[0].style.border = '1px solid #e33339';
                flg = true;
            } else {
                if (message.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                    alert_text[2].textContent = '特殊文字は入力できません';
                    textArea[0].style.border = '1px solid #e33339';
                    flg = true;
                } else {
                    if (message.value.length < 6) {
                        alert_text[2].textContent = '文字数が少なすぎます';
                        textArea[0].style.border = '1px solid #e33339';
                        flg = true;
                    } else {
                        if (message.value.length > 1000) {
                            alert_text[2].textContent = '1000文字以下で入力してください';
                            textArea[0].style.border = '1px solid #e33339';
                            flg = true;
                        }
                    }
                }
            }

            if (flg) {

                curAge.addEventListener('change', () => {
                    if (curAge.value == '現在' || posAge.value == '過去') {
                        alert_text[0].textContent = '年齢を選択してください';
                    } else {
                        if (Number(curAge.value) <= Number(posAge.value)) {
                            alert_text[0].textContent = '過去の自分へ手紙を書いてください';
                        } else {
                            alert_text[0].textContent = '';
                        }
                    }
                })

                posAge.addEventListener('change', () => {
                    if (curAge.value == '現在' || posAge.value == '過去') {
                        alert_text[0].textContent = '年齢を選択してください';
                    } else {
                        if (Number(curAge.value) <= Number(posAge.value)) {
                            alert_text[0].textContent = '過去の自分へ手紙を書いてください';
                        } else {
                            alert_text[0].textContent = '';
                        }
                    }
                })

                category.forEach(
                    r => r.addEventListener('change', () => {
                        alert_text[1].textContent = '';
                    })
                )

                message.addEventListener('input', () => {
                    if (!message.value.match(/[\S]+/)) {
                        alert_text[2].textContent = '本文を入力してください';
                        textArea[0].style.border = '1px solid #e33339';
                    } else {
                        if (message.value.match(/[\u2700-\u27BF]|[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/)) {
                            alert_text[2].textContent = '特殊文字は入力できません';
                            textArea[0].style.border = '1px solid #e33339';
                        } else {
                            if (message.value.length < 6) {
                                alert_text[2].textContent = '文字数が少なすぎます';
                                textArea[0].style.border = '1px solid #e33339';
                            } else {
                                if (message.value.length > 1000) {
                                    alert_text[2].textContent = '1000文字以下で入力してください';
                                    textArea[0].style.border = '1px solid #e33339';
                                } else {
                                    alert_text[2].textContent = '';
                                    textArea[0].style.border = '1px solid #696969';
                                }
                            }
                        }
                    }
                })
            } else {
                var replaceMes3 = message.value.replace(/^[^\S\n]+/g, "");
                var replaceMes2 = replaceMes3.replace(/\n{3,}/g, "\n\n");
                var replaceMes = replaceMes2.replace(/^[\n]+/g, "");

                for (let i = 0; i < category.length; i++) {
                    if (category[i].checked) {
                        var categoryValue = category[i].value;
                        break;
                    }
                }

                $.ajax({
                    type: "POST",
                    url: "ajax_ngword_check.php",
                    datatype: "json",
                    data: {
                        "check1": replaceMes,
                    }
                }).done((data) => {
                    if (data == 1) {
                        var wordCheck = 1;
                        $.ajax({
                            type: "POST",
                            url: "ajax_posting_done.php",
                            datatype: "json",
                            data: {
                                "wordcheck": wordCheck,
                                "userid": userid,
                                "nickname": nickname,
                                "message": replaceMes,
                                "cur_age": $('#cur-age').val(),
                                "pos_age": $('#pos-age').val(),
                                "category": categoryValue,
                            }
                        }).fail((data) => {
                            modalBg[1].classList.remove('close');
                        });
                    } else {
                        var wordCheck = 0;
                        $.ajax({
                            type: "POST",
                            url: "ajax_posting_done.php",
                            datatype: "json",
                            data: {
                                "wordcheck": wordCheck,
                                "userid": userid,
                                "nickname": nickname,
                                "message": replaceMes,
                                "cur_age": $('#cur-age').val(),
                                "pos_age": $('#pos-age').val(),
                                "category": categoryValue,
                            }
                        }).fail((data) => {
                            modalBg[1].classList.remove('close');
                        });
                    }
                });

                return false;
            }

        })
    </script>
    <script src="./js/index.js"></script>
</body>

</html>