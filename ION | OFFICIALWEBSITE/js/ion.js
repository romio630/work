$(function(){

$(window).on('load',function(){
    $('html,body').animate({ scrollTop: 0 }, '1');
    $('#splash-logo').delay(800).fadeOut('slow');
    $('#splash').delay(1000).fadeOut('slow',function(){
        $('body').addClass('appear');

        if(window.innerWidth>768){
            $("#splashbg-top,#splashbg-bottom").css({
                "animation-name":"heightShrink"
            });
        }else{
            $("#splashbg-top").css({
                "animation-name":"heightShrinkSpTop"
            });
            $("#splashbg-bottom").css({
                "animation-name":"heightShrinkSpBottom"
            });
        }
        $("#splashbg-right,#splashbg-left").css({
            "animation-name":"widthShrink"
        });
        
    })

    $('#splashbg-top').on('animationend', function () {
        $('.svganimeblock svg path').css('opacity','1');
        $('.header-inner').css('transform','translateX(0)');
        VivusInit(); 
        VivusAnime();
    });
});


var logoVivus;

function VivusInit(){
	logoVivus = new Vivus('logo',
		{
			start: 'autostart',
            duration: 120 ,
			type: 'scenario',
            animTimingFunction: Vivus.EASE,
		},
		function(obj){
			$("#logo").attr("class", "done");
            $('#mv-logo').css('transform','translateX(0)');
            $('#inner-website').css('transform','translateX(0)');
		}
	);
	logoVivus.stop();		
}

function VivusAnime(){
	var elemPos = $("#logo").offset().top - 50;
	var scroll = $(window).scrollTop();
	var windowHeight = $(window).height();
	if (scroll >= elemPos - windowHeight) {
		logoVivus.play(1);
	}
}
    // メニューの開閉
    $(".toggle-btn").click(function(){
        $('body').toggleClass('open');
    })
    
    $('.menu a,.title').click(function(){
        $('body').removeClass('open');
    })

    // アコーディオンメニュー
    $("#ac-menu .movie-title").on("click", function() {
        $(this).next().next().slideToggle();
        $(this).toggleClass("open");
    });

    // ボタン関連
    $('nav a').click(function(){
        var id =$(this).attr('href');
        var sectionName=$(id);

        if(id ==="#movie"){
            var position =sectionName.offset().top - 150;
        }else{
            var position =sectionName.offset().top;
        }
        $('html,body').animate({
            'scrollTop':position
        },1000);
    })

    $('.section-item').click(function(){
        var id =$(this).attr('data-target');
        var sectionName=$(id);

        if(id ==="#movie"){
            var position =sectionName.offset().top - 150;
        }else{
            var position =sectionName.offset().top;
        }
        $('html,body').animate({
            'scrollTop':position
        },600);
    })

    $('#pagetop,#url').click(function(){
        $('html,body').animate({
            'scrollTop':0
        },1000);
    })

    //スクロールバー
    function scrollbarAnime(){
        $('.section-item').each(function(){
            var section = $($(this).attr('data-target'));
            var target = section.offset();
            console.log(target);
            var scroll = $(window).scrollTop();
    
            if(scroll > target){
                var H = section.outerHeight();
                var percent = (scroll - target)/H;
    
                if(percent >1){
                    percent =1;
                }
            }else{
                percent=0;
            }

            if(percent>0 && percent<1){
                $(this).addClass('current');
            }else{
                $(this).removeClass('current');
            }
    
            $(this).find('.section-bar-blue').css({
                'transform' : 'translateX(-1px) scaleY(' + percent + ')'
            })
        })
    }

    // スクロールイベント
    $(window).scroll(function() {
        var scroll =$(window).scrollTop();
    $('.scrolldown').each(function() {

      if (scroll > 80) {
        $(this).css('opacity','0');
      }else{
        $(this).css('opacity','1');
      }
    });

    if(window.innerWidth>768){
        
        $('#frontbox-top,#frontbox-bottom').each(function(){

            if(scroll > 100){
                $(this).css({
                    'height':'0',
                });
            }else{
                $(this).css({
                    'height':'calc((100vh - 43vw) / 2 * 1.18)',
                });
            }
        })
    
        $('#frontbox-right,#frontbox-left').each(function(){
    
            if(scroll > 100){
                $(this).css({
                    'width':'0',
                });
            }else{
                $(this).css({
                    'width':'15%',
                });
            }
        })
    }else{
        $('#frontbox-top').each(function(){
    
            if(scroll > 100){
                $(this).css({
                    'height':'0',
                });
            }else{
                $(this).css({
                    'height':'10%',
                });
            }
        })

        $('#frontbox-bottom').each(function(){
    
            if(scroll > 100){
                $(this).css({
                    'height':'0',
                });
            }else{
                $(this).css({
                    'height':'17%',
                });
            }
        })
    
        $('#frontbox-right,#frontbox-left').each(function(){
    
            if(scroll > 100){
                $(this).css({
                    'width':'0',
                });
            }else{
                $(this).css({
                    'width':'17%',
                });
            }
        })
    }

    $('#mv-title').each(function(){

        if(window.innerWidth>768){

            if(scroll > 288){
                $(this).css({
                    'position':'absolute',
                    'top':'102%',
                    'transform':'trasnlate(-50%,-102%)'
                });
            }else{
                $(this).css({
                    'position':'fixed',
                    'top':'50%',
                    'transform':'trasnlate(-50%,-50%)'
                });
            }
        }else{
            if(scroll > 460){
                $(this).css({
                    'position':'absolute',
                    'top':'121%',
                    'transform':'trasnlate(-50%,-121%)'
                });
            }else{
                $(this).css({
                    'position':'fixed',
                    'top':'50%',
                    'transform':'trasnlate(-50%,-50%)'
                });
            }
        }
    })
    // スクロールバー出現タイミング
    $('.section-item').each(function(){
        var point=$('#profile').offset().top - 100;
        var bar=$(this).children('.section-bar');
        var number=$(this).find('span');

        if(scroll > point){
            $(this).addClass('block');
            bar.removeClass('disappear');
            bar.addClass('appear');
            number.removeClass('disappear');
            number.addClass('appear');
        }else{
            bar.removeClass('appear');
            bar.addClass('disappear');
            number.removeClass('appear');
            number.addClass('disappear');
        }
    })

    // photo-area
    $('.photoarea-bg').each(function(){
        var start =$('.pin-spacer').offset().top;
        var end =$('#discography').offset().top;
        var bg=$('#profile-bg');

        if(scroll-end > 0){
            bg.fadeOut(50);
        }else if(scroll-start > 0){
            var goal = ($('.photo-area').outerHeight() + $(window).height())/2;
            var percent = (scroll - start) / goal * 100;

            if(window.innerWidth>768){
                var width = 100 - percent*0.72
                var height = 100 - percent*0.2
                var radius = percent*0.5
    
                if(width<28){
                    width=28;
                }
                if(height<80){
                    height=80;
                }
                if(radius >50){
                    radius=50;
                    bg.fadeIn(500);
                }else{
                    bg.fadeOut(500);
                }

            }else{
                var width = 100 - percent*0.42
                var height = 100 - percent*0.64
                var radius = percent*0.5
    
                if(width<58){
                    width=58;
                }
                if(height<36){
                    height=36;
                }
                if(radius >50){
                    radius=50;
                    bg.fadeIn(500);
                }else{
                    bg.fadeOut(500);
                }
            }
        }else{
            width=100;
            height=100;
            radius=0;
            bg.fadeOut(50);
        }

        $(this).css({
            'width': width + '%',
            'height': height +'%',
            'border-radius':radius +'%'
        });   
    });

    // フローティングメニュー
    $('#buttons-inner').each(function(){
        var buttons=$(this).parent('#buttons').offset().top;
        var height=$(this).parent('#buttons').outerHeight();
        var top= scroll - buttons;

        if(top > 0 && top < height-162){
            $(this).stop().animate({
                'top':top
            },500);
        }else if(top < 0){
            $(this).css('top','0');
        }  
    })

    scrollbarAnime();
    
    });

//   MOVIEモーダルウィンドウ
$('.movie-play').modaal({
    background:'rgb(50, 197, 255)',
    overlay_opacity:.9,
    type:'video',
    overlay_close:true,
    before_open: function() {
        if(window.innerWidth <=768){
            $('.toggle-btn').css({
            'opacity':0,
            'visibility':'hidden'
        });
        }
    },
　  before_close: function() {
        $('.toggle-btn').css({
            'opacity':1,
            'visibility':'visible'
        });
　  }
})

// NEWSフィルタリング
var element=$('#move-point');
var now=element.css('top');
$("button").on('click',function(){
    let target=$(this).attr("value");
    let element=$('#news');
    let position=$('.news-wrapper').offset().top;

    $('#buttons-inner').stop().animate({
        'top':'0'
    },500);
    $('html,body').animate({
        'scrollTop':position
    },500);

    if(window.innerWidth>768){
        if(target ==='all'){
            element.css('height','115.078vw');
        }else if(target ==='release'){
            element.css('height','61.797vw');
        }else{
            element.css('height','61.953vw');
        }
    }else{
        if(target ==='all'){
            element.css('height','256.267vw');
        }else if(target ==='release'){
            element.css('height','144.53vw');
        }else{
            element.css('height','145.6vw');
        }
    }

    if(target === 'media'){
        $('.media-top').css('border-top','1px dotted #333');
    }else{
        $('.media-top').css('border-top','none');
    }

    $('.current').removeClass('current');
    $(this).children().addClass('current');
    now=$('#move-point').css('top');

    $(".select").removeClass("select");
    $(this).addClass("select");

    $(".news-list li").each(function(){
         $(this).hide();

        if($(this).hasClass(target) || target=="all"){ 
            $(this).show();
        }
    })
    
})

// NEWSホバーポイント

$('button').hover(function(){
    var position =$(this).attr('data-top');
    element.css('top',position);
},function(){
    element.css('top',now);
});

// NEWSホバー画像
if(window.innerWidth>768){
    $(window).mousemove(function(e){
        $('.hover-image').each(function(){
            var element=$(this).find('span');
            var height =$(this).height()*2.6;
            var cursor =$(window).width()*0.078
    
            if(e.pageX > $(this).offset().left && e.pageX < $(this).offset().left + $(this).width() && e.pageY > $(this).offset().top && e.pageY <= $(this).offset().top + height){
    
                var posX=e.pageX -cursor;
                var posY=e.pageY -cursor;
                var image=$(this).attr('data-image');
                
                element.css({
                    "top":posY,
                    "left":posX,
                    "background-image":"url("+ image +")"
                })
                element.show();
    
            }else{
                element.hide();
            }
        })
    })
}

// NEWS SP画像
$('.newsleft-sp').each(function(){
    var image=$(this).parent('.hover-image').attr('data-image');
    $(this).css('background-image','url('+ image +')');
})

// DISCOGRAPHY スライダー
$('#album-slider').slick({
    autoplay:true,
    dots:true,
    autoplaySpeed:4000,
    arrows:false,

});

$('#single-slider').slick({
    autoplay:true,
    dots:true,
    autoplaySpeed:3000,
    arrows:false,

});

});

