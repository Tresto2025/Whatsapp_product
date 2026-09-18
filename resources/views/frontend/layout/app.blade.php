<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    
    
    <title>@yield('title', 'Tatkal Doctor - WhatsApp Appointment Booking System')</title>
    
    <meta name="description" content="@yield('description', 'Tatkal Doctor - WhatsApp Appointment Booking System')">
    <meta name="keywords" content="@yield('keywords', '')">
    <link rel="canonical" href="@yield('canonical', '')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta property="og:title" content="@yield('og:title', 'Tatkal Doctor - WhatsApp Appointment Booking System')">
    <meta property="og:url" content="@yield('og:url', 'https://tatkaldoctor.com/')">
    <meta property="og:description" content="@yield('og:description', 'Tatkal Doctor - WhatsApp Appointment Booking System')">
    <meta property="og:Keywords" content="@yield('og:Keywords', '')">
    <meta property="og:image" content="@yield('og:image', '')">
    
    <meta name="google-site-verification" content="@yield('google-site-verification', '')" />
    <meta name="coverage" content="@yield('coverage', 'India')">
    <meta name="language" content="@yield('language', 'en-us')">
    <meta name="allow-search" content="@yield('allow-search', 'yes')" />
    <meta name="search engines" content="@yield('search engines', 'all')" />
    <meta name="distribution" content="@yield('distribution', 'global')" />
    <meta name="rating" content="@yield('distribution', 'safe for kids')">
    <meta name="revisit-after" content="@yield('revisit-after', 'always')" />
    <meta name="country" content="@yield('country', 'India')">
    <meta name="geo.region" content="@yield('geo.region', 'IN-DELHI')" />
    <meta name="geo.placename" content="@yield('geo.placename', 'DELHI')" />
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.4.47/css/materialdesignicons.min.css" integrity="sha512-/k658G6UsCvbkGRB3vPXpsPHgWeduJwiWGPCGS14IQw3xpr63AEMdA8nMYG2gmYkXitQxDTn6iiK/2fD4T87qA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/voice_agent.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/whatsapp-ai-agent.css') }}" />
    <!-- Favicon -->
<link rel="apple-touch-icon" sizes="180x180"
      href="{{ asset('favicon/apple-touch-icon.png') }}">

<link rel="icon" type="image/png" sizes="32x32"
      href="{{ asset('favicon/favicon-32x32.png') }}">

<link rel="icon" type="image/png" sizes="16x16"
      href="{{ asset('favicon/favicon-16x16.png') }}">

<link rel="icon" type="image/x-icon"
      href="{{ asset('favicon/favicon.ico') }}">

<link rel="manifest"
      href="{{ asset('favicon/site.webmanifest') }}">
    <script src="{{ asset('frontend/js/dropdown.js') }}"></script>
    <style>
        @media(max-width:1400px)
        {
            .navbar>.container{
                max-width:100%;
            }
        }
          @media(max-width:1350px)
        {
            .navbar>.container{
                max-width:100%;
            }
            .navbar .nav-item
            {
                font-size:15px;
              
            }
            .navbar .nav-item .nav-link
            {
               margin:4px 7px;
                padding:0 8px;
            }
        }
          @media(max-width:768px)
        {
            .navbar>.container{
                max-width:100%;
            }
            .navbar .nav-item
            {
                font-size:16px;
              
            }
            .navbar .nav-item .nav-link
            {
               margin:8px 7px;
                padding:0 8px;
            }
        }
    </style>
    @stack('styles')
    
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

    @stack('scripts')
</body>

</html>
