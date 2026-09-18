<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('front') }}/assets/styles.css" />
    <title>Tatkal Doctor</title>
</head>

<body>
    <div class="container">
        <nav>
            <div class="nav__logo"><img src="{{ asset('uploads') }}/logo/logo.png" alt="logo" style="width: 60%;" />
            </div>
            <ul class="nav__links">
                <li class="link"><a href="{{ route('home') }}">Home</a></li>
                <li class="link"><a href="{{ route('about') }}">About Us</a></li>
                <li class="link"><a href="{{ route('contact') }}">Contacts</a></li>
                <li class="link"><a href="{{ route('register') }}" >Register Now</a></li>
            </ul>
            <a href="{{ route('login') }}"><button class="btn">Login</button></a>
        </nav>
        <header class="header">
            <div class="content">
                <h1>Renovate to WhatsApp Clinic</h1>
                <p>A New Innovation in Practice Management To Quickly Grow Your Practice & Improve The Patient
                    Experience.</p>
                <p>Tatkal Doctor is an easy-to-use practice management software that brings together all your medical
                    records at one
                    place. It automatically syncs with your existing EMR system, so you don't have to worry about data
                    loss and downtime.</p>
                <!-- <button class="btn">Get Services</button>-->
            </div>
            <div class="image">
                <span class="image__bg"></span>
                <img src="{{ asset('front') }}/assets/header-bg.png" alt="header image" />
                <div class="image__content image__content__1">
                    <span><i class="ri-user-3-line"></i></span>
                    <div class="details">
                        <h4>1520+</h4>
                        <p>Active Clients</p>
                    </div>
                </div>
                <div class="image__content image__content__2">
                    <ul>
                        <li>
                            <span><i class="ri-check-line"></i></span>
                            Get 20% off on every 1st month
                        </li>
                        <li>
                            <span><i class="ri-check-line"></i></span>
                            Expert Doctors
                        </li>
                    </ul>
                </div>
            </div>
        </header>
    </div>
    <footer class="site-footer">
        <div class="footer__wrap">
            <div class="footer__brand">
                <img src="{{ asset('uploads') }}/logo/logo.png" alt="Tatkal Doctor" style="height:40px;" />
                <p>Tatkal Doctor — WhatsApp-first practice management.</p>
                <p>&copy; <span id="year"></span> <strong>TatkalDoctor</strong>. All rights reserved.</p>
            </div>
            <div class="footer__links">
                <h4>Legal</h4>
                <ul>
                    <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('refunds') }}">Cancellation &amp; Refund Policy</a></li>
                    <li><a href="{{ route('shipping') }}">Shipping &amp; Delivery</a></li>
                    <li><a href="{{ route('contact') }}">Contact &amp; Grievance</a></li>
                </ul>
            </div>
            <div class="footer__contact">
                <h4>Contact</h4>
                <p>Email: <a href="mailto:doctortatkal@gmail.com">doctortatkal@gmail.com</a></p>
                <p>Phone/WhatsApp: <a href="tel:+919891681122">+91 9891681122</a></p>
                <p>Address: 2ND Floor, Vinayak Complex,
                    Sakarpur, Computer Market,
                    Near Nirman Vihar Metro Station, Delhi-110092</p>
            </div>
        </div>
    </footer>
    </div>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>

</html>
