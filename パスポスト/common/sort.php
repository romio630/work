<?php
$sql = "SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,L.edit,U.icon,U.hide 
FROM letter as L join pp_user as U on L.user_id=U.id WHERE ng_word=0 and U.hide=1 ";
$cnt_sql = "SELECT count(*) as cnt FROM letter as L join pp_user as U on L.user_id=U.id WHERE ng_word=0 and U.hide=1 ";
$sort = "";
$x = 0;
$y = 0;

if (isset($_GET['sort'])) {
    $get = sanitize($_GET);
    $cur_above = $get['cur'];
    $pos_above = $get['pos'];
    if (isset($_COOKIE['id'])) {
        $user = $get['user'];
    }
    if (isset($get['category'])) {
        $category = $get['category'];
    } else {
        $category = '';
    }
    $order = $get['order'];
    $is_category = false;

    if ($user == 2) {
        $sql .= "and L.user_id in(select following_id from follow where follower_id=?) ";
        $cnt_sql .= "and L.user_id in(select following_id from follow where follower_id=?) ";
        $sort .= "and L.user_id in(select following_id from follow where follower_id=?) ";
        $x++;
        $y++;
    }

    if ($cur_above != '' && $pos_above != '') {
        if ($cur_above != 0) {
            $cur_below = $cur_above + 9;
        } else {
            $cur_below = 0;
        }
        $pos_below = $pos_above + 9;
        $x = $x + 4;
        $y = $y + 4;
        $sql .= "and cur_age between ? and ? AND pos_age between ? and ? ";
        $cnt_sql .= "and cur_age between ? and ? AND pos_age between ? and ? ";
        $sort .= "and cur_age between ? and ? AND pos_age between ? and ? ";
    } elseif ($cur_above != '' && $pos_above == '') {
        if ($cur_above != 0) {
            $cur_below = $cur_above + 9;
        } else {
            $cur_below = 0;
        }
        $x = $x + 2;
        $y = $y + 2;
        $sql .= "and cur_age between ? and ? ";
        $cnt_sql .= "and cur_age between ? and ? ";
        $sort .= "and cur_age between ? and ? ";
        $is_cur = true;
    } elseif ($cur_above == '' && $pos_above != '') {
        $pos_below = $pos_above + 9;
        $x = $x + 2;
        $y = $y + 2;
        $sql .= "and pos_age between ? and ? ";
        $cnt_sql .= "and pos_age between ? and ? ";
        $sort .= "and pos_age between ? and ? ";
        $is_cur = false;
    }

    if ($category != '' && $category != 8) {
        $is_category = true;
        $x++;
        $y++;
        $sql .= "and L.category=? ";
        $cnt_sql .= "and L.category=? ";
        $sort .= "and L.category=? ";
    }

    $sql .= " UNION ALL SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,L.edit,U.icon,U.hide 
    FROM letter as L join pp_user as U on L.user_id=U.id join hide_status as H on L.user_id=H.to_id WHERE L.ng_word=0 and H.status=1 and H.from_id=? ";
    $sql .= $sort;
    $cnt_sql .= " UNION ALL SELECT count(*) as cnt FROM letter as L join pp_user as U on L.user_id=U.id join hide_status as H on L.user_id=H.to_id 
    WHERE L.ng_word=0 and H.status=1 and H.from_id=? ";
    $cnt_sql .= $sort;

    if ($order == 1) {
        $sql .= " ORDER BY created_at DESC LIMIT ?,8";
        $x++;
    } else {
        $sql .= " ORDER BY good DESC LIMIT ?,8";
        $x++;
    }
    // echo var_dump($sql) . '<br><br>';
    $stmt = $dbh->prepare($sql);
    switch ($x) {
        case 1:
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $start, PDO::PARAM_INT);
            break;
        case 2:
            if ($is_category) {
                $stmt->bindParam(1, $category, PDO::PARAM_INT);
                $stmt->bindParam(2, $id, PDO::PARAM_INT);
                $stmt->bindParam(3, $category, PDO::PARAM_INT);
                $stmt->bindParam(4, $start, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $id, PDO::PARAM_INT);
                $stmt->bindParam(3, $id, PDO::PARAM_INT);
                $stmt->bindParam(4, $start, PDO::PARAM_INT);
            }
            break;
        case 3:
            if ($is_category) {
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $category, PDO::PARAM_INT);
                $stmt->bindParam(3, $id, PDO::PARAM_INT);
                $stmt->bindParam(4, $id, PDO::PARAM_INT);
                $stmt->bindParam(5, $category, PDO::PARAM_INT);
                $stmt->bindParam(6, $start, PDO::PARAM_INT);
            } else {
                if ($is_cur) {
                    $above = $cur_above;
                    $below = $cur_below;
                } else {
                    $above = $pos_above;
                    $below = $pos_below;
                }
                $stmt->bindParam(1, $above, PDO::PARAM_INT);
                $stmt->bindParam(2, $below, PDO::PARAM_INT);
                $stmt->bindParam(3, $id, PDO::PARAM_INT);
                $stmt->bindParam(4, $above, PDO::PARAM_INT);
                $stmt->bindParam(5, $below, PDO::PARAM_INT);
                $stmt->bindParam(6, $start, PDO::PARAM_INT);
            }
            break;
        case 4:
            if ($is_cur) {
                $above = $cur_above;
                $below = $cur_below;
            } else {
                $above = $pos_above;
                $below = $pos_below;
            }
            if ($is_category) {
                $stmt->bindParam(1, $above, PDO::PARAM_INT);
                $stmt->bindParam(2, $below, PDO::PARAM_INT);
                $stmt->bindParam(3, $category, PDO::PARAM_INT);
                $stmt->bindParam(4, $id, PDO::PARAM_INT);
                $stmt->bindParam(5, $above, PDO::PARAM_INT);
                $stmt->bindParam(6, $below, PDO::PARAM_INT);
                $stmt->bindParam(7, $category, PDO::PARAM_INT);
                $stmt->bindParam(8, $start, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $above, PDO::PARAM_INT);
                $stmt->bindParam(3, $below, PDO::PARAM_INT);
                $stmt->bindParam(4, $id, PDO::PARAM_INT);
                $stmt->bindParam(5, $id, PDO::PARAM_INT);
                $stmt->bindParam(6, $above, PDO::PARAM_INT);
                $stmt->bindParam(7, $below, PDO::PARAM_INT);
                $stmt->bindParam(8, $start, PDO::PARAM_INT);
            }
            break;
        case 5:
            if ($is_category) {
                if ($is_cur) {
                    $above = $cur_above;
                    $below = $cur_below;
                } else {
                    $above = $pos_above;
                    $below = $pos_below;
                }
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $above, PDO::PARAM_INT);
                $stmt->bindParam(3, $below, PDO::PARAM_INT);
                $stmt->bindParam(4, $category, PDO::PARAM_INT);
                $stmt->bindParam(5, $id, PDO::PARAM_INT);
                $stmt->bindParam(6, $id, PDO::PARAM_INT);
                $stmt->bindParam(7, $above, PDO::PARAM_INT);
                $stmt->bindParam(8, $below, PDO::PARAM_INT);
                $stmt->bindParam(9, $category, PDO::PARAM_INT);
                $stmt->bindParam(10, $start, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(2, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(3, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(4, $pos_below, PDO::PARAM_INT);
                $stmt->bindParam(5, $id, PDO::PARAM_INT);
                $stmt->bindParam(6, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(7, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(8, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(9, $pos_below, PDO::PARAM_INT);
                $stmt->bindParam(10, $start, PDO::PARAM_INT);
            }
            break;
        case 6:
            if ($is_category) {
                $stmt->bindParam(1, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(2, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(3, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(4, $pos_below, PDO::PARAM_INT);
                $stmt->bindParam(5, $category, PDO::PARAM_INT);
                $stmt->bindParam(6, $id, PDO::PARAM_INT);
                $stmt->bindParam(7, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(8, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(9, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(10, $pos_below, PDO::PARAM_INT);
                $stmt->bindParam(11, $category, PDO::PARAM_INT);
                $stmt->bindParam(12, $start, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(3, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(4, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(5, $pos_below, PDO::PARAM_INT);
                $stmt->bindParam(6, $id, PDO::PARAM_INT);
                $stmt->bindParam(7, $id, PDO::PARAM_INT);
                $stmt->bindParam(8, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(9, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(10, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(11, $pos_below, PDO::PARAM_INT);
                $stmt->bindParam(12, $start, PDO::PARAM_INT);
            }
            break;
        case 7:
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $cur_above, PDO::PARAM_INT);
            $stmt->bindParam(3, $cur_below, PDO::PARAM_INT);
            $stmt->bindParam(4, $pos_above, PDO::PARAM_INT);
            $stmt->bindParam(5, $pos_below, PDO::PARAM_INT);
            $stmt->bindParam(6, $category, PDO::PARAM_INT);
            $stmt->bindParam(7, $id, PDO::PARAM_INT);
            $stmt->bindParam(8, $id, PDO::PARAM_INT);
            $stmt->bindParam(9, $cur_above, PDO::PARAM_INT);
            $stmt->bindParam(10, $cur_below, PDO::PARAM_INT);
            $stmt->bindParam(11, $pos_above, PDO::PARAM_INT);
            $stmt->bindParam(12, $pos_below, PDO::PARAM_INT);
            $stmt->bindParam(13, $category, PDO::PARAM_INT);
            $stmt->bindParam(14, $start, PDO::PARAM_INT);
            break;
    }

    $stmt->execute();
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row[] = $rec;
    }

    // echo var_dump($cnt_sql);
    $stmt = $dbh->prepare($cnt_sql);
    switch ($y) {
        case 0:
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            break;
        case 1:
            if ($is_category) {
                $stmt->bindParam(1, $category, PDO::PARAM_INT);
                $stmt->bindParam(2, $id, PDO::PARAM_INT);
                $stmt->bindParam(3, $category, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $id, PDO::PARAM_INT);
                $stmt->bindParam(3, $id, PDO::PARAM_INT);
            }
            break;
        case 2:
            if ($is_category) {
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $category, PDO::PARAM_INT);
                $stmt->bindParam(3, $id, PDO::PARAM_INT);
                $stmt->bindParam(4, $id, PDO::PARAM_INT);
                $stmt->bindParam(5, $category, PDO::PARAM_INT);
            } else {
                if ($is_cur) {
                    $above = $cur_above;
                    $below = $cur_below;
                } else {
                    $above = $pos_above;
                    $below = $pos_below;
                }
                $stmt->bindParam(1, $above, PDO::PARAM_INT);
                $stmt->bindParam(2, $below, PDO::PARAM_INT);
                $stmt->bindParam(3, $id, PDO::PARAM_INT);
                $stmt->bindParam(4, $above, PDO::PARAM_INT);
                $stmt->bindParam(5, $below, PDO::PARAM_INT);
            }
            break;
        case 3:
            if ($is_cur) {
                $above = $cur_above;
                $below = $cur_below;
            } else {
                $above = $pos_above;
                $below = $pos_below;
            }
            if ($is_category) {
                $stmt->bindParam(1, $above, PDO::PARAM_INT);
                $stmt->bindParam(2, $below, PDO::PARAM_INT);
                $stmt->bindParam(3, $category, PDO::PARAM_INT);
                $stmt->bindParam(4, $id, PDO::PARAM_INT);
                $stmt->bindParam(5, $above, PDO::PARAM_INT);
                $stmt->bindParam(6, $below, PDO::PARAM_INT);
                $stmt->bindParam(7, $category, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $above, PDO::PARAM_INT);
                $stmt->bindParam(3, $below, PDO::PARAM_INT);
                $stmt->bindParam(4, $id, PDO::PARAM_INT);
                $stmt->bindParam(5, $id, PDO::PARAM_INT);
                $stmt->bindParam(6, $above, PDO::PARAM_INT);
                $stmt->bindParam(7, $below, PDO::PARAM_INT);
            }
            break;
        case 4:
            if ($is_category) {
                if ($is_cur) {
                    $above = $cur_above;
                    $below = $cur_below;
                } else {
                    $above = $pos_above;
                    $below = $pos_below;
                }
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $above, PDO::PARAM_INT);
                $stmt->bindParam(3, $below, PDO::PARAM_INT);
                $stmt->bindParam(4, $category, PDO::PARAM_INT);
                $stmt->bindParam(5, $id, PDO::PARAM_INT);
                $stmt->bindParam(6, $id, PDO::PARAM_INT);
                $stmt->bindParam(7, $above, PDO::PARAM_INT);
                $stmt->bindParam(8, $below, PDO::PARAM_INT);
                $stmt->bindParam(9, $category, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(2, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(3, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(4, $pos_below, PDO::PARAM_INT);
                $stmt->bindParam(5, $id, PDO::PARAM_INT);
                $stmt->bindParam(6, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(7, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(8, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(9, $pos_below, PDO::PARAM_INT);
            }
            break;
        case 5:
            if ($is_category) {
                $stmt->bindParam(1, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(2, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(3, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(4, $pos_below, PDO::PARAM_INT);
                $stmt->bindParam(5, $category, PDO::PARAM_INT);
                $stmt->bindParam(6, $id, PDO::PARAM_INT);
                $stmt->bindParam(7, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(8, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(9, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(10, $pos_below, PDO::PARAM_INT);
                $stmt->bindParam(11, $category, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->bindParam(2, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(3, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(4, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(5, $pos_below, PDO::PARAM_INT);
                $stmt->bindParam(6, $id, PDO::PARAM_INT);
                $stmt->bindParam(7, $id, PDO::PARAM_INT);
                $stmt->bindParam(8, $cur_above, PDO::PARAM_INT);
                $stmt->bindParam(9, $cur_below, PDO::PARAM_INT);
                $stmt->bindParam(10, $pos_above, PDO::PARAM_INT);
                $stmt->bindParam(11, $pos_below, PDO::PARAM_INT);
            }
            break;
        case 6:
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->bindParam(2, $cur_above, PDO::PARAM_INT);
            $stmt->bindParam(3, $cur_below, PDO::PARAM_INT);
            $stmt->bindParam(4, $pos_above, PDO::PARAM_INT);
            $stmt->bindParam(5, $pos_below, PDO::PARAM_INT);
            $stmt->bindParam(6, $category, PDO::PARAM_INT);
            $stmt->bindParam(7, $id, PDO::PARAM_INT);
            $stmt->bindParam(8, $id, PDO::PARAM_INT);
            $stmt->bindParam(9, $cur_above, PDO::PARAM_INT);
            $stmt->bindParam(10, $cur_below, PDO::PARAM_INT);
            $stmt->bindParam(11, $pos_above, PDO::PARAM_INT);
            $stmt->bindParam(12, $pos_below, PDO::PARAM_INT);
            $stmt->bindParam(13, $category, PDO::PARAM_INT);
            break;
    }
    $stmt->execute();
    $letter_number = 0;
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $letter_number += (int)$rec['cnt'];
    }
    $max_page = ceil($letter_number / 8);
} else {
    $sql = "SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,L.edit,U.icon,U.hide
        FROM letter as L join pp_user as U on L.user_id=U.id WHERE L.ng_word=0 and U.hide=1
        UNION SELECT L.id,L.nickname,L.cur_age,L.pos_age,L.message,L.created_at,L.user_id,L.good,L.comment,L.category,L.ng_word,L.edit,U.icon,U.hide
        FROM letter as L join pp_user as U on L.user_id=U.id where L.user_id in(select H.to_id from hide_status as H where H.status=1 and H.from_id=?) and L.ng_word=0 
        ORDER BY created_at DESC LIMIT ?,8";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->bindParam(2, $start, PDO::PARAM_INT);
    $stmt->execute();
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row[] = $rec;
    }

    $sql = 'SELECT count(*) as cnt FROM letter as L where L.user_id in(select U.id from pp_user as U where U.hide=1) and L.ng_word=0 
    UNION SELECT count(*) as cnt FROM letter as L where L.user_id in(select H.to_id from hide_status as H where H.status=1 and H.from_id=?) and L.ng_word=0';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $letter_number = 0;
    while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $letter_number += (int)$rec['cnt'];
    }
    $max_page = ceil($letter_number / 8);
}

$stmt = null;
$dbh = null;
