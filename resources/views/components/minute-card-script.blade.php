<script>
    (function () {
        const galleries = document.querySelectorAll('[data-minute-gallery]');

        if (!galleries.length) {
            return;
        }

        galleries.forEach((gallery) => {
            const slides = gallery.querySelectorAll('.minute-card-slide');
            const dots = gallery.querySelectorAll('[data-minute-dot]');
            const prevButton = gallery.querySelector('[data-minute-prev]');
            const nextButton = gallery.querySelector('[data-minute-next]');

            if (!slides.length) {
                return;
            }

            let activeIndex = 0;

            const setActive = (index) => {
                if (index < 0) {
                    index = slides.length - 1;
                } else if (index >= slides.length) {
                    index = 0;
                }

                slides.forEach((slide, slideIndex) => {
                    slide.classList.toggle('is-active', slideIndex === index);
                });

                dots.forEach((dot, dotIndex) => {
                    dot.classList.toggle('is-active', dotIndex === index);
                });

                activeIndex = index;
            };

            if (prevButton) {
                prevButton.addEventListener('click', () => setActive(activeIndex - 1));
            }

            if (nextButton) {
                nextButton.addEventListener('click', () => setActive(activeIndex + 1));
            }

            dots.forEach((dot) => {
                dot.addEventListener('click', () => {
                    const targetIndex = Number(dot.dataset.target) || 0;
                    setActive(targetIndex);
                });
            });
        });
    })();
</script>
