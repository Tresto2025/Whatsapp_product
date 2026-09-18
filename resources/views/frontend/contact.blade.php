
@extends('frontend.layout.app')

@section('title', 'Contact Us – Tatkal Doctor')
@section('description', 'Contact Us page')
@section('keywords', 'Contact Tatkal Doctor for AI-powered healthcare solutions including WhatsApp automation, AI voice receptionist, appointment booking, communication systems for hospitals and clinics.')
@section('canonical', 'https://www.tatkaldoctor.com/contact')
@section('robots', 'index, follow')
@section('og:title', 'Contact Us – Tatkal Doctor')
@section('og:url', 'https://www.tatkaldoctor.com/contact')
@section('og:description', 'Contact Tatkal Doctor for AI-powered healthcare solutions including WhatsApp automation, AI voice receptionist, appointment booking, communication systems for hospitals and clinics.')
@section('og:Keywords', 'Test')
@section('og:image', '')


@section('google-site-verification', '-X0SGrGwjmNQl3P4hy6vzpqOFvJpmsXJ4PkTx61VyI0')
@section('coverage', 'India')
@section('language', 'en-us')
@section('allow-search', 'yes')
@section('search engines', 'all')
@section('distribution', 'global')
@section('rating', 'safe for kids')
@section('revisit-after', 'always')
@section('country', 'India')
@section('geo.region', 'IN-DELHI')
@section('geo.placename', 'DELHI')

@section('content')
<section class="contact-section py-5 py-lg-6 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h1 class="display-5 fw-bold text-success">
        Contact Us
      </h1>
      <p class="lead text-muted">
        Have a question or need support? Reach out to us — we’re here to help.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-lg-6">
        <div class="contact-info-card p-4 p-md-5 rounded-4 shadow-lg h-100 bg-white border-0">
          <h2 class="h3 mb-4 text-primary">
            Get in touch
          </h2>
          <p class="text-secondary mb-4">
            If you have queries about services, pricing, or technical support, contact us:
          </p>

          <div class="list-group list-group-flush mb-4">
            <div class="list-group-item d-flex align-items-start p-3 border-0">
              <div class="icon-square me-3 flex-shrink-0">
                <i class="fas fa-map-marker-alt fa-lg text-success"></i>
              </div>
              <div>
                <h5 class="fw-bold mb-0">Office</h5>
                <p class="mb-0 text-muted">
                  2ND Floor, Vinayak Complex, Sakarpur, Computer Market, Near Nirman Vihar Metro Station, Delhi-110092
                </p>
              </div>
            </div>

            <div class="list-group-item d-flex align-items-start p-3 border-0">
              <div class="icon-square me-3 flex-shrink-0">
                <i class="fas fa-phone-alt fa-lg text-success"></i>
              </div>
              <div>
                <h5 class="fw-bold mb-0">Phone</h5>
                <a href="tel:+919217443758" class="text-decoration-none text-dark d-block">+919217443758</a>
                <a href="tel:+918071387288" class="text-decoration-none text-dark d-block">+918071387288</a>
              </div>
            </div>

            <div class="list-group-item d-flex align-items-start p-3 border-0">
              <div class="icon-square me-3 flex-shrink-0">
                <i class="fas fa-envelope fa-lg text-success"></i>
              </div>
              <div>
                <h5 class="fw-bold mb-0">Email</h5>
                <a href="mailto:doctortatkal@gmail.com"
                  class="text-decoration-none text-dark d-block">doctortatkal@gmail.com</a>
                <small class="text-muted">Support / Refunds:
                  <a href="mailto:doctortatkal@gmail.com"
                    class="text-decoration-none text-muted">doctortatkal@gmail.com</a>
                  |
                  <a href="tel:+919217443758" class="text-decoration-none text-muted">+919217443758</a></small>
              </div>
            </div>
          </div>

          <div class="mt-4 pt-3 border-top social-icons-modern">
            <a href="https://www.facebook.com/tatkaldoctor/" class="social-icon-link me-3" target="_blank"><i class="fab fa-facebook-f fa-lg"></i></a>
            <a href="https://www.instagram.com/tatkaldoctor/" class="social-icon-link me-3" target="_blank"><i class="fab fa-instagram fa-lg"></i></a>
            <a href="https://www.linkedin.com/company/tatkaldoctor/" class="social-icon-link me-3" target="_blank"><i class="fab fa-linkedin-in fa-lg"></i></a>
            <a href="https://x.com/tatkaldoctor" class="social-icon-link me-3" target="_blank"><i class="fab fa-twitter fa-lg"></i></a>
            <a href="#" class="social-icon-link"><i class="fab fa-youtube fa-lg"></i></a>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="contact-form-card p-4 p-md-5 rounded-4 shadow-lg h-100 bg-white border-0">
          <h2 class="h3 mb-4 text-primary">
            Send us your Query
          </h2>

          <form id="contactFormModern" method="post">
              @csrf
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="name" name="name" placeholder="abc" required />
              <label for="name">Name</label>
            </div>

            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="email" name="email" placeholder="abc@mail.com" required />
              <label for="email">Email</label>
            </div>

            <div class="form-floating mb-3">
              <input type="tel" class="form-control" id="phone" name="phone" placeholder="99999 99999" />
              <label for="phone">Phone</label>
            </div>

            <div class="form-floating mb-4">
              <textarea class="form-control" id="message" name="message" placeholder="Message" rows="5"
                style="height: 150px" required></textarea>
              <label for="message">Message</label>
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100 send-btn">
              Send Message
              <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>



@endsection
@push('scripts')
<script>
document
  .getElementById("contactFormModern")
  ?.addEventListener("submit", function (e) {
    e.preventDefault();

    const form = this;
    const sendBtn = form.querySelector(".send-btn");

    const formData = new FormData(form);

    // UI state
    sendBtn.disabled = true;
    sendBtn.innerHTML =
      'Sending... <span class="spinner-border spinner-border-sm ms-2"></span>';

    fetch("{{ route('contact.submit') }}", {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      sendBtn.disabled = false;
      sendBtn.innerHTML = "Send Message";

      if (data.status === "success") {
        alert("Thank you! We’ll get back to you shortly.");
        form.reset();
      } else {
        alert("Something went wrong.");
      }
    })
    .catch(() => {
      sendBtn.disabled = false;
      sendBtn.innerHTML = "Send Message";
      alert("Server error. Try again.");
    });
});
</script>
  
@endpush