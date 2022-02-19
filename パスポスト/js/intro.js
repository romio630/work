const lines = $('.intro').text().split("\n");
const length = $('.intro').text().length;
let windowWidth = window.innerWidth;
let flg=false;
if(lines.length>3){
    for(let i=3;i<lines.length;i++){
        if(lines[i]!=""){
            flg=true;
            break;
        }
    }
}else{
    flg=false;
}
if(windowWidth > 768){
    if (flg==true || length >= 100) {
        $('.user-profile').append('<div class="more-intro-outer"><button class="more-intro">もっと見る<img src="./img/down.svg"></button></div>');
    }else{
        $('.intro').attr('style', '-webkit-line-clamp: 10;');
    }
    $('.more-intro').on('click', function() {
        $('.intro').attr('style', '-webkit-line-clamp: 10;');
        $(this).hide();
    })
}else{
    if (flg==true || length >= 90) {
        $('.user-profile').append('<div class="more-intro-outer"><button class="more-intro">もっと見る<img src="./img/down.svg"></button></div>');
    }else{
        $('.intro').attr('style', '-webkit-line-clamp: 30;');
    }
    $('.more-intro').on('click', function() {
        $('.intro').attr('style', '-webkit-line-clamp: 30;');
        $(this).hide();
    })
}
lines[4]