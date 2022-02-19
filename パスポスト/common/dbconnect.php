<?php
try {
    $dsn = 'mysql:dbname=paspost;host=localhost;charset=utf8';
    $user = 'romio';
    $password = 'romio630';
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    file_put_contents('./dberror.txt', $e->getMessage());
    echo 'ただいま、障害によりご迷惑をお掛けしております。';
    die();
}
