<?php

$ip_address = $_SERVER['SERVER_ADDR'];

function terminalinfo()
{
    $terminal = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strstr($terminal, 'edg')) {
        $browser = 'Edge';
    } elseif (strstr($terminal, 'trident') || strstr($terminal, 'msie')) {
        $browser = 'Internet Explorer';
    } elseif (strstr($terminal, 'chrome')) {
        $browser = 'Chrome';
    } elseif (strstr($terminal, 'firefox')) {
        $browser = 'Firefox';
    } elseif (strstr($terminal, 'safari')) {
        $browser = 'Safari';
    } elseif (strstr($terminal, 'opera')) {
        $browser = 'Opera';
    } else {
        $browser = '不明';
    }


    if (strstr($terminal, 'windows')) {
        $os = 'Windows';
    } elseif (strstr($terminal, 'macintosh')) {
        $os = 'Mac';
    } elseif (strstr($terminal, 'linux')) {
        $os = 'Linux';
    } elseif (strstr($terminal, 'ipad')) {
        $os = 'iPad';
    } elseif (strstr($terminal, 'iphone')) {
        $os = 'iPhone';
    } elseif (strstr($terminal, 'android') || strstr($terminal, 'mobile')) {
        $os = 'Android';
    } elseif (strstr($terminal, 'nintendo wiiu')) {
        $os = 'Wii';
    } elseif (strstr($terminal, 'playstation')) {
        $os = 'PlayStation';
    } elseif (strstr($terminal, 'nintendo switch')) {
        $os = 'Nintendo Switch';
    } elseif (strstr($terminal, 'playstation vita')) {
        $os = 'PS Vita';
    } elseif (strstr($terminal, 'new nintendo 3ds')) {
        $os = '3DS';
    } elseif (strstr($terminal, 'xbox one')) {
        $os = 'XBox';
    } elseif (strstr($terminal, 'docomo')) {
        $os = 'docomo';
    } elseif (strstr($terminal, 'kddi')) {
        $os = 'au';
    } elseif (strstr($terminal, 'Vodafone') || strstr($terminal, 'SoftBank')) {
        $os = 'SoftBank';
    } else {
        $os = '不明';
    }

    return $browser . ' on ' . $os;
}
