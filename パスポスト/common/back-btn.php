<?php
$hostname = $_SERVER['HTTP_HOST'];
if (!empty($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'], $hostname) !== false)) {
    echo '<button onclick="history.back()" class="back-btn">BACK</button>';
}
