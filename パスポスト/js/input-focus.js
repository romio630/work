const inputs = document.getElementsByClassName('input');
const textArea=document.getElementsByTagName('textarea');

addEventListener('click', (e) => {
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i] == document.activeElement) {
            if(inputs[i].parentNode.style.border != '1px solid rgb(227, 51, 57)'){
                inputs[i].parentNode.style.border = '1px solid #696969';
            }
        }else if(e.target != button){
            if(inputs[i].parentNode.style.border != '1px solid rgb(227, 51, 57)'){
                inputs[i].parentNode.style.border = '1px solid #b3b3b3';
            }
        }
    }
});

addEventListener('click', (e) => {
    for (let i = 0; i < textArea.length; i++) {
        if (textArea[i] == document.activeElement) {
            if(textArea[i].style.border != '1px solid rgb(227, 51, 57)'){
                textArea[i].style.border = '1px solid #696969';
            }
        }else if(e.target != button){
            if(textArea[i].style.border != '1px solid rgb(227, 51, 57)'){
                textArea[i].style.border = '1px solid #b3b3b3';
            }
        }
    }
});

