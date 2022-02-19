if(login==1){
    const userWin = document.getElementById('user-window');
    const account = document.getElementById('account');

    addEventListener('click', (e) => {
        if (e.target != userWin && e.target != account) {
            userWin.classList.add('close');
        }
    });

    $('#account').on('click', function() {
        $('#user-window').toggleClass('close');
    })

    $('.logout').on('click', function() {
        modalBg[0].classList.remove('close');
        $('#user-window').addClass('close');
    })

    $('.tab-list .tab').on('click', function() {
        $('.active-bar').css({
            'left': $(this).attr('data-left')
        })
    });

    const tab = document.getElementsByClassName('tab');
    const panel = document.getElementsByClassName('panel');
    for (let i = 0; i < tab.length; i++) {
        tab[i].addEventListener('click', () => {

            if (!tab[i].classList.contains('active')) {
                document.getElementsByClassName('active')[0].classList.remove('active');
                document.getElementsByClassName('show')[0].classList.remove('show');
                tab[i].classList.add('active');
                panel[i].classList.add('show');
            }

        });
    };
    // いいね！ボタン
    $(document).on('click','.good-btn',function() {
        let thisClass=$(this).attr('data-class');
        $.ajax({
            type: "POST",
            url: "ajax_good_insert.php",
            data: {
                "letterid": $(this).attr('data-id'),
                "comment": $(this).attr('data-comment'),
                "good": $(this).attr('data-good'),
                "userid": userid
            }
        }).done((data) => {
            $(this).parent().html(data);
        });
        return false;
    });
    $(document).on('click','.dl-good-btn',function() {
        let letterid=$(this).attr('data-id');
        $.ajax({
            type: "POST",
            url: "ajax_good_delete.php",
            data: {
                "letterid": $(this).attr('data-id'),
                "comment": $(this).attr('data-comment'),
                "good": $(this).attr('data-good'),
                "userid": userid
            }
        }).done((data) => {
            $(this).parent().html(data);
        });
        return false;
    });


    $(document).on('click','.letter-good-btn',function() {
        $.ajax({
            type: "POST",
            url: "ajax_letter_good_insert.php",
            data: {
                "letterid": $(this).attr('data-id'),
                "userid": userid
            }
        }).done((data) => {
            $('.reaction').html(data);
            $('.letter-dl-good-btn').addClass('clicked');
        });
        return false;
    });
    $(document).on('click','.letter-dl-good-btn',function() {
        $.ajax({
            type: "POST",
            url: "ajax_letter_good_delete.php",
            data: {
                "letterid": $(this).attr('data-id'),
                "userid": userid,
            }
        }).done((data) => {
            $('.reaction').html(data);
            $('.letter-good-btn').addClass('clicked');
        });
        return false;
    });

    // 報告ボタン

    $('.edit-btn').on('click', function() {
        let letterId = $(this).attr('data-id');
        location.href = `./edit.php?id=${letterId}`;
        return false;
    })
    
    $('.report-user-modal-btn').on('click',function(){
        modalBg[2].classList.remove('close');
        document.addEventListener('touchmove', disableScroll, {
            passive: false
        });
        $('body').css('overflow', 'hidden');
    })

    $('.report-modal-btn').on('click', function() {
        var letterId = $(this).attr('data-id');
        modalBg[1].classList.remove('close');
        document.addEventListener('touchmove', disableScroll, {
            passive: false
        });
        $('body').css('overflow', 'hidden');
        $('.sp-report-bg').addClass('close');
        $('#report-form').attr('data-id', letterId);
        return false;
    })
    
    $('.report-btn').on('click', function() {
        var reason = document.getElementById('reason').value;
        var letterId = $('#report-form').attr('data-id');
        $.ajax({
            type: "POST",
            url: "ajax_report_letter.php",
            datatype: "json",
            data: {
                "userid": userid,
                "letterid": letterId,
                "reason": reason,
            }
        }).fail((data) => {
            location.reload();
            // modalBg[1].classList.remove('close');
            // $('.notice').remove();
            // $('body').append('<div class="notice"><img src="./img/check-red.svg">手紙を報告しました</div>');
        });
    })
    
    $('.user-report-btn').on('click', function() {
        var reason = document.getElementById('user-reason').value;
        $.ajax({
            type: "POST",
            url: "ajax_report_user.php",
            datatype: "json",
            data: {
                "userid": userid,
                "toid": followid,
                "reason": reason,
            }
        }).fail((data) => {
            location.reload();
        });
    })

    let windowWidth = window.innerWidth;
    if (windowWidth < 769) {

        $('.user-menu-btn').on('click',function(){
            $('.sp-modal-bg').removeClass('close');
        });

        $('.sp-report-btn').on('click', function(){
            $('.sp-report-bg').removeClass('close');
            return false;
        });
        $('.sp-edit-btn').on('click', function(){
            $('.sp-edit-bg').removeClass('close');
            return false;
        });
    }else{
        $('.report-btn-bg').on('click', function() {
            let letterId = $(this).attr('data-id');
            $('.report-btn-bg:not([data-id=' + letterId + '])').next().removeClass('active');
            $(this).next().toggleClass('active');

            if ($(this).next().hasClass('active')) {
                addEventListener('click', (e) => {
                    if (e.target !== $(this).next()) {
                        $(this).next().removeClass('active');
                    }
                });
            }
            return false;
        })

        $('.user-menu-btn').on('click',function(){
            $(this).next().toggleClass('active');
        
            if ($(this).next().hasClass('active')) {
                addEventListener('click', (e) => {
                    if (e.target !== $(this).next()) {
                        $(this).next().removeClass('active');
                    }
                });
            }
            return false;
        })
    }

}