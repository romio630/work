<?php
if (isset($_COOKIE['id'])) {
    $flg = true;
} else {
    $flg = false;
}

session_start();
session_regenerate_id(true);
