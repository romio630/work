const modalBg = document.getElementsByClassName('modal-bg');
const modalCan = document.getElementsByClassName('modal-cancel');
function disableScroll(event) {
    event.preventDefault();
}

for (let i = 0; i < modalBg.length; i++) {
    modalBg[i].addEventListener('click', () => {
        if (!modalBg[i].classList.contains('close')) {
            modalBg[i].classList.add('close');
            $('body').css('overflow', 'visible');
            document.removeEventListener('touchmove', disableScroll, { passive: false });
        }
    });
};

for (let i = 0; i < modalCan.length; i++) {
    modalCan[i].addEventListener('click', () => {
        if (!modalBg[i].classList.contains('close')) {
            modalBg[i].classList.add('close');
            $('body').css('overflow', 'visible');
            document.removeEventListener('touchmove', disableScroll, { passive: false });
        }
    });
};

$('.modal').on('click',function(event){
    const logout=document.getElementById('logout-btn');
    const login=document.getElementById('login-btn');
    const userAdd=document.getElementById('user-add-btn');
    if(event.target!==logout && event.target!==login && event.target!==userAdd){
        return false;
    }
})

$('.sp-search-btn').on('click',function(){
    $('.sp-header').css({
        'opacity':1,
        'visibility':'visible'
    })
})
$('.sp-search-close').on('click',function(){
    $('.sp-header').css({
        'opacity':0,
        'visibility':'hidden'
    })
})

