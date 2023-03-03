'use strict';
// выделить цветом текущую страницу
const markCurrentPage = () => {
    const currentPage = window.location.search,
        index = currentPage.indexOf('='),
        number = currentPage.substring(index + 1),
        btns = document.querySelectorAll('.btn');

    if (!currentPage) {       
        btns[0].classList.add('current');
    } else {
        btns.forEach(item => {
            if (item.textContent === number) {
                item.classList.add('current');
                localStorage.setItem('pageNumber', number);
            } else {
                item.classList.remove('current');
            }
        });
    }
};
markCurrentPage();
//  если на странице только 1 запись, то показывать ее вверху. В противном случае растягивать по всей высоте
const changeDisplay = () => {
    const articles = document.querySelectorAll('article'),
        main = document.querySelector('main');
    if (articles.length < 2) {
        main.classList.remove('stretched');
    } else {
        main.classList.add('stretched');
    }
};
changeDisplay();