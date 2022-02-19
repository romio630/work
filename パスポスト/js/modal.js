const modalBg = document.getElementsByClassName('modal-bg');
const modalCan = document.getElementsByClassName('modal-cancel');

for (let i = 0; i < modalBg.length; i++) {
    modalBg[i].addEventListener('click', () => {
        if (!modalBg[i].classList.contains('close')) {
            modalBg[i].classList.add('close');
        }
    });
};

for (let i = 0; i < modalCan.length; i++) {
    modalCan[i].addEventListener('click', () => {
        if (!modalBg[i].classList.contains('close')) {
            modalBg[i].classList.add('close');
        }
    });
};