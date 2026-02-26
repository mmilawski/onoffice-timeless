document.addEventListener('DOMContentLoaded', function () {
    // Check if user prefers reduced motion (accessibility setting, often used by screen readers)
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('.c-success-counter').forEach((section) => {
        const animationSpeed = (parseFloat(section.getAttribute('data-animation-speed')) || 2) * 1000;

        section.querySelectorAll('.c-success-counter-card').forEach((card) => {
            const numberElement = card.querySelector('.c-success-counter-card__value-number');
            if (!numberElement) return;

            const endValue = parseFloat(card.getAttribute('data-end')) || 0;
            const siteLang = document.documentElement.lang || 'de-DE';

            // Skip animation for users who prefer reduced motion (screen readers, etc.)
            if (prefersReducedMotion) {
                if (endValue % 1 !== 0) {
                    numberElement.textContent = endValue.toLocaleString(siteLang, {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2,
                    });
                } else {
                    numberElement.textContent = Math.round(endValue).toLocaleString(siteLang);
                }
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        animateSuccessCounter(
                            numberElement,
                            parseFloat(card.getAttribute('data-start')) || 0,
                            endValue,
                            animationSpeed
                        );
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            observer.observe(card);
        });
    });

    function animateSuccessCounter(element, startValue, endValue, duration) {
        const startTime = Date.now();
        const difference = endValue - startValue; 
        const siteLang = document.documentElement.lang || 'de-DE';

        function animate() {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const currentValue = startValue + difference * (progress * (2 - progress));
            if (endValue % 1 !== 0) {
                element.textContent = currentValue.toLocaleString(siteLang, {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2,
                });
            } else {
                element.textContent = Math.round(currentValue).toLocaleString(siteLang);
            }

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        }

        animate();
    }
});