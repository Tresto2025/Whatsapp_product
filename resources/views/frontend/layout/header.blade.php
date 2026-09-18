<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('frontend/images/logo.png') }}" alt="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('features') }}">Voice AI Agent</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('whatsApp-ai-agent-hospital-clinic') }}">WhatsApp AI Agent</a>
                </li>

                {{-- Resources Dropdown — Desktop: hover | Mobile: click --}}
                <li class="nav-item td-resources">
                    <a class="nav-link td-resources-toggle" href="#" aria-expanded="false">
                        Resources <span class="td-arrow">&#9660;</span>
                    </a>
                    <ul class="td-dropdown">
                        <li><a class="td-drop-item" href="{{ route('blog.index') }}">Blogs</a></li>
                        <li><a class="td-drop-item" href="{{ route('about') }}" href="{{ route('about') }}">About Us</a></li>
                        <li class="td-divider"></li>
                        <li><a class="td-drop-item" href="#">Case Studies</a></li>
                        <li><a class="td-drop-item" href="#">FAQs</a></li>
                        <li><a class="td-drop-item" href="#">Knowledge Base</a></li>
                        <li><a class="td-drop-item" href="#">News</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact') }}">Contact Us</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link demo" href="{{ route('schedule.demo') }}">Demo</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}" href="{{ route('register') }}">Register Now</a>
                </li>

                <li class="nav-item">
                    <button class="btn-login">LOGIN</button>
                </li>

            </ul>
        </div>
    </div>
</nav>