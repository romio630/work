function gotfocus(element) {
    element.addEventListener('keyup', () => {
        if (element.value.length > 0) {
            element.nextElementSibling.classList.remove('none');
        } else {
            element.nextElementSibling.classList.add('none');
        }
    })
}

function lostfocus() {
    $('.fa-times-circle:not(:hover)').addClass('none');
}

function inputlength(input, element, limit) {
    input.addEventListener('keyup', () => {
        let textLength = input.value.length;
        document.querySelector(element).textContent = `${textLength}/${limit}`;
    })
}

function inputreplace(input) {
    const replaceValue=input.value.replace(/\s+/g,'');
    return replaceValue;
}