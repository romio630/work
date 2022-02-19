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
    $('.user-modal-bg').removeClass('close');
    return false;
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
    modalBg[2].classList.remove('close');
    $('.user-modal-bg').removeClass('close');
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
    $('.user-modal-bg').removeClass('close');
    return false;
});

$('.list-request-cancel-btn').on('click', function() {
    const followId = $(this).attr('data-id');
    const nickName = $(this).attr('data-name');
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
        modalBg[2].classList.add('close');
    });
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
    modalBg[3].classList.remove('close');
    $('.user-modal-bg').removeClass('close');
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
    modalBg[4].classList.remove('close');
    $('.user-modal-bg').removeClass('close');
    return false;
});

$('.list-hide-unfollow-btn').on('click', function() {
    const followId = $(this).attr('data-id');
    const nickName = $(this).attr('data-name');
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
        modalBg[3].classList.add('close');
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
            "unfollowid": followId,
            "userid": userid,
            "nickname": nickName
        }
    }).done((data) => {
        $(`.list-unfollow-confirm[data-id="${followId}"]`).parent().append(data);
        $(`.list-unfollow-confirm[data-id="${followId}"]`).remove();
        modalBg[4].classList.add('close');
    });
    return false;
});