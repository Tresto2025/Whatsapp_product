@extends('frontend.layout.app')

@section('content')

<section class="terms-wrapper">
  <div class="terms-container">

    <h2 class="terms-heading">Terms & Conditions</h2>
    <p class="updated-date"><i>Last updated: 06 November 2025</i></p>

    <div class="terms-content">

      <div class="term-box">
        <h4>1. Acceptance</h4>
        <p>
          By accessing <strong>https://tatkaldoctor.com</strong> or using our services,
          you agree to these Terms. If you do not agree, please do not use the services.
        </p>
      </div>

      <div class="term-box">
        <h4>2. Eligibility & Accounts</h4>
        <p>
          You must be competent to contract under Indian law. You are responsible
          for safeguarding your credentials and all activity under your account.
        </p>
      </div>

      <div class="term-box">
        <h4>3. Services & Pricing</h4>
        <p>
          We may modify, suspend, or discontinue any service or price at any time.
          Prices are in INR and shown at checkout.
        </p>
      </div>

      <div class="term-box">
        <h4>4. Payments via Razorpay</h4>
        <p>
          Payments are processed by Razorpay. By paying, you authorise charges to
          your selected method. Disputes may lead to temporary suspension.
        </p>
      </div>

      <div class="term-box">
        <h4>5. Cancellations, Refunds & Returns</h4>
        <p>
          See our
          <a href="{{ route('refunds') }}">Cancellation & Refund Policy</a>
          for timelines and conditions.
        </p>
      </div>

      <div class="term-box">
        <h4>6. Acceptable Use</h4>
        <p>
          No unlawful, harmful, or abusive activity. Do not interfere with
          service security or violate third-party rights.
        </p>
      </div>

      <div class="term-box">
        <h4>7. Intellectual Property</h4>
        <p>
          All content belongs to <strong>Tatkal Doctor</strong>. Unauthorized use
          is strictly prohibited.
        </p>
      </div>

      <div class="term-box">
        <h4>8. Warranties & Disclaimers</h4>
        <p>
          Services are provided on an “as is” and “as available” basis without
          warranties of any kind.
        </p>
      </div>

      <div class="term-box">
        <h4>9. Limitation of Liability</h4>
        <p>
          We are not liable for indirect, incidental, or consequential damages.
        </p>
      </div>

      <div class="term-box">
        <h4>10. Governing Law & Disputes</h4>
        <p>
          Governed by the laws of India. Courts in <strong>New Delhi</strong>
          have jurisdiction.
        </p>
      </div>

      <div class="term-box">
        <h4>11. Changes</h4>
        <p>
          Continued use of the service means acceptance of updated terms.
        </p>
      </div>

      <div class="term-box">
        <h4>12. Contact</h4>
        <p>
          Email:
          <a href="mailto:doctortatkal@gmail.com">doctortatkal@gmail.com</a><br>
          Phone:
          <a href="tel:+919891681122">+91 9891681122</a>
        </p>
      </div>

    </div>
  </div>
</section>

@endsection