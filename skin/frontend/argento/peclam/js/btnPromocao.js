let $btnPromocao = document.querySelector('.campaign-label-catalog');

let $btnPromocaoSpan = document.querySelector('.campaign-label .block');

$btnPromocao.addEventListener('click', function() { $btnPromocaoSpan.classList.toggle('showPromocao') });
