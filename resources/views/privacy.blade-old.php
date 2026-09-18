<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
 <link rel="stylesheet" href="{{ asset('front') }}/assets/styles.css" />
 <title>Privacy Policy | Tatkal Doctor</title>
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
  <div class="nav__logo"><a href="{{ route('home') }}"><img src="{{ asset('uploads') }}/logo/logo.png" alt="logo" style="width:60%;" /></a></div>
  <ul class="nav__links">
   <li class="link"><a href="{{ route('home') }}">Home</a></li>
    <li class="link"><a href="{{ route('about') }}">About Us</a></li>
    <li class="link"><a href="{{ route('contact') }}">Contacts</a></li>
  </ul>
  <a href="{{ route('login') }}"><button class="btn">Register Now</button></a>
 </nav>
   <div class="content">
    <main>
  <h1>Privacy Policy</h1>
  <p class="muted"><em>Last updated: 06 November 2025</em></p>

  <p><strong>TatkalDoctor</strong> operated by <strong>TatkalDoctor</strong> at
   <strong>https://tatkaldoctor.com</strong>. We act as the <strong>data fiduciary (data controller)</strong> for
   personal data we collect and process under Indian law (IT Act & DPDP Act).</p>

  <h2>1. Data We Collect</h2>
  <ul>
   <li><strong>Identity & Contact:</strong> name, email, phone, address; GSTIN/CIN if provided for invoicing/compliance.
   </li>
   <li><strong>Account & Usage:</strong> log data, device/browser info, IP, cookies for functionality and analytics.
   </li>
   <li><strong>Payments:</strong> via Razorpay. Card/bank details are collected and stored by Razorpay; we receive
    limited transaction metadata.</li>
   <li><strong>Support Content:</strong> messages, attachments, feedback (email/WhatsApp/forms).</li>
  </ul>

  <h2>2. Purposes & Legal Bases</h2>
  <ul>
   <li>Provide and improve services and support (contract/legitimate uses).</li>
   <li>Process payments/refunds, prevent fraud (legal obligation/legitimate uses).</li>
   <li>Transactional communications (contract/legitimate uses).</li>
   <li>Marketing communications (with consent; you may opt out anytime).</li>
   <li>Compliance with laws and law-enforcement requests.</li>
  </ul>

  <h2>3. Sharing</h2>
  <ul>
   <li>Service providers: hosting, analytics, support, and payment processing (Razorpay).</li>
   <li>Legal/compliance: when required by law or to protect rights, safety, and property.</li>
   <li>Business transfers: merger, acquisition, or reorganisation.</li>
  </ul>

  <h2>4. Cookies</h2>
  <p>We use cookies for essential operations, preferences, and analytics. You can manage cookies in your browser;
   disabling some may affect functionality.</p>

  <h2>5. Security</h2>
  <p>We implement reasonable security practices and procedures. However, no method of transmission or storage is
   entirely secure.</p>

  <h2>6. Retention</h2>
  <p>We retain personal data as long as necessary to provide services, meet legal obligations, resolve disputes, and
   enforce agreements.</p>

  <h2>7. Your Rights & Choices</h2>
  <p>You may request to access, correct, update, or delete your data; withdraw consent (where applicable); and object
   to/restrict processing, subject to law.</p>
  <p><strong>Submit a request:</strong> email <a href="mailto:doctortatkal@gmail.com">doctortatkal@gmail.com</a> with
   subject “Data Rights Request,” your name/phone, and description. We may request limited verification.</p>

  <h2>8. Children</h2>
  <p>Our services are not directed to children under <strong>14</strong>. We do not knowingly collect data from minors
   without appropriate consent.</p>

  <h2>9. International Transfers</h2>
  <p>Where data is processed outside India by our providers, we ensure appropriate safeguards as required by law.</p>

  <h2>10. Automated Decision-Making</h2>
  <p>We do <strong>not</strong> use automated decision-making or profiling that has legal or similarly significant
   effects.</p>

  <h2>11. Grievance Officer</h2>
  <p>Grievance Officer: <strong>Vinay Kumar Jain</strong><br />
   Email: <a href="mailto:infosoft273@gmail.com">infosoft273@gmail.com</a><br />
   Address: 2ND Floor, Vinayak Complex,
   Sakarpur, Computer Market,
   Near Nirman Vihar Metro Station, Delhi-110092<br />
   Acknowledgement within <strong>2 business days</strong>; resolution target within <strong>10 business days</strong>
   (max <strong>30 days</strong>).</p>


  <h2>13. Changes</h2>
  <p>We may update this Policy and post the latest version here with an updated “Last updated” date.</p>

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