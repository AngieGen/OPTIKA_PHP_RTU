let currentIndex = 0;

function showSlide(index) {
  const slides = document.querySelectorAll(".carousel-item");
  const totalSlides = slides.length;

  // Сбрасываем активный класс
  slides.forEach((slide) => slide.classList.remove("active"));

  // Устанавливаем новый активный слайд
  currentIndex = (index + totalSlides) % totalSlides;
  slides[currentIndex].classList.add("active");

  // Двигаем карусель
  const carouselInner = document.querySelector(".carousel-inner");
  carouselInner.style.transform = `translateX(-${currentIndex * 100}%)`;
}

function prevSlide() {
  showSlide(currentIndex - 1);
}

function nextSlide() {
  showSlide(currentIndex + 1);
}

// Автоматическая прокрутка (опционально)
setInterval(() => {
  nextSlide();
}, 5000); // 5 секунд
