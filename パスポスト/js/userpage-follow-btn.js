$(document).on('click', '.follow-btn', function() {
    $.ajax({
        type: "POST",
        url: "ajax_follow.php",
        data: {
            "followid": followid,
            "userid": userid
        }
    }).done((data) => {
        let windowWidth = window.innerWidth;
        if (windowWidth < 769) {
            $(this).remove();
            $('.sp-user-btn').prepend(data);
        } else {
            $(this).remove();
            $('.user-btn').prepend(data);
        }
    });
    return false;
});

$('.unfollow-btn').on('click', function() {
    $.ajax({
        type: "POST",
        url: "ajax_unfollow.php",
        data: {
            "unfollowid": followid,
            "userid": userid
        }
    }).done((data) => {
        let windowWidth = window.innerWidth;
        if (windowWidth < 769) {
            $(this).remove();
            $('.sp-user-btn').prepend(data);
        } else {
            $('.unfollow-confirm').remove();
            $('.user-btn').prepend(data);
        }
        modalBg[7].classList.add('close');
    });
    return false;
});

$(document).on('click', '.list-follow-btn', function() {
    const followId = $(this).attr('data-id');
    const nickName = $(this).attr('data-name');
    $.ajax({
        type: "POST",
        url: "ajax_list_follow.php",
        data: {
            "followid": followId,
            "userid": userid,
            "nickname": nickName,
        }
    }).done((data) => {
        $(this).parent().append(data);
        $(this).remove();
    });
    return false;
});

$('.list-unfollow-btn').on('click', function() {
    const followId = $(this).attr('data-id');
    const nickName = $(this).attr('data-name');
    $.ajax({
        type: "POST",
        url: "ajax_list_unfollow.php",
        data: {
            "unfollowid":  followId,
            "userid": userid,
            "nickname":nickName
        }
    }).done((data) => {
        $(`.list-unfollow-confirm[data-id="${followId}"]`).parent().append(data);
        $(`.list-unfollow-confirm[data-id="${followId}"]`).remove();
        modalBg[8].classList.add('close');
    });
    return false;
});

$(document).on('click', '.follow-request-btn', function() {
    $.ajax({
        type: "POST",
        url: "ajax_follow_request.php",
        data: {
            "followid": followid,
            "userid": userid
        }
    }).done((data) => {
        let windowWidth = window.innerWidth;
        if (windowWidth < 769) {
            $(this).remove();
            $('.sp-user-btn').prepend(data);
            $('.notice').remove();
            $('body').append(`<div class="notice nocheck">${userNickName}さんへフォローリクエストが送信され、承認待ちになりました。</div>`);
        } else {
            $(this).remove();
            $('.user-btn').prepend(data);
            $('.notice').remove();
            $('body').append(`<div class="notice nocheck">${userNickName}さんへフォローリクエストが送信され、承認待ちになりました。</div>`);
        }
    });
    return false;
});

$(document).on('click', '.list-follow-request-btn', function() {
    const followId = $(this).attr('data-id');
    const nickName = $(this).attr('data-name');
    $.ajax({
        type: "POST",
        url: "ajax_list_follow_request.php",
        data: {
            "followid": followId,
            "userid": userid,
            "nickname": nickName,
        }
    }).done((data) => {
        $(this).parent().append(data);
        $(this).remove();
        $('.notice').remove();
        $('body').append(`<div class="notice nocheck">${nickName}さんへフォローリクエストが送信され、承認待ちになりました。</div>`);
    });
    return false;
});

$(document).on('click', '.unapproved-btn', function() {
    modalBg[3].classList.remove('close');
});

$(document).on('click', '.list-unapproved-btn', function() {
    const followId = $(this).attr('data-id');
    const nickName = $(this).attr('data-name');
    $('.header').text('フォローリクエストを破棄');
    $('.modal-text').text(`未承認のフォローリクエストがキャンセルされ、${nickName}さんには表示されなくなります。`);
    $('.list-request-cancel-btn').attr({
        'data-id': followId,
        'data-name': nickName,
    });
    modalBg[4].classList.remove('close');
    return false;
});

$('.request-cancel-btn').on('click', function() {
    $.ajax({
        type: "POST",
        url: "ajax_follow_request_cancel.php",
        data: {
            "followid": followid,
            "userid": userid
        }
    }).done((data) => {
        let windowWidth = window.innerWidth;
        if (windowWidth < 769) {
            $('.unapproved-btn').remove();
            $('.sp-user-btn').prepend(data);
        } else {
            $('.unapproved-btn').remove();
            $('.user-btn').prepend(data);
        }
        modalBg[3].classList.add('close');
    });
    return false;
});

$('.list-request-cancel-btn').on('click', function() {
    const followId = $(this).attr('data-id');
    const nickName = $(this).attr('data-name');
    console.log(followId);
    console.log(userid);
    $.ajax({
        type: "POST",
        url: "ajax_list_follow_request_cancel.php",
        data: {
            "followid": followId,
            "userid": userid,
            "nickname": nickName,
        }
    }).done((data) => {
        $(`.list-unapproved-btn[data-id="${followId}"]`).parent().append(data);
        $(`.list-unapproved-btn[data-id="${followId}"]`).remove();
        modalBg[4].classList.add('close');
    });
    return false;
});

$(document).on('click', '.hide-unfollow-confirm', function() {
    modalBg[5].classList.remove('close');
    return false;
});
$(document).on('click', '.list-hide-unfollow-confirm', function() {
    const followId = $(this).attr('data-id');
    const nickName = $(this).attr('data-name');
    $('.header').text(`${nickName}さんをフォロー解除`);
    $('.list-hide-unfollow-btn').attr({
        'data-id': followId,
        'data-name': nickName,
    });
    modalBg[6].classList.remove('close');
    return false;
});
$(document).on('click', '.unfollow-confirm', function() {
    modalBg[7].classList.remove('close');
    return false;
});
$(document).on('click', '.list-unfollow-confirm', function() {
    const followId = $(this).attr('data-id');
    const nickName = $(this).attr('data-name');
    $('.header').text(`${nickName}さんをフォロー解除`);
    $('.list-unfollow-btn').attr({
        'data-id': followId,
        'data-name': nickName,
    });
    modalBg[8].classList.remove('close');
    return false;
});

$('.hide-unfollow-btn').on('click', function() {
    $.ajax({
        type: "POST",
        url: "ajax_hide_unfollow.php",
        data: {
            "unfollowid": followid,
            "userid": userid
        }
    }).done((data) => {
        let windowWidth = window.innerWidth;
        if (windowWidth < 769) {
            $('.hide-unfollow-confirm').remove();
            $('.sp-user-btn').prepend(data);
        } else {
            $('.hide-unfollow-confirm').remove();
            $('.user-btn').prepend(data);
        }
        modalBg[5].classList.add('close');
    });
    return false;
});
$('.list-hide-unfollow-btn').on('click', function() {
    const followId = $(this).attr('data-id');
    $.ajax({
        type: "POST",
        url: "ajax_list_hide_unfollow.php",
        data: {
            "unfollowid": followId,
            "userid": userid,
            "nickname": nickName,
        }
    }).done((data) => {
        $(`.list-hide-unfollow-confirm[data-id="${followId}"]`).parent().append(data);
        $(`.list-hide-unfollow-confirm[data-id="${followId}"]`).remove();
        modalBg[6].classList.add('close');
    });
    return false;
});