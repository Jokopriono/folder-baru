const navWrap = document.querySelector('.nav-wrap');
const menuToggle = document.querySelector('.menu-toggle');
const searchToggle = document.querySelector('.search-toggle');
const searchPanel = document.querySelector('#search-panel');

if (menuToggle && navWrap) {
    menuToggle.addEventListener('click', () => {
        const isOpen = navWrap.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', String(isOpen));
    });
}

if (searchToggle && searchPanel) {
    searchToggle.addEventListener('click', () => {
        const isExpanded = searchToggle.getAttribute('aria-expanded') === 'true';
        searchToggle.setAttribute('aria-expanded', String(!isExpanded));
        searchPanel.hidden = isExpanded;
    });
}

const slider = document.querySelector('[data-slider]');

if (slider) {
    const slides = Array.from(slider.querySelectorAll('[data-slide]'));
    const previousButton = slider.querySelector('[data-prev]');
    const nextButton = slider.querySelector('[data-next]');
    let currentIndex = 0;

    const showSlide = (index) => {
        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle('is-active', slideIndex === index);
        });
        currentIndex = index;
    };

    const moveSlide = (direction) => {
        const nextIndex = (currentIndex + direction + slides.length) % slides.length;
        showSlide(nextIndex);
    };

    previousButton?.addEventListener('click', () => moveSlide(-1));
    nextButton?.addEventListener('click', () => moveSlide(1));

    setInterval(() => moveSlide(1), 5500);
}