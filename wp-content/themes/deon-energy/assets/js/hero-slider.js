// Deon Energy — Hero slider (Swiper v11). Only runs when .hero__swiper exists.
document.addEventListener('DOMContentLoaded', function () {
	var el = document.querySelector('.hero__swiper');
	if (!el) return;

	new Swiper(el, {
		loop: true,
		effect: 'fade',
		fadeEffect: { crossFade: true },
		autoplay: {
			delay: 4500,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		a11y: {
			prevSlideMessage: 'Previous slide',
			nextSlideMessage: 'Next slide',
		},
	});
});
