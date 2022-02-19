<?php
session_start();
$_SESSION = array();
if (isset($_COOKIE[session_name()]) == true) {
    setcookie(session_name(), '', time() - 42000, '/');
}

session_destroy();
setcookie('paspost_id', '', time() - 42000);

header('Location:index.php');
