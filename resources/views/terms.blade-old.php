<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
 <link rel="stylesheet" href="{{ asset('front') }}/assets/styles.css" />
 <title>Terms & Conditions | Tatkal Doctor</title>
 <style>
  main {margin: 0 auto;padding: 2rem 1rem;line-height: 1.7}
  .hero {display: flex;gap: 2rem;align-items: flex-start}
  .hero .left {flex: 1}
  .hero .right {flex: 0 0 360px}
  .card {background: #f8f8f8;padding: 1.25rem;border-radius: 8px;margin: 1rem 0}
  .feature-grid {display: grid;grid-template-columns: repeat(2, 1fr);gap: 1rem}
  .feature {background: #fff;border: 1px solid #eee;padding: 1rem;border-radius: 8px}
  @media (max-width:900px) {
   .hero {flex-direction: column}
   .feature-grid {grid-template-columns: 1fr}
  }
  /* ensure register CTA stays in top-right of nav */
  nav .btn {position: absolute;right: 1rem;top: 18px}
  nav {position: relative}
 </style>
</head>

<body>
 <div class="container">
  <nav>
  <div class="nav__logo"><a href="{{ route('home') }}">
    <img src="{{ asset('uploads') }}/logo/logo.png" alt="logo" style="width:60%;" /></a>
  </div>
  <ul class="nav__links">
   <li class="link"><a href="{{ route('home') }}">Home</a></li>
    <li class="link"><a href="{{ route('about') }}">About Us</a></li>
    <li class="link"><a href="{{ route('contact') }}">Contacts</a></li>
  </ul>
  <a href="{{ route('login') }}"><button class="btn">Register Now</button></a>
 </nav>
   <div class="content">
    <main>
  <h1>Terms & Conditions</h1>
  <p><em>Last updated: 06 November 2025</em></p>

  <h2>1. Acceptance</h2>
  <p>By accessing <strong>https://tatkaldoctor.com</strong> or using our services, you agree to these Terms. If you do
   not agree, please do not use the services.</p>

  <h2>2. Eligibility & Accounts</h2>
  <ul>
   <li>You must be competent to contract under Indian law.</li>
   <li>You are responsible for safeguarding your credentials and all activity under your account.</li>
  </ul>

  <h2>3. Services & Pricing</h2>
  <ul>
   <li>We may modify, suspend, or discontinue any service or price at any time.</li>
   <li>Prices are in INR and inclusive/exclusive of taxes as shown at checkout.</li>
  </ul>

  <h2>4. Payments via Razorpay</h2>
  <ul>
   <li>Payments are processed by Razorpay. Your payment data is handled by Razorpay as per their policies.</li>
   <li>By paying, you authorise charges to your selected method.</li>
   <li>Chargebacks/disputes may result in temporary suspension pending resolution.</li>
  </ul>

  <h2>5. Cancellations, Refunds & Returns</h2>
  <p>See our <a href="{{ route('refunds') }}">Cancellation &amp; Refund Policy</a> for timelines and conditions.</p>

  <h2>6. Acceptable Use</h2>
  <ul>
   <li>No unlawful, harmful, fraudulent, infringing, or abusive activity.</li>
   <li>No interference with service integrity or security.</li>
   <li>No violation of third-party intellectual property or privacy rights.</li>
  </ul>

  <h2>7. Intellectual Property</h2>
  <p>All content and IP belong to <strong>TatkalDoctor</strong> or its licensors. Do not copy, modify, distribute, or
   create derivatives without permission.</p>

  <h2>8. Warranties & Disclaimers</h2>
  <p>Services are provided on an “as is” and “as available” basis. To the fullest extent permitted by law, we disclaim
   all warranties, express or implied.</p>

  <h2>9. Limitation of Liability</h2>
  <p>To the maximum extent permitted by law, we are not liable for indirect, incidental, special, consequential, or
   punitive damages, or any loss of profits, data, or business.</p>

  <h2>10. Indemnity</h2>
  <p>You agree to indemnify and hold harmless <strong>TatkalDoctor</strong> from claims arising out of your use of the
   services or breach of these Terms.</p>

  <h2>11. Governing Law & Disputes</h2>
  <p>These Terms are governed by the laws of India. Courts at <strong>New Delhi</strong> have exclusive jurisdiction.
   Optionally, disputes may be referred to arbitration under the Arbitration and Conciliation Act, 1996 by a sole
   arbitrator seated in <strong>New Delhi</strong>, proceedings in English.</p>

  <h2>12. Changes</h2>
  <p>We may update these Terms; continued use after changes means you accept the updated Terms.</p>

  <h2>13. Contact</h2>
  <p>Email: <a href="mailto:doctortatkal@gmail.com">doctortatkal@gmail.com</a> &nbsp;<br> Phone: <a
    href="tel:+919891681188">+91 9891681188</a></p>
 </main>
   </div>
 </div>
  <footer class="site-footer">
  <div class="footer__wrap">
   <div class="footer__brand">
    <img src="{{ asset('uploads') }}/logo/logo.png" alt="Tatkal Doctor" style="height:40px;" />
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
    <p>Phone/WhatsApp: <a href="tel:+919891681188">+91 9891681188</a></p>
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