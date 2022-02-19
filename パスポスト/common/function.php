<?php
date_default_timezone_set('Asia/Tokyo');

function detail_time($time_db)
{
    $unix = strtotime($time_db);
    if ((int)date("H", $unix) >= 12) {
        $hour = (int)date("H", $unix) - 12;
        $time = date("午後" . $hour . ":i・Y年m月d日", $unix);
    } else {
        $time = date("午前H:i・Y年m月d日", $unix);
    }

    return $time;
}

function letter_time($time_db)
{
    $unix = strtotime($time_db);
    $now = time();
    $diff_sec = $now - $unix;

    if ($diff_sec < 60) {
        $time   = $diff_sec;
        $unit   = "秒前";
    } elseif ($diff_sec < 3600) {
        $time   = $diff_sec / 60;
        $unit   = "分前";
    } elseif ($diff_sec < 86400) {
        $time   = $diff_sec / 3600;
        $unit   = "時間前";
    } elseif ($diff_sec < 2678400) {
        $time   = $diff_sec / 86400;
        $unit   = "日前";
    } else {
        if (date("Y") != date("Y", $unix)) {
            $time   = date("Y年n月j日", $unix);
        } else {
            $time   = date("n月j日", $unix);
        }

        return $time;
    }

    return (int)$time . $unit;
}

function number_unit($number)
{
    if ($number >= 1000000) {
        $quotient = $number / 1000000;
        $unit = 'M';
        return number_format($quotient, 1) . $unit;
    } elseif ($number >= 1000) {
        $quotient = $number / 1000;
        $unit = 'K';
        return number_format($quotient, 1) . $unit;
    } else {
        $quotient = $number;
        $unit = '';
        return (int)$quotient . $unit;
    }
}
