<?php
date_default_timezone_set('Asia/Tokyo');

function comment_time($time_db)
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
            $time   = date("Y.n.j", $unix);
        } else {
            $time   = date("n.j", $unix);
        }

        return $time;
    }

    return (int)$time . $unit;
}