// スクロールアニメーション
ScrollTrigger.matchMedia({

    "(min-width: 769px)": function() {
        // Lyrics
        gsap.fromTo('.lyrics-left', { 
            autoAlpha:0,
            y:100,
        },{
            autoAlpha:1,
            y:0,
            duration: 0.8,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '#lyrics',
                start: 'start center',
            },
        });

        gsap.fromTo('.lyrics-animation1', { 
            autoAlpha:0,
            y:20,
        },{
            autoAlpha:1,
            y:0,
            duration: 1,
            ease:"power3.out",
            delay:0.6,
              scrollTrigger: {
                trigger: '#lyrics',
                start: 'start center',
            },
            stagger: {
                from: "start", 
                amount: 0.5
            }
        });
        gsap.fromTo('.lyrics-animation2', { 
            autoAlpha:0,
            y:20
        },{
            autoAlpha:1,
            y:0,
            duration: 1,
            ease:"power3.out",
            delay:1.6,
              scrollTrigger: {
                trigger: '#lyrics',
                start: 'start center',
            },
            stagger: {
                from: "start", 
                amount: 0.5
            }
        });
        gsap.fromTo('.lyrics-animation3', { 
            autoAlpha:0,
            y:20
        },{
            autoAlpha:1,
            y:0,
            duration: 1,
            ease:"power3.out",
            delay:2.35,
              scrollTrigger: {
                trigger: '#lyrics',
                start: 'start center',
            },
            stagger: {
                from: "start", 
                amount: 0.5
            }
        });
        gsap.fromTo('.lyrics-animation4', { 
            autoAlpha:0,
            y:20
        },{
            autoAlpha:1,
            y:0,
            duration: 1,
            ease:"power3.out",
            delay:3.1,
              scrollTrigger: {
                trigger: '#lyrics',
                start: 'start center',
            },
            stagger: {
                from: "start", 
                amount: 0.5
            }
        });
        gsap.fromTo('.lyrics-animation5', { 
            autoAlpha:0,
            y:20,
        },{
            autoAlpha:1,
            y:0,
            duration: 1,
            ease:"power3.out",
            delay:3.85,
              scrollTrigger: {
                trigger: '#lyrics',
                start: 'start center',
            },
            stagger: {
                from: "start", 
                amount: 0.5
            }
        });
        // PROFILE
        gsap.fromTo('.profile-img', { 
            x: 1000, 
        },{
            x: 0, 
            duration: 0.8,
            ease:"circ.out",
              scrollTrigger: {
                trigger: '.flex-profile',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.profile-info', { 
            scaleX: 0, 
        },{
            scaleX: 1, 
            duration: 0.8,
            ease:"circ.out",
              scrollTrigger: {
                trigger: '.flex-profile',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.name', { 
            y: -100, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 0.8,
            ease:"power4.out",
            delay:0.4,
              scrollTrigger: {
                trigger: '.flex-profile',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.text', { 
            y: 100, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 0.8,
            ease:"power4.out",
            delay:0.8,
              scrollTrigger: {
                trigger: '.flex-profile',
                start: 'start 50%',
            }
        });
        // photo-area
        gsap.fromTo('.myname', { 
            x:window.innerWidth*1.4,
        },{
            x:-window.innerWidth,
            scrollTrigger:{
                trigger:'.photo-area',
                start:'center center',
                end: '+=7000',
                pin:true,
                scrub:true,
            }
        });

        gsap.fromTo('.category-title1', { 
            y: -100, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 1,
            ease:"power4.out",
            delay:0.4,
              scrollTrigger: {
                trigger: '.album',
                start: 'start 50%',
            }
        });

        gsap.fromTo('.category-title2', { 
            y: -100, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 1,
            ease:"power4.out",
            delay:0.4,
              scrollTrigger: {
                trigger: '.single',
                start: 'start 50%',
            }
        });

    },
    "(max-width: 768px)": function(){
        // profile
        gsap.fromTo('.profile-img', { 
            x: 500, 
        },{
            x: 0, 
            duration: 0.8,
            ease:"power1.out",
              scrollTrigger: {
                trigger: '.flex-profile',
                start: 'start 80%',
            }
        });
        gsap.fromTo('.profile-info', { 
            scaleX: 0, 
        },{
            scaleX: 1, 
            duration: 0.8,
            ease:"power1.out",
              scrollTrigger: {
                trigger: '.flex-profile',
                start: 'start 60%',
            }
        });
        gsap.fromTo('.name', { 
            y: -100, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 0.8,
            ease:"power1.out",
              scrollTrigger: {
                trigger: '.flex-profile',
                start: 'start 40%',
            }
        });
        gsap.fromTo('.text', { 
            y: 100, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 0.8,
            ease:"power1.out",
              scrollTrigger: {
                trigger: '.flex-profile',
                start: 'start 20%',
            }
        });

        gsap.fromTo('.myname', { 
            x:window.innerWidth*2,
        },{
            x:-window.innerWidth*2,
            scrollTrigger:{
                trigger:'.photo-area',
                start:'center center',
                end: '+=9000',
                pin:true,
                scrub:true,
            }
        });

        // ALBUM
        gsap.fromTo('.category-title1', { 
            y: -50, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 1,
            ease:"power4.out",
            delay:0.4,
              scrollTrigger: {
                trigger: '.album',
                start: 'start 50%',
            }
        });

        // SINGLE
        gsap.fromTo('.category-title2', { 
            y: -50, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 1,
            ease:"power4.out",
            delay:0.4,
              scrollTrigger: {
                trigger: '.single',
                start: 'start 50%',
            }
        });
        // NEWS
        gsap.fromTo('.news-wrapper', { 
            scaleX: 0, 
        },{
            scaleX: 1, 
            duration: 0.8,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '#news',
                start: 'top 50%',
            }
        });

        gsap.fromTo('#buttons', { 
            y: 100, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 0.8,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '#buttons',
                start: 'start 60%',
            }
        });

        gsap.fromTo('.news-list', { 
            y: 100, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 0.8,
            ease:"power3.out",
            delay:0.4,
              scrollTrigger: {
                trigger: '#buttons',
                start: 'start 60%',
            }
        });
    },
    "all": function() {
        ScrollTrigger.create({
            trigger: '.class-trigger1', 
            start: 'center 70%',
            toggleClass: {targets: ".text-anime1", className: "is-active"}, 
            once: true,
        });

        ScrollTrigger.create({
            trigger: '.class-trigger2', 
            start: 'center 70%',
            toggleClass: {targets: ".text-anime2", className: "is-active"}, 
            once: true
        });
        ScrollTrigger.create({
            trigger: '.class-trigger3', 
            start: 'center 55%',
            toggleClass: {targets: ".text-anime3", className: "is-active"}, 
            once: true,
        });
        ScrollTrigger.create({
            trigger: '.class-trigger4', 
            start: 'center 70%',
            toggleClass: {targets: ".text-anime4", className: "is-active"}, 
            once: true
        });
        ScrollTrigger.create({
            trigger: '.class-trigger5', 
            start: 'center 70%',
            toggleClass: {targets: ".text-anime5", className: "is-active"}, 
            once: true
        });
        ScrollTrigger.create({
            trigger: '.class-trigger6', 
            start: 'center 70%',
            toggleClass: {targets: ".text-anime6", className: "is-active"}, 
            once: true
        });
        ScrollTrigger.create({
            trigger: '.class-trigger7', 
            start: 'center 70%',
            toggleClass: {targets: ".text-anime7", className: "is-active"}, 
            once: true
        });
        ScrollTrigger.create({
            trigger: '.class-trigger8', 
            start: 'center 70%',
            toggleClass: {targets: ".text-anime8", className: "is-active"}, 
            once: true
        });
        ScrollTrigger.create({
            trigger: '.class-trigger9', 
            start: 'center 70%',
            toggleClass: {targets: ".text-anime9", className: "is-active"}, 
            once: true
        });
        ScrollTrigger.create({
            trigger: '.class-trigger10', 
            start: 'center 70%',
            toggleClass: {targets: ".text-anime10", className: "is-active"}, 
            once: true
        });

        // discpgraphy
        gsap.fromTo('.discography-wrapper', { 
            scaleX: 0, 
        },{
            scaleX: 1, 
            duration: 0.8,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '#discography',
                start: 'top 50%',
            }
        });

        // ALBUM
        gsap.fromTo('.title-box1', { 
            scaleX:0,
        },{
            scaleX:1,
            duration: 1.1,
            ease:"power4.out",
              scrollTrigger: {
                trigger: '.album',
                start: 'start 50%',
            }
        });

        gsap.fromTo('#album-slider', { 
            x: 100, 
            autoAlpha: 0,
        },{
            x: 0, 
            autoAlpha: 1,
            duration: 1,
            ease:"power4.out",
              scrollTrigger: {
                trigger: '.album',
                start: 'start 50%',
            }
        });

        // SINGLE
        gsap.fromTo('.title-box2', { 
            scaleX:0,
        },{
            scaleX:1,
            duration: 1.1,
            ease:"power4.out",
              scrollTrigger: {
                trigger: '.single',
                start: 'start 50%',
            }
        });
        gsap.fromTo('#single-slider', { 
            x: -100, 
            autoAlpha: 0,
        },{
            x: 0, 
            autoAlpha: 1,
            duration: 1,
            ease:"power4.out",
              scrollTrigger: {
                trigger: '.single',
                start: 'start 50%',
            }
        });

        // MOVIE
        // １段目
        gsap.fromTo('.movie-box1', { 
            scaleX:0
        },{
            scaleX: 1, 
            duration: 1.1,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger1',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.movie1', { 
            y: -80, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 1,
            delay:0.2,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger1',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.movie-contents1', { 
            x: 100, 
            autoAlpha: 0,
        },{
            x: 0, 
            autoAlpha: 1,
            duration: 1,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger1',
                start: 'start 50%',
            }
        });
        // ２段目
        gsap.fromTo('.movie-box2', { 
            scaleX:0
        },{
            scaleX: 1, 
            duration: 1.1,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger2',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.movie2', { 
            y: -80, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 1,
            delay:0.2,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger2',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.movie-contents2', { 
            x: 100, 
            autoAlpha: 0,
        },{
            x: 0, 
            autoAlpha: 1,
            duration: 1,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger2',
                start: 'start 50%',
            }
        });
        // ３段目
        gsap.fromTo('.movie-box3', { 
            scaleX:0
        },{
            scaleX: 1, 
            duration: 1.1,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger3',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.movie3', { 
            y: -80, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 1,
            delay:0.2,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger3',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.movie-contents3', { 
            x: 100, 
            autoAlpha: 0,
        },{
            x: 0, 
            autoAlpha: 1,
            duration: 1,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger3',
                start: 'start 50%',
            }
        });
        // ４段目
        gsap.fromTo('.movie-box4', { 
            scaleX:0
        },{
            scaleX: 1, 
            duration: 1.1,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger4',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.movie4', { 
            y: -80, 
            autoAlpha: 0,
        },{
            y: 0, 
            autoAlpha: 1,
            duration: 1,
            delay:0.2,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger4',
                start: 'start 50%',
            }
        });
        gsap.fromTo('.movie-contents4', { 
            x: 100, 
            autoAlpha: 0,
        },{
            x: 0, 
            autoAlpha: 1,
            duration: 1,
            ease:"power3.out",
              scrollTrigger: {
                trigger: '.movie-trigger4',
                start: 'start 50%',
            }
        });

    }

}); 
