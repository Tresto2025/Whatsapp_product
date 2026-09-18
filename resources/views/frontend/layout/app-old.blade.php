<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tatkal Doctor - WhatsApp Appointment Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}" />
    @stack('css')
</head>

<body>
    @include('frontend.layout.header')

    @yield('content')

    <!-- Footer -->
    @include('frontend.layout.footer')






    <!-- End Footer -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sync carousel indicators with Bootstrap carousel
        document.addEventListener('DOMContentLoaded', function() {
            const heroCarousel = document.getElementById('heroCarousel');
            const indicators = document.querySelectorAll('.carousel-indicators .indicator');

            if (heroCarousel) {
                heroCarousel.addEventListener('slide.bs.carousel', function(e) {
                    // Remove active class from all indicators
                    indicators.forEach(indicator => {
                        indicator.classList.remove('active');
                        indicator.removeAttribute('aria-current');
                    });

                    // Add active class to current indicator
                    const activeIndicator = indicators[e.to];
                    if (activeIndicator) {
                        activeIndicator.classList.add('active');
                        activeIndicator.setAttribute('aria-current', 'true');
                    }
                });
            }

            // Counter Animation for Statistics
            function animateCounter(element) {
                const target = parseFloat(element.getAttribute('data-target'));
                const prefix = element.getAttribute('data-prefix') || '';
                const suffix = element.getAttribute('data-suffix') || '';
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // 60fps
                let current = 0;
                const isDecimal = target % 1 !== 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }

                    const displayValue = isDecimal ? current.toFixed(1) : Math.floor(current);
                    element.textContent = prefix + displayValue + suffix;
                }, 16);
            }

            // Intersection Observer for triggering animation when in viewport
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                        entry.target.classList.add('counted');
                        animateCounter(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all stat numbers
            document.querySelectorAll('.stat-number[data-target]').forEach(stat => {
                observer.observe(stat);
            });
        });
    </script>

</body>

</html>
