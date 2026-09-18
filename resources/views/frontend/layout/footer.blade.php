<!-- Start Footer -->
<footer class="footer">
  <div class="footer-main">
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
          <h5 class="footer-title">Ready to Transform Your Business?</h5>
          <button type="button" class="btn-schedule"
            onclick="location.href='{{ route('contact') }}'">
            Schedule Now <i class="fas fa-arrow-right"></i>
          </button>
          <div class="footer-logo mt-4">
            <img src="{{ asset('frontend/images/logo.png') }}" alt="logo">
          </div>
        </div>

        <div class="col-lg-2 col-md-6 mb-4">
          <h5 class="footer-title">Company</h5>
          <ul class="footer-links">
            <li><a href="#">WhatsApp API Pricing</a></li>
            <li><a href="{{ route('whatsApp-ai-agent-hospital-clinic') }}">Whatsapp Business Solutions</a></li>
            <li><a href="{{ route('schedule.demo') }}">Book a Demo</a></li>
            <li><a href="{{ route('contact') }}">Contact us</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6 mb-4">
          <h5 class="footer-title">Other Locations</h5>
          <ul class="footer-links">
            <li><a href="#">New Delhi</a></li>
            <li><a href="#">Coimbatore</a></li>
            <li><a href="#">Hyderabad</a></li>
            <li><a href="#">Bangalore</a></li>
            <li><a href="#">Mumbai</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6 mb-4">
          <h5 class="footer-title">Industry</h5>
          <ul class="footer-links">
            <li><a href="#">Chatbot for Medical</a></li>
            <li><a href="#">Chatbot for Education</a></li>
            <li><a href="#">Chatbot for Automotive</a></li>
            <li><a href="#">Chatbot for Travel &amp; Hospitality</a></li>
            <li><a href="#">Chatbot for Healthcare</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
          <h5 class="footer-title">Contact us</h5>
          <div class="contact-info">
            <p>
              <i class="fas fa-map-marker-alt"></i>
              2ND Floor, Vinayak Complex, Sakarpur, Computer Market,
              Near Nirman Vihar Metro Station, Delhi-110092
            </p>
            <p><i class="fas fa-phone"></i> +91 9891681122</p>
            <p><i class="fas fa-phone"></i> +91 9217443758</p>
            <p><i class="fas fa-envelope"></i> doctortatkal@gmail.com</p>
          </div>
          <div class="social-icons mt-3">
            <a href="https://www.facebook.com/profile.php?id=61585476892452" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/tatkaldoctor/" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://www.linkedin.com/company/tatkaldoctor/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            <a href="https://x.com/tatkaldoctor" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <p class="mb-0">Copyright &copy; TatkalDoctor 2025, All rights reserved.</p>
        </div>
        <div class="col-md-6 text-md-end">
          <a href="{{ route('privacy') }}" class="footer-link">Privacy Policy</a> |
          <a href="{{ route('terms') }}" class="footer-link">T &amp; C</a> |
          <a href="{{ route('refunds') }}" class="footer-link">Refunds</a> |
          <a href="{{ route('shipping') }}" class="footer-link">Shipping &amp; Delivery</a>
        </div>
      </div>
      <div class="text-center mt-2">
        <small>Official Partners | Business Partners</small>
      </div>
    </div>
  </div>

  <a href="https://wa.me/919891681122" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
  </a>
</footer>