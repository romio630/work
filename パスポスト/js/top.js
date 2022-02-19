var windowwidth = window.innerWidth;
if(windowwidth < 769){
    var responsiveImage = [
        {src:'./img/sp_main01.jpg'},
        {src:'./img/sp_main02.jpg'},
        {src:'./img/sp_main03.jpg'},
        {src:'./img/sp_main04.jpg'}
    ];
}else{
    var responsiveImage = [
        {src:'./img/main01.jpg'},
        {src:'./img/main02.jpg'},
        {src:'./img/main03.jpg'},
        {src:'./img/main04.jpg'}
    ];
}

$('#slider').vegas({
    transition:'fade2',
    transitionDuration:1500,
    delay:6000,
    animation:'slideright',
    animationDuration:10000,
    timer:false,
    slides: responsiveImage,
})

$(window).on('load',()=>{
    $('.main-text').animate({
        'opacity':1,
        'padding-top':0
    },1000,function(){
        $(this).addClass('active');
        $('#slider').addClass('active');
    });
})

