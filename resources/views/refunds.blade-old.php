<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
 <link rel="stylesheet" href="{{ asset('front') }}/assets/styles.css" />
 <title>Cancellation & Refund Policy | Tatkal Doctor</title>
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
  <h1>Cancellation & Refund Policy</h1>
  <p><em>Last updated: 06 November 2025</em></p>

  <p>This policy applies to purchases made on <strong>https://tatkaldoctor.com</strong> operated by <strong>TATKAL
    DOCTOR</strong>.</p>

  <h2>1. Order Cancellation</h2>
  <ul>
   <li><strong>Before activation:</strong> Cancel within <strong>12 hours</strong> of purchase by emailing <a
     href="mailto:doctortatkal@gmail.com">doctortatkal@gmail.com</a> or calling <a href="tel:+919891681188">+91
     9891681188</a>.</li>
   <li><strong>After activation:</strong> If service is activated/consumed (e.g., credits used, session completed),
    cancellation isn’t available; see Refund Eligibility.</li>
  </ul>

  <h2>2. Refund Eligibility (Digital Services)</h2>
  <ul>
   <li>Refunds eligible if service access not delivered or a confirmed technical fault occurs on our side and you notify
    us within <strong>7 days</strong> of purchase.</li>
   <li>After full delivery/consumption, refunds are generally not available.</li>
  </ul>

  <h2>3. How to Request a Refund</h2>
  <ul>
   <li>Email <a href="mailto:doctortatkal@gmail.com">doctortatkal@gmail.com</a> with order ID, issue description, and
    screenshots if any.</li>
   <li>We acknowledge within <strong>48 hours</strong> and provide a decision within <strong>7 business days</strong>.
   </li>
  </ul>

  <h2>4. Refund Method & Timeline</h2>
  <ul>
   <li>Approved refunds are <strong>initiated within 5–7 business days</strong> to the original payment method via
    Razorpay.</li>
   <li>Your bank/payment provider may take an additional <strong>3–10 business days</strong> to credit.</li>
   <li>Convenience fees/taxes (if any) are <strong>non-refundable</strong> unless legally required.</li>
  </ul>

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