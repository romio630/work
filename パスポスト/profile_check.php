<?php
require_once('./common/login_check.php');
require_once('./common/sanitize.php');

$id = $_SESSION['id'];
$post = sanitize($_POST);
$nickname = $post['nickname'];
$enc_nickname = openssl_encrypt($nickname, 'aes-256-ecb', 'hogehoge');
$replace_nickname = preg_replace('/^\s+/u', '', $nickname);
$intro = $post['intro'];
$enc_intro = openssl_encrypt($intro, 'aes-256-ecb', 'hogehoge');
$replace_intro3 = preg_replace('/^[^\S\n]+/u', "", $intro);
$replace_intro2 = preg_replace('/\n{3,}/u', "\n\n", $replace_intro3);
$replace_intro = preg_replace('/^[\n]+/u', "", $replace_intro2);
$icon = $_FILES['icon'];
$enc_icon = openssl_encrypt($icon['name'], 'aes-256-ecb', 'hogehoge');
$hide_status = $post['hide-status'];
$counter = 0;

require_once('./common/dbconnect.php');

$sql = 'SELECT word FROM ng_word';
$stmt = $dbh->query($sql);
while ($rec = $stmt->fetch()) {
    $word_list[] = $rec['word'];
}

foreach ($word_list as $word) {
    if (strpos($replace_nickname, $word) !== false) {
        $counter++;
        break;
    }
}
foreach ($word_list as $word) {
    if (strpos($replace_intro, $word) !== false) {
        $counter += 2;
        break;
    }
}

if ($counter > 0) {
    if ($counter == 1) {
        if ($icon['size'] > 1000000) {
            header('Location:profile.php?e=2&n=' . $enc_nickname);
            exit;
        } else {
            header('Location:profile.php?e=5&n=' . $enc_nickname);
            exit;
        }
    } elseif ($counter == 2) {
        if ($icon['size'] > 1000000) {
            header('Location:profile.php?e=3&i=' . $enc_intro);
            exit;
        } else {
            header('Location:profile.php?e=6&i=' . $enc_intro);
            exit;
        }
    } else {
        if ($icon['size'] > 1000000) {
            header('Location:profile.php?e=4&n=' . $enc_nickname . '&i=' . $enc_intro);
            exit;
        } else {
            header('Location:profile.php?e=7&n=' . $enc_nickname . '&i=' . $enc_intro);
            exit;
        }
    }
} else {
    if ($icon['size'] > 0) {
        if ($icon['size'] > 1000000) {
            header('Location:profile.php?e=1');
            exit;
        } else {
            move_uploaded_file($icon['tmp_name'], './icon/' . $icon['name']);
            if ($_SESSION['icon'] != 'initial.svg') {
                unlink('./icon/' . $_SESSION['icon']);
            }
            $_SESSION['icon'] = $icon['name'];
        }
    }
}

if ($hide_status == 2) {
    $sql = 'UPDATE pp_user SET hide=? WHERE id=?';
    $stmt = $dbh->prepare($sql);
    $data[] = 2;
    $data[] = $id;
    $stmt->execute($data);

    $sql = 'INSERT INTO hide_status(from_id,to_id,status) VALUES(?,?,?)';
    $stmt = $dbh->prepare($sql);
    $data = [];
    $data[] = $id;
    $data[] = $id;
    $data[] = 1;
    $stmt->execute($data);

    $sql = 'SELECT follower_id FROM follow WHERE following_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($id));
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row[] = $rec;
    }

    for ($i = 0; $i < count($row); $i++) {
        $sql = "INSERT INTO hide_status(from_id,to_id,status) values(?,?,?)";
        $stmt = $dbh->prepare($sql);
        $data = [];
        $data[] = $row[$i]['follower_id'];
        $data[] = $id;
        $data[] = 1;
        $stmt->execute($data);
    }
} else {
    $sql = 'UPDATE pp_user SET hide=? WHERE id=?';
    $stmt = $dbh->prepare($sql);
    $data[] = 1;
    $data[] = $id;
    $stmt->execute($data);

    $sql = 'DELETE FROM hide_status WHERE to_id=?';
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$id]);
}

$sql = 'UPDATE pp_user SET nickname=?,intro=?,icon=? WHERE id=?';
$stmt = $dbh->prepare($sql);
$data = [];
$data[] = $replace_nickname;
$data[] = $replace_intro;
$data[] = $_SESSION['icon'];
$data[] = $id;
$stmt->execute($data);

$sql = 'UPDATE letter SET nickname=? WHERE user_id=?';
$stmt = $dbh->prepare($sql);
$data = [];
$data[] = $replace_nickname;
$data[] = $id;
$stmt->execute($data);

$stmt = null;
$dbh = null;

$_SESSION['nickname'] = $replace_nickname;

if ($icon['name'] != '') {
    $_SESSION['icon'] = $icon['name'];
}

setcookie('ajax', 'profile-check', time() + 1);
header('Location:./configuration.php');
exit;
