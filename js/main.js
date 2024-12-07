/*carousel du header */
let myCurrentIndex = 0;
let myTimer;

function startCarousel() {
    myTimer = setInterval(nextSlide, 5000);
}

function stopCarousel() {
    clearInterval(myTimer);
}

function nextSlide() {
    const slides = document.querySelectorAll('.my-slide');
    const totalSlides = slides.length;
    myCurrentIndex = (myCurrentIndex + 1) % totalSlides;
    updateMyCarousel();
}

function prevSlide() {
    const slides = document.querySelectorAll('.my-slide');
    const totalSlides = slides.length;
    myCurrentIndex = (myCurrentIndex - 1 + totalSlides) % totalSlides;
    updateMyCarousel();
}

function updateMyCarousel() {
    const carousel = document.querySelector('.my-carousel');
    const slideWidth = carousel.offsetWidth;
    const offset = -myCurrentIndex * slideWidth;
    carousel.style.transform = `translateX(${offset}px)`;
}

document.addEventListener('DOMContentLoaded', function() {
    startCarousel();
});

/*carousel des livres à la une*/
let currentIndex = 0;

function livrenextSlide() {
    const carousel = document.getElementById('carousel');
    const books = carousel.querySelectorAll('.book');
    if (currentIndex === books.length - 1) {
        currentIndex = 0;
    } else {
        currentIndex++;
    }
    
    updateCarouselPosition(books);
}

function livreprevSlide() {
    const carousel = document.getElementById('carousel');
    const books = carousel.querySelectorAll('.book');
    
    if (currentIndex === 0) {
        currentIndex = books.length - 1;
    } else {
        currentIndex--;
    }
    
    updateCarouselPosition(books);
}

function updateCarouselPosition(books) {
    const carousel = document.getElementById('carousel');
    const bookWidth = books[0].offsetWidth;
    const newPosition = -currentIndex * bookWidth;
    
    carousel.style.transform = `translateX(${newPosition}px)`;
}
/*carousel des livres fantasy */

let fcurrentIndex = 0;

function fantasynextSlide() {
    const carousel = document.getElementById('carouself');
    const books = carousel.querySelectorAll('.book');
    if (fcurrentIndex === books.length - 1) {
        fcurrentIndex = 0;
    } else {
        fcurrentIndex++;
    }
    
    fupdateCarouselPosition(books);
}

function fantasyprevSlide() {
    const carousel = document.getElementById('carouself');
    const books = carousel.querySelectorAll('.book');
    
    if (fcurrentIndex === 0) {
        fcurrentIndex = books.length - 1;
    } else {
        fcurrentIndex--;
    }
    
    fupdateCarouselPosition(books);
}

function fupdateCarouselPosition(books) {
    const carousel = document.getElementById('carouself');
    const bookWidth = books[0].offsetWidth;
    const newPosition = -fcurrentIndex * bookWidth;
    
    carousel.style.transform = `translateX(${newPosition}px)`;
}

/*carousel des mangas*/

let mcurrentIndex = 0;

function manganextSlide() {
    const carousel = document.getElementById('carouselm');
    const books = carousel.querySelectorAll('.book');
    if (mcurrentIndex === books.length - 1) {
        mcurrentIndex = 0;
    } else {
        mcurrentIndex++;
    }
    
    mupdateCarouselPosition(books);
}

function mangaprevSlide() {
    const carousel = document.getElementById('carouselm');
    const books = carousel.querySelectorAll('.book');
    
    if (mcurrentIndex === 0) {
        mcurrentIndex = books.length - 1;
    } else {
        mcurrentIndex--;
    }
    
    mupdateCarouselPosition(books);
}

function mupdateCarouselPosition(books) {
    const carousel = document.getElementById('carouselm');
    const bookWidth = books[0].offsetWidth;
    const newPosition = -mcurrentIndex * bookWidth;
    
    carousel.style.transform = `translateX(${newPosition}px)`;
}

