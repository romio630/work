<?php
require_once('./common/sanitize.php');
require_once('./common/dbconnect.php');
$post = sanitize($_POST);
$id = $post['id'];

$sql = 'DELETE FROM follow WHERE following_id=?  OR follower_id=?';
$stmt = $dbh->prepare($sql);
$data[] = $id;
$data[] = $id;
$stmt->execute($data);
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = 'SELECT letter_id FROM good_list WHERE giver_id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
while ($letter = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $giver_letter[] = $letter['letter_id'];
}
if (!isset($giver_letter)) {
    $giver_letter = null;
}

if ($giver_letter != null) {
    for ($i = 0; $i < count($giver_letter); $i++) {
        $sql = "UPDATE letter SET good=good-1 WHERE id=?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($giver_letter[$i]));
    }
}

$sql = 'SELECT letter_id FROM comment WHERE user_id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
while ($comment = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $commented_letter[] = $comment['letter_id'];
}
if (!isset($commented_letter)) {
    $commented_letter = null;
}

if ($commented_letter != null) {
    for ($i = 0; $i < count($commented_letter); $i++) {
        $sql = "UPDATE letter SET comment=comment-1 WHERE id=?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($commented_letter[$i]));
    }
}

$sql = 'DELETE FROM good_list WHERE giver_id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = 'DELETE FROM draft WHERE user_id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = 'DELETE FROM login WHERE user_id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = 'DELETE FROM notification WHERE to_id=? or from_id=?';
$stmt = $dbh->prepare($sql);
$data = [];
$data[] = $id;
$data[] = $id;
$stmt->execute($data);
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = 'DELETE FROM hide_status WHERE to_id=? or from_id=?';
$stmt = $dbh->prepare($sql);
$data = [];
$data[] = $id;
$data[] = $id;
$stmt->execute($data);
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = 'DELETE FROM comment WHERE user_id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = 'DELETE FROM pp_user WHERE id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = 'DELETE FROM letter WHERE user_id=?';
$stmt = $dbh->prepare($sql);
$stmt->execute(array($id));
$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = null;
$dbh = null;

session_start();
$_SESSION = array();
if (isset($_COOKIE[session_name()]) == true) {
    setcookie(session_name(), '', time() - 42000, '/');
}

session_destroy();

header('Content-type: application/json');
