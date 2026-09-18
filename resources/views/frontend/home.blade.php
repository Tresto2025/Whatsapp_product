@extends('frontend.layout.app')

@section('title', 'WhatsApp and AI Voice Appointment Booking System for Hospitals | Tatkal Doctor')
@section('description', 'India Best AI Voice Agent and WhatsApp Appointment System for Clinics and Hospitals. Stop missing patient calls. Transform your hospital with 24/7 smart automation.')
@section('canonical', 'https://www.tatkaldoctor.com/')
@section('robots', 'index, follow')
@section('og:title', 'WhatsApp and AI Voice Appointment Booking System for Hospitals | Tatkal Doctor')
@section('og:url', 'https://www.tatkaldoctor.com/')
@section('og:description', 'India Best AI Voice Agent and WhatsApp Appointment System for Clinics and Hospitals. Stop missing patient calls. Transform your hospital with 24/7 smart automation.')
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

  {{-- ============================================================
       FULL-WIDTH MARKETING BANNER SLIDER (naya design index.html se)
  ============================================================ --}}
  <section class="td-home-top-banner" id="home" aria-label="Featured Tatkal Doctor services">
    <div id="tdHomeTopBannerCarousel" class="carousel slide td-home-top-banner__carousel" data-bs-ride="carousel"
      data-bs-interval="6000" data-bs-pause="hover" data-bs-wrap="true" data-bs-touch="true">

      <div class="carousel-indicators td-home-top-banner__indicators">
        <button type="button" data-bs-target="#tdHomeTopBannerCarousel" data-bs-slide-to="0" class="active"
          aria-current="true" aria-label="Slide 1: AI voice and missed calls"></button>
        <button type="button" data-bs-target="#tdHomeTopBannerCarousel" data-bs-slide-to="1"
          aria-label="Slide 2: AI voice automation for clinics and hospitals"></button>
        <button type="button" data-bs-target="#tdHomeTopBannerCarousel" data-bs-slide-to="2"
          aria-label="Slide 3: Book appointment via WhatsApp in four steps"></button>
      </div>

      <div class="carousel-inner td-home-top-banner__inner">
        <div class="carousel-item active td-home-top-banner__item">
          <figure class="td-home-top-banner__figure">
            <img src="{{ asset('frontend/images/slider5.png') }}" class="td-home-top-banner__media"
              width="1920" height="640"
              alt="Missed calls mean missed revenue—automate patient calls with natural-sounding AI agents 24/7."
              loading="eager" fetchpriority="high" decoding="async" />
          </figure>
        </div>
        <div class="carousel-item td-home-top-banner__item">
          <figure class="td-home-top-banner__figure">
            <img src="{{ asset('frontend/images/slider2.jpg') }}" class="td-home-top-banner__media"
              width="1920" height="640"
              alt="AI voice automation for clinics and hospitals—24/7 answering, instant booking, better patient experience."
              loading="lazy" decoding="async" />
          </figure>
        </div>
        <div class="carousel-item td-home-top-banner__item">
          <figure class="td-home-top-banner__figure">
            <img src="{{ asset('frontend/images/slider3.jpg') }}" class="td-home-top-banner__media"
              width="1920" height="640"
              alt="Book your appointment in four easy steps—fast and secure via WhatsApp with Tatkal Doctor."
              loading="lazy" decoding="async" />
          </figure>
        </div>
      </div>

      <button class="carousel-control-prev td-home-top-banner__arrow td-home-top-banner__arrow--prev" type="button"
        data-bs-target="#tdHomeTopBannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon td-home-top-banner__arrow-glyph" aria-hidden="true"></span>
        <span class="visually-hidden">Previous slide</span>
      </button>
      <button class="carousel-control-next td-home-top-banner__arrow td-home-top-banner__arrow--next" type="button"
        data-bs-target="#tdHomeTopBannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon td-home-top-banner__arrow-glyph" aria-hidden="true"></span>
        <span class="visually-hidden">Next slide</span>
      </button>
    </div>
  </section>

  {{-- ============================================================
       FEATURES SECTION
  ============================================================ --}}
  <section class="features-section" id="features">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-7">
          <h2 class="text-green">AUTOMATE CLINIC AND HOSPITALS</h2>
          <h3>APPOINTMENTS VIA WHATSAPP</h3>
          <ul class="feature-list">
            <li>Automatically Respond to Patient Inquiries</li>
            <li>Register and Schedule Appointments via WhatsApp</li>
            <li>Automatically Send Updates via WhatsApp</li>
            <li>Automatically Send Test Results</li>
            <li>Remind Patients of Appointment Dates</li>
            <li>Enhance Patient Satisfaction and Loyalty</li>
          </ul>
          <button class="btn-start" onclick="location.href='{{ route('contact') }}'">START FOR FREE!</button>
        </div>
        <div class="col-lg-5">
          <div class="phone-mockup-large">
            <img src="{{ asset('frontend/images/1.png') }}" alt="WhatsApp Interface" class="img-fluid" />
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ============================================================
       SERVICE ICONS SECTION
  ============================================================ --}}
  <section class="services-icons">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="service-card d-flex justify-content-center align-items-center">
            <div class="service-icon">
              <img src="{{ asset('frontend/images/icon-1.png') }}" alt="" />
            </div>
            <div class="ps-3">
              <h4>List of Services</h4>
              <p>Provide your service details manually with a chatbot</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="service-card d-flex justify-content-center align-items-center">
            <div class="service-icon">
              <img src="{{ asset('frontend/images/icon-2.png') }}" alt="" />
            </div>
            <div class="ps-3">
              <h4>Send Reminders</h4>
              <p>Notify patient for schedule and confirmations</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="service-card d-flex justify-content-center align-items-center">
            <div class="service-icon">
              <img src="{{ asset('frontend/images/icon-3.png') }}" alt="" />
            </div>
            <div class="ps-3">
              <h4>Patient Support</h4>
              <p>24/7 chatbot for patient support you can add it manually</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ============================================================
       APPOINTMENT REMINDERS SECTION
  ============================================================ --}}
  <section class="reminders-section">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <h2>Appointment Reminders to<br />Post-Treatment Care</h2>

          <div class="reminder-item">
            <div class="icon-box d-flex">
              <div class="icon me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
                  <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z" />
                  <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0" />
                </svg>
              </div>
              <div class="icon-content">
                <h4 class="mb-0">Scheduling</h4>
                <p class="mb-0">Patients can easily book, reschedule, or cancel appointments through WhatsApp, enhancing accessibility.</p>
              </div>
            </div>
          </div>

          <div class="reminder-item">
            <div class="icon-box d-flex">
              <div class="icon me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
                  <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z" />
                  <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0" />
                </svg>
              </div>
              <div class="icon-content">
                <h4 class="mb-0">Real-time Notifications</h4>
                <p class="mb-0">Automated appointment reminders help reduce no-shows and ensure patients stay informed.</p>
              </div>
            </div>
          </div>

          <div class="reminder-item">
            <div class="icon-box d-flex">
              <div class="icon me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
                  <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z" />
                  <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0" />
                </svg>
              </div>
              <div class="icon-content">
                <h4 class="mb-0">Improved Efficiency</h4>
                <p class="mb-0">Clinic and hospital operations become more efficient by automating appointment management, saving time and resources.</p>
              </div>
            </div>
          </div>

          <div class="reminder-item">
            <div class="icon-box d-flex">
              <div class="icon me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
                  <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z" />
                  <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0" />
                </svg>
              </div>
              <div class="icon-content">
                <h4 class="mb-0">Cost Savings</h4>
                <p class="mb-0">Reduced administrative workload and paper-based processes lead to cost savings for healthcare facilities.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 mt-3">
          <img src="{{ asset('frontend/images/2.png') }}" alt="Doctor and Patient" class="doctor-image" />
        </div>
      </div>
    </div>
  </section>

  {{-- ============================================================
       STATISTICS SECTION
  ============================================================ --}}
  <section class="stats-section" id="products">
    <div class="container">
      <h2>MEDICAL CLINIC SCHEDULES 2.5X</h2>
      <p class="subtitle">MORE APPOINTMENTS WITH WHATSAPP</p>
      <div class="row">
        <div class="col-md-3 col-6">
          <div class="stat-card">
            <div class="stat-number" data-target="60" data-prefix="+" data-suffix="%">0</div>
            <div class="stat-label">Scheduling<br />Efficiency</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card">
            <div class="stat-number" data-target="30" data-prefix="-" data-suffix="%">0</div>
            <div class="stat-label">No-show<br />Rates</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card">
            <div class="stat-number" data-target="2.5" data-suffix="x">0</div>
            <div class="stat-label">Qualified<br />Appointments</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card">
            <div class="stat-number" data-target="25" data-prefix="+" data-suffix="%">0</div>
            <div class="stat-label">Patient<br />Satisfaction</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ============================================================
       SERVICE DETAIL SECTION (4 rows - text-center added from index.html)
  ============================================================ --}}
  <!--<section class="section service-section">-->
  <!--  <div class="container">-->
  <!--    <div class="row">-->
  <!--      <div class="col-md-6">-->
  <!--        <div class="image">-->
  <!--          <img src="{{ asset('frontend/images/bgnew.png') }}" class="img-fluid rounded-4" alt="" />-->
  <!--        </div>-->
  <!--      </div>-->
  <!--      <div class="col-md-6">-->
  <!--        <div class="content d-flex flex-column justify-content-center h-100 text-center">-->
  <!--          <h2>Faster And More Efficient Communication</h2>-->
  <!--          <p>Customize WhatsApp in your healthcare facilities to promptly respond to patient inquiries and deliver-->
  <!--            real-time updates, whether it's about appointment confirmations, test results, or general healthcare-->
  <!--            information. This enhanced communication capability serves to not only improve patient satisfaction but-->
  <!--            also fosters a stronger sense of patient loyalty. Patients appreciate the convenience of receiving quick-->
  <!--            and accurate information, which ultimately contributes to a more positive and efficient healthcare-->
  <!--            experience.</p>-->
  <!--        </div>-->
  <!--      </div>-->
  <!--    </div>-->

  <!--    <div class="row mt-5 pt-5">-->
  <!--      <div class="col-md-6">-->
  <!--        <div class="content d-flex flex-column justify-content-center h-100 text-center">-->
  <!--          <h2>Automated Notifications</h2>-->
  <!--          <p>Automated notifications for various crucial functions, including appointment reminders, prescription-->
  <!--            refills, and follow-up care alerts. This automation plays a pivotal role in reducing the workload of-->
  <!--            reception staff, streamlining clinic operations, and effectively trimming operational costs. The benefits-->
  <!--            are twofold: it enhances operational efficiency and ensures that patients receive timely and important-->
  <!--            information, contributing to an overall improved patient experience.</p>-->
  <!--        </div>-->
  <!--      </div>-->
  <!--      <div class="col-md-6">-->
  <!--        <div class="image">-->
  <!--          <img src="{{ asset('frontend/images/3.png') }}" class="img-fluid rounded-4" alt="" />-->
  <!--        </div>-->
  <!--      </div>-->
  <!--    </div>-->

  <!--    <div class="row mt-5 pt-5">-->
  <!--      <div class="col-md-6">-->
  <!--        <div class="image">-->
  <!--          <img src="{{ asset('frontend/images/4.png') }}" class="img-fluid rounded-4" alt="" />-->
  <!--        </div>-->
  <!--      </div>-->
  <!--      <div class="col-md-6">-->
  <!--        <div class="content d-flex flex-column justify-content-center h-100 text-center">-->
  <!--          <h2>Personalized Messaging</h2>-->
  <!--          <p>Entails sending customized treatment plans, medication reminders, and follow-up messages following a-->
  <!--            patient's visit. This level of individualized communication not only fosters patient loyalty but also-->
  <!--            significantly elevates the overall patient experience. Patients appreciate the care and attention to-->
  <!--            detail, which leads to a stronger and more lasting connection with the clinic.</p>-->
  <!--        </div>-->
  <!--      </div>-->
  <!--    </div>-->

  <!--    <div class="row mt-5 pt-5">-->
  <!--      <div class="col-md-6">-->
  <!--        <div class="content d-flex flex-column justify-content-center h-100 text-center">-->
  <!--          <h2>Enhanced Patient Experience</h2>-->
  <!--          <p>Clinics have the capacity to substantially enhance the overall patient experience. For instance, clinics-->
  <!--            can reach out to patients to inquire about their health post-visit, offer valuable medical guidance, and-->
  <!--            extend support throughout their recovery journey. This elevated level of care not only bolsters the-->
  <!--            clinic's reputation but also serves as a compelling magnet for attracting new patients. Patient-->
  <!--            satisfaction becomes a hallmark of the clinic, leading to its continued growth and success.</p>-->
  <!--        </div>-->
  <!--      </div>-->
  <!--      <div class="col-md-6">-->
  <!--        <div class="image">-->
  <!--          <img src="{{ asset('frontend/images/5.png') }}" class="img-fluid rounded-4" alt="" />-->
  <!--        </div>-->
  <!--      </div>-->
  <!--    </div>-->
  <!--  </div>-->
  <!--</section>-->
  
  <!-- ============================================================
     SECTION 1 — WHATSAPP AUTOMATION
============================================================ -->
    <section
      class="td-wa-section"
      id="whatsapp-automation"
      aria-label="WhatsApp appointment automation"
    >
      <div class="container">
        <div class="td-wa-header">
          <!--<span class="td-badge td-badge--green"-->
          <!--  >WhatsApp Automation for Clinics</span-->
          <!-->
          <h2>
            From patient message<br />to
            <span class="td-hl-green">confirmed appointment.</span>
          </h2>
          <p>
            TatkalDoctor connects your clinic to WhatsApp using the official
            Meta Cloud API — eliminating phone-tag, missed calls, and manual
            slot management.
          </p>
        </div>

        <div class="td-wa-grid">
          <!-- Phone -->
          <div class="td-phone-col">
            <div class="td-flow-badge">
              <span class="td-badge-dot"></span>
              <span id="td-badge-txt">Booking appointment</span>
            </div>
            <div class="td-device">
              <div class="td-device-frame">
                <!-- Status bar -->
                <div class="td-statusbar">
                  <span id="td-clock" style="font-variant-numeric: tabular-nums"
                    >9:41</span
                  >
                  <div class="td-sb-icons">
                    <svg width="15" height="11" viewBox="0 0 15 11" fill="none">
                      <rect
                        x="0"
                        y="4"
                        width="3"
                        height="7"
                        rx=".5"
                        fill="currentColor"
                        opacity=".4"
                      />
                      <rect
                        x="4"
                        y="2.5"
                        width="3"
                        height="8.5"
                        rx=".5"
                        fill="currentColor"
                        opacity=".6"
                      />
                      <rect
                        x="8"
                        y="1"
                        width="3"
                        height="10"
                        rx=".5"
                        fill="currentColor"
                        opacity=".8"
                      />
                      <rect
                        x="12"
                        y="0"
                        width="3"
                        height="11"
                        rx=".5"
                        fill="currentColor"
                      />
                    </svg>
                    <svg width="16" height="11" viewBox="0 0 16 11" fill="none">
                      <path
                        d="M8 2.5C10.2 2.5 12.1 3.5 13.4 5L14.8 3.5C13.1 1.8 10.7.8 8 .8S2.9 1.8 1.2 3.5L2.6 5C3.9 3.5 5.8 2.5 8 2.5Z"
                        fill="currentColor"
                        opacity=".4"
                      />
                      <path
                        d="M8 5.5C9.4 5.5 10.6 6.1 11.5 7L13 5.4C11.7 4.1 10 3.3 8 3.3S4.3 4.1 3 5.4L4.5 7C5.4 6.1 6.6 5.5 8 5.5Z"
                        fill="currentColor"
                        opacity=".7"
                      />
                      <circle cx="8" cy="9.5" r="1.5" fill="currentColor" />
                    </svg>
                    <svg width="25" height="11" viewBox="0 0 25 11" fill="none">
                      <rect
                        x=".5"
                        y=".5"
                        width="21"
                        height="10"
                        rx="2.5"
                        stroke="currentColor"
                        opacity=".35"
                      />
                      <rect
                        x="2"
                        y="2"
                        width="17"
                        height="7"
                        rx="1.5"
                        fill="currentColor"
                      />
                      <path
                        d="M23 3.5v4a2 2 0 0 0 0-4z"
                        fill="currentColor"
                        opacity=".4"
                      />
                    </svg>
                  </div>
                </div>
                <!-- WA header -->
                <div class="td-wa-hdr">
                  <button aria-label="Back">
                    <svg width="11" height="18" viewBox="0 0 11 19" fill="none">
                      <path
                        d="M9.5 1 1.5 9.5 9.5 18"
                        stroke="white"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </button>
                  <div class="td-wa-av">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                      <path
                        d="M11 11.5C13.2 11.5 15 9.7 15 7.5S13.2 3.5 11 3.5 7 5.3 7 7.5s1.8 4 4 4zm0 2c-2.7 0-8 1.4-8 4v1.5h16V17.5c0-2.6-5.3-4-8-4z"
                        fill="white"
                      />
                    </svg>
                  </div>
                  <div style="flex: 1; min-width: 0">
                    <div class="td-wa-nm">Tatkal Doctor</div>
                    <div class="td-wa-st">
                      <span class="td-online"></span>Online
                    </div>
                  </div>
                  <div class="td-wa-acts">
                    <button aria-label="Video call">
                      <svg
                        width="20"
                        height="14"
                        viewBox="0 0 20 14"
                        fill="none"
                      >
                        <path
                          d="M13 1H2a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM19 2.5l-4 3.5v1.5l4 3.5v-8.5z"
                          fill="rgba(255,255,255,.9)"
                        />
                      </svg>
                    </button>
                    <button aria-label="Call">
                      <svg
                        width="18"
                        height="18"
                        viewBox="0 0 18 18"
                        fill="none"
                      >
                        <path
                          d="M3.6 1H1a1 1 0 0 0-1 1c0 8.8 7.2 16 16 16a1 1 0 0 0 1-1v-2.6a1 1 0 0 0-.8-1l-3.2-.7a1 1 0 0 0-1 .4l-1.4 1.8C7.9 13.4 4.6 10.1 3.1 6L5 4.5a1 1 0 0 0 .4-1L4.6 1.8A1 1 0 0 0 3.6 1z"
                          fill="rgba(255,255,255,.9)"
                        />
                      </svg>
                    </button>
                  </div>
                </div>
                <!-- Chat -->
                <div class="td-chat" id="td-chat" role="log" aria-live="polite">
                  <div class="td-date-sep" id="td-date-sep">
                    <span>Today</span>
                  </div>
                  <div class="td-typ-wrap" id="td-typ-wrap">
                    <div class="td-typ" id="td-typ">
                      <span></span><span></span><span></span>
                    </div>
                  </div>
                  <div class="td-fade" id="td-fade"></div>
                </div>
                <!-- Input -->
                <div class="td-wa-input">
                  <button aria-label="Emoji">
                    <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                      <circle
                        cx="11"
                        cy="11"
                        r="10"
                        stroke="#8696a0"
                        stroke-width="1.5"
                      />
                      <circle cx="7.5" cy="9" r="1.2" fill="#8696a0" />
                      <circle cx="14.5" cy="9" r="1.2" fill="#8696a0" />
                      <path
                        d="M7 13.5c.8 1.6 2.2 2.5 4 2.5s3.2-.9 4-2.5"
                        stroke="#8696a0"
                        stroke-width="1.5"
                        stroke-linecap="round"
                      />
                    </svg>
                  </button>
                  <div class="td-input-field">Type a message</div>
                  <button aria-label="Attach">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                      <path
                        d="M17.5 9.2L9.8 16.9a5 5 0 0 1-7.1-7L10.2 2.4a3.3 3.3 0 1 1 4.7 4.7L7.4 14.6a1.7 1.7 0 0 1-2.4-2.4l7-7"
                        stroke="#8696a0"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </button>
                  <button aria-label="Voice message">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                      <circle cx="14" cy="14" r="14" fill="#00a884" />
                      <path
                        d="M14 7a3 3 0 0 0-3 3v4a3 3 0 0 0 6 0v-4a3 3 0 0 0-3-3z"
                        fill="white"
                      />
                      <path
                        d="M8 14c0 3.3 2.7 6 6 6s6-2.7 6-6"
                        stroke="white"
                        stroke-width="1.5"
                        stroke-linecap="round"
                      />
                      <line
                        x1="14"
                        y1="20"
                        x2="14"
                        y2="22.5"
                        stroke="white"
                        stroke-width="1.5"
                        stroke-linecap="round"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- /phone -->

          <!-- Steps -->
          <div class="td-steps-col">
            <div class="td-steps" role="list">
              <div
                class="td-step active"
                id="td-step-0"
                data-flow="0"
                role="listitem"
              >
                <div class="td-step-track">
                  <div class="td-step-icon">
                    <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                      <rect
                        x="1"
                        y="2"
                        width="14"
                        height="13"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.5"
                      />
                      <path
                        d="M5 1v3M11 1v3M1 7h14"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                      />
                    </svg>
                  </div>
                  <div class="td-step-line"></div>
                </div>
                <div class="td-step-body">
                  <div class="td-step-label">Appointment Booking</div>
                  <div class="td-step-desc">
                    Patient selects department, picks a slot, and gets instant
                    confirmation — no phone call needed.
                  </div>
                  <span class="td-step-pill"
                    ><svg
                      width="11"
                      height="11"
                      viewBox="0 0 11 11"
                      fill="none"
                    >
                      <polyline
                        points="2,6 4.5,8.5 9,3"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      /></svg
                    >143 bookings / day avg.</span
                  >
                </div>
              </div>

              <div class="td-step" id="td-step-1" data-flow="1" role="listitem">
                <div class="td-step-track">
                  <div class="td-step-icon">
                    <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                      <path
                        d="M8 1a6 6 0 1 0 0 12A6 6 0 0 0 8 1zM8 4v4l2.5 1.5"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                      />
                    </svg>
                  </div>
                  <div class="td-step-line"></div>
                </div>
                <div class="td-step-body">
                  <div class="td-step-label">Smart Reminders</div>
                  <div class="td-step-desc">
                    Automated reminders sent 24 h and 1 h before every
                    appointment. Patients confirm or reschedule in one tap.
                  </div>
                  <span class="td-step-pill"
                    ><svg
                      width="11"
                      height="11"
                      viewBox="0 0 11 11"
                      fill="none"
                    >
                      <polyline
                        points="2,6 4.5,8.5 9,3"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      /></svg
                    >–31 % no-shows</span
                  >
                </div>
              </div>

              <div class="td-step" id="td-step-2" data-flow="2" role="listitem">
                <div class="td-step-track">
                  <div class="td-step-icon">
                    <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                      <path
                        d="M3 2h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H5l-3 3V3a1 1 0 0 1 1-1z"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </div>
                  <div class="td-step-line"></div>
                </div>
                <div class="td-step-body">
                  <div class="td-step-label">Reports &amp; Follow-ups</div>
                  <div class="td-step-desc">
                    Share lab reports, prescriptions, and recovery instructions
                    directly over WhatsApp after every visit.
                  </div>
                  <span class="td-step-pill"
                    ><svg
                      width="11"
                      height="11"
                      viewBox="0 0 11 11"
                      fill="none"
                    >
                      <polyline
                        points="2,6 4.5,8.5 9,3"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      /></svg
                    >2.4× follow-up bookings</span
                  >
                </div>
              </div>

              <div class="td-step" id="td-step-3" data-flow="3" role="listitem">
                <div class="td-step-track">
                  <div class="td-step-icon">
                    <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                      <path
                        d="M2 2.8C2.9 1.7 4.3 1 5.8 1c1 0 1.9.4 2.7 1l-1.4 1.4A2.4 2.4 0 0 0 5.8 3C4.3 3 3 4.3 3 5.8c0 .6.2 1.1.5 1.6L2.1 8.8A5 5 0 0 1 1 5.8C1 4.6 1.4 3.5 2 2.8zM14 13.2C13.1 14.3 11.7 15 10.2 15c-1 0-1.9-.4-2.7-1l1.4-1.4c.4.3.8.4 1.3.4 1.5 0 2.8-1.3 2.8-2.8 0-.6-.2-1.1-.5-1.6l1.4-1.4c.7.8 1.1 1.9 1.1 3 0 1.2-.4 2.3-1 3zM1 1l14 14"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                      />
                    </svg>
                  </div>
                  <div class="td-step-line last"></div>
                </div>
                <div class="td-step-body">
                  <div class="td-step-label">Missed Call Recovery</div>
                  <div class="td-step-desc">
                    When patients can't get through, TatkalDoctor auto-sends a
                    WhatsApp message to re-engage and book instantly.
                  </div>
                  <span class="td-step-pill"
                    ><svg
                      width="11"
                      height="11"
                      viewBox="0 0 11 11"
                      fill="none"
                    >
                      <polyline
                        points="2,6 4.5,8.5 9,3"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      /></svg
                    >63 % calls recovered</span
                  >
                </div>
              </div>
            </div>

            <div class="td-ctas">
              <a href="{{ route('schedule.demo') }}" class="td-btn td-btn--green">Book a live demo</a>
              <a href="{{ route('whatsApp-ai-agent-hospital-clinic') }}" class="td-btn td-btn--outline">See all features</a>
            </div>
            <div class="td-proof">
              <div class="td-proof-avs">
                <span style="background: #d1fae5; color: #065f46">RK</span>
                <span style="background: #dbeafe; color: #1e40af">PM</span>
                <span style="background: #fef3c7; color: #92400e">SJ</span>
              </div>
              <span class="td-proof-txt"
                ><strong>400+ clinics</strong> automate bookings on WhatsApp
                daily</span
              >
            </div>
          </div>
          <!-- /steps col -->
        </div>
      </div>
    </section>

  {{-- ============================================================
       STATS NEW SECTION
  ============================================================ --}}
  <section class="stats-new">
    <div class="container">
      <h2>MEDICAL CLINIC SCHEDULES 2.5X</h2>
      <p class="subtitle">MORE APPOINTMENTS WITH VOICE AI</p>
      <div class="row">
        <div class="col-md-3 col-6">
          <div class="stat-card">
            <div class="stat-number" data-target="60" data-prefix="+" data-suffix="%">0</div>
            <div class="stat-label">Scheduling<br />Efficiency</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card">
            <div class="stat-number" data-target="30" data-prefix="-" data-suffix="%">0</div>
            <div class="stat-label">No-show<br />Rates</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card">
            <div class="stat-number" data-target="2.5" data-suffix="x">0</div>
            <div class="stat-label">Qualified<br />Appointments</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card">
            <div class="stat-number" data-target="25" data-prefix="+" data-suffix="%">0</div>
            <div class="stat-label">Patient<br />Satisfaction</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ============================================================
       VOICE AI AGENTS SECTION
  ============================================================ --}}
  <section class="voice-ai-section" id="support">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h2>
            <span class="blue-text">VOICE AI AGENTS FOR</span><br />HEALTHCARE CALL ROUTING
          </h2>
          <p class="lead">
            Answer every patient call, route it automatically, and reduce no-shows. TatkalDoctor's Voice AI Agents
            simplify transfers across 100+ clinics and departments, so your team can scale care access without scaling
            headcount.
          </p>
          <p class="features-text">
            Inbound Appointment Booking | Lead Qualification | Handling FAQ
          </p>
        </div>
        <div class="col-lg-6">
          <img src="{{ asset('frontend/images/6.png') }}" alt="Voice AI Agent" class="img-fluid rounded-4" />
        </div>
      </div>
    </div>
  </section>
  
  
  
  <!-- ============================================================
     SECTION 2 — VOICE AGENT  (redesigned)
============================================================ -->
    <section class="td-voice-section" id="voice-demo" aria-labelledby="voice-h">
      <div class="container">
        <!-- Header -->
        <div class="td-voice-hdr">
          <!--<span class="td-badge">Voice AI Demo</span>-->
          <h2 id="voice-h">
            Hear Our <span class="td-hl-green">AI Voice Agent</span> in Action
          </h2>
          <p>
            A real call recording — our AI books appointments, answers queries,
            and routes patients automatically. No humans needed.
          </p>
        </div>

        <!-- Player -->
        <div class="td-player-outer">
          <div class="td-player-card">
            <!-- Top row: avatar / info / live badge -->
            <div class="td-player-top">
              <div class="td-player-av">
                <svg
                  width="26"
                  height="26"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path
                    d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"
                  />
                  <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
                  <line x1="12" y1="19" x2="12" y2="23" />
                  <line x1="8" y1="23" x2="16" y2="23" />
                </svg>
              </div>
              <div class="td-player-info">
                <div class="td-player-title">
                  Healthcare Appointment Booking Call
                </div>
                <div class="td-player-sub">
                  AI Voice Agent
                  <span class="td-player-sub-dot"></span>
                  Hindi + English
                  <span class="td-player-sub-dot"></span>
                  Live Demo
                </div>
              </div>
              <div class="td-player-live">
                <span class="td-player-live-dot"></span>
                Real
              </div>
            </div>

            <!-- Body: [waveform + seek + play btn] LEFT  |  [vertical volume] RIGHT -->
            <div class="td-player-body">
              <!-- Left: waveform → seek bar → centered play btn -->
              <div class="td-player-main">
                <!-- Waveform -->
                <div
                  class="td-wf"
                  id="td-wf"
                  aria-hidden="true"
                  role="presentation"
                >
                  <!-- bars injected by JS -->
                </div>

                <!-- Seek bar -->
                <div class="td-seek-row">
                  <span class="td-seek-time" id="td-cur">0:00</span>
                  <input
                    type="range"
                    class="td-seek"
                    id="td-seek"
                    min="0"
                    max="100"
                    value="0"
                    step="0.1"
                    aria-label="Seek audio"
                  />
                  <span class="td-seek-time" id="td-dur">0:00</span>
                </div>

                <!-- Play button — centred below the seek bar -->
                <button
                  class="td-play-btn"
                  id="td-play"
                  aria-label="Play voice demo"
                >
                  <svg
                    id="td-play-ico"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                  >
                    <polygon points="6 3 20 12 6 21 6 3" />
                  </svg>
                </button>
              </div>

              <!-- Right: vertical volume slider -->
              <div class="td-vol-col">
                <!-- loud icon top -->
                <div class="td-vol-icon">
                  <svg
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                  >
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" />
                    <path
                      d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"
                    />
                  </svg>
                </div>
                <input
                  type="range"
                  class="td-vol"
                  id="td-vol"
                  min="0"
                  max="100"
                  value="80"
                  aria-label="Volume"
                />
                <!-- quiet icon bottom -->
                <div class="td-vol-icon">
                  <svg
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                  >
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" />
                  </svg>
                </div>
              </div>
            </div>
            <!-- /body -->

            <!-- Note -->
            <!--<p class="td-player-note">-->
            <!--  <svg-->
            <!--    width="12"-->
            <!--    height="12"-->
            <!--    viewBox="0 0 16 16"-->
            <!--    fill="none"-->
            <!--    stroke-width="1.3"-->
            <!--    stroke-linecap="round"-->
            <!--  >-->
            <!--    <circle cx="8" cy="8" r="7" stroke="currentColor" />-->
            <!--    <path d="M8 7v5M8 5v.5" stroke="currentColor" />-->
            <!--  </svg>-->
            <!--  Audio pauses automatically when you switch tabs or scroll away.-->
            <!--</p>-->

            <!-- CTA strip -->
            <div class="td-player-ctas">
              <a href="{{ route('schedule.demo') }}" class="td-btn td-btn--green">
                <svg
                  width="15"
                  height="15"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.2"
                  stroke-linecap="round"
                >
                  <path
                    d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 11.69 18.9a19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3.09a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.34 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"
                  />
                </svg>
                Book a Live Demo
              </a>
              <a href="{{ route('features') }}" class="td-btn--ghost">
                Explore AI Calling
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M5 12h14M12 5l7 7-7 7" />
                </svg>
              </a>
            </div>

            <!-- Audio element -->
            <audio id="td-audio" preload="metadata">
              <source
                src="/frontend/audio/voice-agent-demo.mp3"
                type="audio/mpeg"
              />
            </audio>
          </div>
        </div>
        <!-- /player -->

        <!-- Feature cards -->
        <div class="td-vf-grid">
          <div class="td-vf-card">
            <div class="td-vf-ico">
              <svg
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
            </div>
            <div class="td-vf-t">24 / 7 Availability</div>
            <div class="td-vf-d">
              Never misses a patient call — answers instantly at midnight or
              during lunch break.
            </div>
          </div>
          <div class="td-vf-card">
            <div
              class="td-vf-ico"
              style="background: var(--blue-lt); color: var(--blue)"
            >
              <svg
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
                />
              </svg>
            </div>
            <div class="td-vf-t">Multilingual Support</div>
            <div class="td-vf-d">
              Converses fluently in Hindi, English, and regional languages —
              understands patient tone and intent.
            </div>
          </div>
          <div class="td-vf-card">
            <div class="td-vf-ico">
              <svg
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <rect x="2" y="3" width="20" height="14" rx="2" />
                <line x1="8" y1="21" x2="16" y2="21" />
                <line x1="12" y1="17" x2="12" y2="21" />
              </svg>
            </div>
            <div class="td-vf-t">Smart Slot Booking</div>
            <div class="td-vf-d">
              Checks live availability, books or reschedules appointments, and
              confirms instantly — no hold time.
            </div>
          </div>
          <div class="td-vf-card">
            <div class="td-vf-ico" style="background: #fef2f2; color: #ef4444">
              <svg
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.36 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.25 1H6.5a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.5 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.34 1.85.573 2.81.7A2 2 0 0 1 22 16z"
                />
              </svg>
            </div>
            <div class="td-vf-t">Emergency Transfer</div>
            <div class="td-vf-d">
              Detects urgency in patient tone and seamlessly routes emergency
              calls to a live human agent.
            </div>
          </div>
          <div class="td-vf-card">
            <div
              class="td-vf-ico"
              style="background: var(--blue-lt); color: var(--blue)"
            >
              <svg
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
            </div>
            <div class="td-vf-t">FAQ Handling</div>
            <div class="td-vf-d">
              Answers common questions about timings, fees, and doctors —
              trained on your clinic's information.
            </div>
          </div>
          <div class="td-vf-card">
            <div class="td-vf-ico" style="background: #f3f0ff; color: #7c3aed">
              <svg
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                <polyline points="17 6 23 6 23 12" />
              </svg>
            </div>
            <div class="td-vf-t">100+ Clinic Routing</div>
            <div class="td-vf-d">
              Routes calls across departments, specialties, and locations
              automatically — zero menu navigation needed.
            </div>
          </div>
          <div class="td-vf-card">
            <div class="td-vf-ico" style="background: #fff7ed; color: #d97706">
              <svg
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
              </svg>
            </div>
            <div class="td-vf-t">Call Summaries</div>
            <div class="td-vf-d">
              Auto-generates structured summaries of each call — sent to clinic
              dashboard and WhatsApp instantly.
            </div>
          </div>
          <div class="td-vf-card">
            <div class="td-vf-ico">
              <svg
                width="22"
                height="22"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
              </svg>
            </div>
            <div class="td-vf-t">No Human Needed</div>
            <div class="td-vf-d">
              Handles 95% of routine calls autonomously — your staff focuses
              only on complex cases that truly need them.
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- ============================================================
     SECTION 3 — ANALYTICS
============================================================ -->
    <section class="td-analytics" id="analytics" aria-labelledby="an-h">
      <div class="container">
        <div class="td-an-hdr">
          <!--<span class="td-badge td-badge--green">Real Results</span>-->
          <h2 id="an-h">
            Performance That <span class="td-hl-green">Speaks for Itself</span>
          </h2>
          <p>
            Live metrics from clinics using TatkalDoctor's WhatsApp &amp; Voice
            AI automation.
          </p>
        </div>

        <!-- Charts -->
        <div class="td-charts">
          <div class="td-chart-card">
            <div class="td-chart-card-title">Monthly Appointments Booked</div>
            <div class="td-chart-legend">
              <span
                ><span class="td-leg-dot" style="background: #00c796"></span
                >With Tatkal Doctor</span
              >
              <span
                ><span class="td-leg-dot" style="background: #cbd5e1"></span
                >Before Automation</span
              >
            </div>
            <div class="td-chart-wrap">
              <canvas
                id="cMonthly"
                aria-label="Monthly appointments bar chart"
              ></canvas>
            </div>
          </div>
          <div class="td-chart-card">
            <div class="td-chart-card-title">Call Answer Rate</div>
            <div class="td-chart-legend">
              <span
                ><span class="td-leg-dot" style="background: #0066ff"></span>AI
                Voice Agent</span
              >
              <span
                ><span class="td-leg-dot" style="background: #fbbf24"></span
                >Manual Staff</span
              >
            </div>
            <div class="td-chart-wrap">
              <canvas
                id="cCalls"
                aria-label="Call answer rate line chart"
              ></canvas>
            </div>
          </div>
          <div class="td-chart-card td-chart-donut">
            <div class="td-chart-card-title">No-show Reduction</div>
            <div class="td-chart-wrap td-chart-wrap--donut">
              <canvas id="cNoshow" aria-label="No-show donut chart"></canvas>
            </div>
            <div class="td-donut-overlay">
              <div class="td-donut-big">30%</div>
              <div class="td-donut-sm">Fewer No-shows</div>
            </div>
            <div
              class="td-chart-legend"
              style="justify-content: center; margin-top: 10px"
            >
              <span
                ><span class="td-leg-dot" style="background: #00c796"></span
                >Attended</span
              >
              <span
                ><span class="td-leg-dot" style="background: #e2e8f0"></span>Was
                No-show</span
              >
              <span
                ><span class="td-leg-dot" style="background: #0066ff"></span
                >Saved</span
              >
            </div>
          </div>
          <div class="td-chart-card">
            <div class="td-chart-card-title">Booking Channel Split</div>
            <div class="td-chart-legend">
              <span
                ><span class="td-leg-dot" style="background: #00c796"></span
                >WhatsApp</span
              >
              <span
                ><span class="td-leg-dot" style="background: #0066ff"></span
                >Voice AI</span
              >
              <span
                ><span class="td-leg-dot" style="background: #f59e0b"></span
                >Walk-in</span
              >
            </div>
            <div class="td-chart-wrap">
              <canvas
                id="cChannels"
                aria-label="Booking channel bar chart"
              ></canvas>
            </div>
          </div>
        </div>
      </div>
    </section>
  

  {{-- ============================================================
       BLOGS SECTION — Dynamic (Laravel se) with fallback
  ============================================================ --}}
  <section class="blogs-section">
    <div class="container">
      <h2 class="text-center"><span class="blue-text">BLOGS</span></h2>
      <h3 class="text-center mb-5">
        IMPROVING MULTI-CLINIC MANAGEMENT WITH WHATSAPP AUTOMATION
      </h3>
      <div class="row g-4">
        @forelse($latestPosts as $post)
          <div class="col-md-4">
            <div class="blog-card">
              <div class="blog-image">
                @if($post->thumbnail)
                  <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="img-fluid" />
                @else
                  <img src="{{ asset('frontend/images/7.png') }}" alt="{{ $post->title }}" class="img-fluid" />
                @endif
                @if($post->category)
                  <span class="blog-tag">{{ $post->category->title }}</span>
                @endif
              </div>
              <div class="blog-content">
                <h4>{{ Str::limit($post->title, 80) }}</h4>
                <p class="blog-date">
                  {{ $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y') }}
                </p>
                <a href="{{ route('blog.detail', $post->slug) }}" class="btn btn-sm btn-primary mt-2">Read More</a>
              </div>
            </div>
          </div>
        @empty
          {{-- Agar koi blog nahi hai to static cards dikhao --}}
          <div class="col-md-4">
            <div class="blog-card">
              <div class="blog-image">
                <img src="{{ asset('frontend/images/7.png') }}" alt="WhatsApp Automation" class="img-fluid" />
                <span class="blog-tag">WhatsApp AUTOMATION</span>
              </div>
              <div class="blog-content">
                <h4>How Healthcare Software in New Delhi is Improving Multi-Clinic Management with WhatsApp Automation</h4>
                <p class="blog-date">June 13, 2025</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="blog-card">
              <div class="blog-image">
                <img src="{{ asset('frontend/images/8.png') }}" alt="Digitize Pharmacy" class="img-fluid" />
                <span class="blog-tag orange">Digitize Your PHARMACY</span>
              </div>
              <div class="blog-content">
                <h4>How to Digitize Your Pharmacy in the India with Digital Solutions</h4>
                <p class="blog-date">May 6, 2025</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="blog-card">
              <div class="blog-image">
                <img src="{{ asset('frontend/images/9.png') }}" alt="Patient Management" class="img-fluid" />
                <span class="blog-tag red">Transforming Patient MANAGEMENT</span>
              </div>
              <div class="blog-content">
                <h4>Medical Software in Bangalore: Transforming Patient Management</h4>
                <p class="blog-date">February 7, 2025</p>
              </div>
            </div>
          </div>
        @endforelse
      </div>

      @if(isset($latestPosts) && $latestPosts->count() > 0)
        <div class="text-center mt-4">
          <a href="{{ route('blog.index') }}" class="btn btn-primary btn-lg">View All Posts</a>
        </div>
      @endif
    </div>
  </section>

  {{-- ============================================================
       FAQ SECTION
  ============================================================ --}}
  <section class="faq-section" id="demo">
    <div class="container">
      <div class="row">
        <div class="col-lg-5">
          <h2 class="blue-text">FAQ's</h2>
          <h3>
            Everything You Need to Know About Tatkal Doctor's AI Voice Agents
          </h3>
        </div>
        <div class="col-lg-7">
          <div class="accordion" id="faqAccordion">

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                  How does Tatkal Doctor route patients to the correct clinic or department without phone menus?
                </button>
              </h2>
              <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Our AI Voice Agent uses natural language processing to understand patient needs and automatically
                  routes calls to the appropriate clinic or department without requiring traditional phone menus.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                  What happens if a patient just says something like "I need to reschedule" or "I have a question"?
                </button>
              </h2>
              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  The AI agent intelligently identifies the intent and asks relevant follow-up questions to understand
                  their needs and provide appropriate assistance.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                  Can Tatkal Doctor route calls after hours, on weekends, or during high volume?
                </button>
              </h2>
              <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Yes, our AI agents work 24/7 and can handle unlimited concurrent calls, ensuring no patient call
                  goes unanswered.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                  How long does setup take across a large clinic network?
                </button>
              </h2>
              <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Setup typically takes 1-2 weeks for large clinic networks, including configuration, testing, and
                  staff training.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                  Do we have to change our phone system?
                </button>
              </h2>
              <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  No, our solution integrates seamlessly with your existing phone system without requiring any hardware
                  changes.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                  Can our team update call flows without writing code?
                </button>
              </h2>
              <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Yes, our intuitive dashboard allows your team to easily update call flows, scripts, and routing rules
                  without any coding knowledge.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                  Is Tatkal Doctor secure and healthcare compliant?
                </button>
              </h2>
              <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  Yes, we are fully HIPAA compliant and use enterprise-grade encryption to ensure all patient data
                  remains secure and confidential.
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ============================================================
       COUNTER ANIMATION SCRIPT
  ============================================================ --}}
  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {

      // Top banner: respect reduced motion
      const topBannerCarousel = document.getElementById('tdHomeTopBannerCarousel');
      if (topBannerCarousel && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const inst = bootstrap.Carousel.getInstance(topBannerCarousel) || bootstrap.Carousel.getOrCreateInstance(topBannerCarousel);
        inst.pause();
      }

      // Counter Animation
      function animateCounter(element) {
        const target = parseFloat(element.getAttribute('data-target'));
        const prefix = element.getAttribute('data-prefix') || '';
        const suffix = element.getAttribute('data-suffix') || '';
        const duration = 2000;
        const increment = target / (duration / 16);
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

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
            entry.target.classList.add('counted');
            animateCounter(entry.target);
          }
        });
      }, { threshold: 0.5 });

      document.querySelectorAll('.stat-number[data-target]').forEach(stat => observer.observe(stat));
    });
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
  
  <script>
      /* ============================================================
   TATKAL DOCTOR — SECTIONS JS
============================================================ */
      (function () {
        "use strict";

        /* ── helpers ── */
        var chat = document.getElementById("td-chat"),
          typWrap = document.getElementById("td-typ-wrap"),
          typEl = document.getElementById("td-typ"),
          fadeEl = document.getElementById("td-fade"),
          badgeTxt = document.getElementById("td-badge-txt"),
          dateSep = document.getElementById("td-date-sep"),
          clockEl = document.getElementById("td-clock");

        if (!chat) return;

        var T = [],
          loop = null,
          fi = 0;
        function sched(fn, ms) {
          var id = setTimeout(fn, ms);
          T.push(id);
          return id;
        }
        function killAll() {
          T.forEach(clearTimeout);
          T = [];
          if (loop) {
            clearTimeout(loop);
            loop = null;
          }
        }
        function ftime(s) {
          s = Math.floor(s || 0);
          var m = Math.floor(s / 60),
            sec = s % 60;
          return m + ":" + (sec < 10 ? "0" + sec : sec);
        }

        /* clock */
        function clk() {
          if (!clockEl) return;
          var d = new Date(),
            h = d.getHours(),
            m = d.getMinutes();
          clockEl.textContent = h + ":" + (m < 10 ? "0" + m : m);
        }
        clk();
        setInterval(clk, 30000);

        /* now string */
        function now() {
          var d = new Date(),
            h = d.getHours(),
            m = d.getMinutes(),
            a = h >= 12 ? "PM" : "AM";
          h = h % 12 || 12;
          return h + ":" + (m < 10 ? "0" + m : m) + " " + a;
        }

        /* ticks svg */
        function tick(b) {
          var c = b ? "#53bdeb" : "#92a0a0";
          return (
            '<svg viewBox="0 0 18 10" fill="none"><path d="M1 5l3.5 3.5L11 1" stroke="' +
            c +
            '" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 5l3.5 3.5L16 1" stroke="' +
            c +
            '" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
          );
        }

        function showTyp() {
          typEl.classList.add("on");
          chat.scrollTop = 99999;
        }
        function hideTyp() {
          typEl.classList.remove("on");
        }

        function insMsg(side, html) {
          var el = document.createElement("div");
          el.className = "td-msg " + (side === "in" ? "in" : "out");
          el.innerHTML =
            html +
            '<div class="td-msg-meta"><span class="td-msg-time">' +
            now() +
            "</span>" +
            (side === "out"
              ? '<span class="td-msg-tick">' + tick(true) + "</span>"
              : "") +
            "</div>";
          chat.insertBefore(el, typWrap);
          requestAnimationFrame(function () {
            requestAnimationFrame(function () {
              el.classList.add("vis");
              chat.scrollTop = 99999;
            });
          });
        }

        function qIn(h, d) {
          sched(function () {
            showTyp();
          }, d - 700);
          sched(function () {
            hideTyp();
            insMsg("in", h);
          }, d);
        }
        function qOut(h, d) {
          sched(function () {
            insMsg("out", h);
          }, d);
        }
        function qr(arr) {
          return arr
            .map(function (t) {
              return '<span class="td-qr">' + t + "</span>";
            })
            .join("");
        }
        function fc(t, s, b) {
          return (
            '<div class="td-fc"><div class="td-fc-t">📋 ' +
            t +
            '</div><div class="td-fc-s">' +
            s +
            '</div><div class="td-fc-b">' +
            b +
            "</div></div>"
          );
        }

        /* set active step + sync badge */
        var BADGES = [
          "Booking appointment",
          "Sending reminders",
          "Post-visit follow-up",
          "Recovering missed call",
        ];
        function setStep(idx) {
          for (var i = 0; i < 4; i++) {
            var s = document.getElementById("td-step-" + i);
            if (s) s.classList.remove("active");
          }
          var t = document.getElementById("td-step-" + idx);
          if (t) t.classList.add("active");
          if (badgeTxt) badgeTxt.textContent = BADGES[idx];
        }

        /* reset chat */
        function resetChat(cb) {
          fadeEl.classList.add("on");
          sched(function () {
            Array.prototype.slice.call(chat.childNodes).forEach(function (k) {
              if (k !== typWrap && k !== fadeEl && k !== dateSep)
                chat.removeChild(k);
            });
            hideTyp();
            chat.scrollTop = 0;
            sched(function () {
              fadeEl.classList.remove("on");
              if (cb) cb();
            }, 180);
          }, 420);
        }

        /* ── flows ── */
        function flowBooking(done) {
          setStep(0);
          qIn(
            "Hello! Welcome to TatkalDoctor.\nHow can I help you today?",
            600,
          );
          qIn(
            "Please choose an option:" +
              qr([
                "📅 Book an appointment",
                "📋 View my bookings",
                "💬 Talk to staff",
              ]),
            2000,
          );
          qOut("Book an appointment", 3400);
          qIn(
            "Please select a department:" +
              qr(["1. General Physician", "2. Dentist", "3. Dermatology"]),
            4800,
          );
          qOut("1", 6200);
          qIn(
            "Available slots with <b>Dr. Sharma</b><br>Tomorrow, Thu 8 May:" +
              qr(["⏰ 10:30 AM", "⏰ 11:00 AM", "⏰ 12:15 PM"]),
            7700,
          );
          qOut("11:00 AM", 9200);
          qIn(
            "✅ <b>Appointment confirmed!</b><br>Dr. Sharma · Thu 8 May · 11:00 AM<br>CityCare Clinic, Sector 14<br><small>We'll send a reminder the day before.</small>",
            10700,
          );
          sched(done, 14000);
        }

        function flowReminder(done) {
          setStep(1);
          qIn(
            "📅 <b>Appointment Reminder</b><br>You have an appointment <b>tomorrow at 11:00 AM</b> with Dr. Sharma at CityCare Clinic.",
            600,
          );
          qIn(
            "Please confirm:" +
              qr(["1. Confirm", "2. Reschedule", "3. Cancel"]),
            2200,
          );
          qOut("1", 3700);
          qIn(
            "Great! Your appointment is <b>confirmed</b>.<br>See you tomorrow at 11:00 AM. Please arrive 5 mins early. 🙂",
            5100,
          );
          qIn(
            "Need directions?" +
              qr(["Yes, share location", "No, I know the way"]),
            7000,
          );
          qOut("No, I know the way", 8400);
          qIn("Perfect! See you tomorrow.", 9700);
          sched(done, 12500);
        }

        function flowFollowup(done) {
          setStep(2);
          qIn(
            "Hi Rahul! Hope you're feeling better after your consultation with Dr. Sharma. 😊",
            600,
          );
          qIn(
            "Your blood test report is ready." +
              fc(
                "Blood Test Report",
                "Dr. Sharma's Clinic · 8 May 2025",
                "View Report →",
              ),
            2200,
          );
          qIn(
            "Dr. Sharma recommends a follow-up in 7 days. Would you like to book?" +
              qr(["📅 Yes, book follow-up", "❌ No, thanks"]),
            4100,
          );
          qOut("Yes, book follow-up", 5600);
          qIn(
            "✅ <b>Follow-up booked!</b><br>Dr. Sharma · Thu 15 May · 11:00 AM<br>You'll receive a reminder the day before.",
            7000,
          );
          sched(done, 10500);
        }

        function flowMissed(done) {
          setStep(3);
          qIn(
            "📞 <b>Missed call alert</b><br>Hi! We noticed you tried calling CityCare Clinic but couldn't connect.",
            600,
          );
          qIn(
            "How can we help you?" +
              qr([
                "📅 1 — Book appointment",
                "💬 2 — Talk to reception",
                "🕐 3 — Clinic timings",
              ]),
            2000,
          );
          qOut("1", 3500);
          qIn(
            "Which department?" +
              qr(["General Physician", "Dental", "Dermatology", "Eye Care"]),
            4900,
          );
          qOut("Dental", 6300);
          qIn(
            "<b>Next available:</b><br>Dr. Kapoor · Tomorrow 10:00 AM<br>Confirm this slot?" +
              qr(["✅ Yes, confirm", "🔄 See other times"]),
            7800,
          );
          qOut("Yes, confirm", 9200);
          qIn(
            "✅ <b>Appointment confirmed!</b><br>Dr. Kapoor · Tomorrow 10:00 AM<br>CityCare Dental Wing",
            10700,
          );
          sched(done, 14000);
        }

        var FLOWS = [flowBooking, flowReminder, flowFollowup, flowMissed];
        function runNext() {
          var fn = FLOWS[fi % FLOWS.length];
          fi++;
          fn(function () {
            loop = setTimeout(function () {
              resetChat(function () {
                sched(runNext, 500);
              });
            }, 1800);
          });
        }

        /* ── VOICE PLAYER ── */
        function initPlayer() {
          var audio = document.getElementById("td-audio"),
            playBtn = document.getElementById("td-play"),
            playIco = document.getElementById("td-play-ico"),
            seekEl = document.getElementById("td-seek"),
            curEl = document.getElementById("td-cur"),
            durEl = document.getElementById("td-dur"),
            volEl = document.getElementById("td-vol"),
            wfEl = document.getElementById("td-wf");
          if (!audio || !playBtn) return;

          /* build waveform bars — seeded speech-like pattern */
          var BARS = 52,
            heights = [],
            seed = 42;
          function rand() {
            seed = (seed * 1664525 + 1013904223) & 0xffffffff;
            return (seed >>> 0) / 0xffffffff;
          }
          for (var i = 0; i < BARS; i++) {
            var t = i / BARS,
              env = Math.sin(Math.PI * t) * 0.6 + 0.4;
            var h = Math.round(8 + env * (18 + rand() * 22));
            heights.push(h);
            var b = document.createElement("div");
            b.className = "td-wf-bar";
            b.style.height = h + "px";
            wfEl.appendChild(b);
          }

          var PLAY = '<polygon points="6 3 20 12 6 21 6 3"/>',
            PAUSE =
              '<rect x="6" y="4" width="4" height="16" rx="1.5"/><rect x="14" y="4" width="4" height="16" rx="1.5"/>';

          function setPlay(p) {
            playIco.innerHTML = p ? PAUSE : PLAY;
            playBtn.setAttribute("aria-label", p ? "Pause" : "Play voice demo");
          }

          function setSeekFill(pct) {
            if (seekEl)
              seekEl.style.setProperty(
                "--seek-fill",
                (pct * 100).toFixed(1) + "%",
              );
          }

          function setVolFill(v) {
            if (volEl) volEl.style.setProperty("--vol-fill", v + "%");
          }

          function updWf() {
            if (!audio.duration) return;
            var pct = audio.currentTime / audio.duration,
              played = Math.floor(pct * BARS);
            wfEl.querySelectorAll(".td-wf-bar").forEach(function (b, i) {
              b.classList.toggle("played", i < played);
            });
            if (seekEl) {
              seekEl.value = (pct * 100).toFixed(1);
              setSeekFill(pct);
            }
            if (curEl) curEl.textContent = ftime(audio.currentTime);
          }

          /* idle shimmer */
          var shimRaf = null,
            shimOn = false;
          function shimmer() {
            if (!shimOn) return;
            wfEl
              .querySelectorAll(".td-wf-bar:not(.played)")
              .forEach(function (b, i) {
                var h = heights[i] || 12,
                  off = Math.sin(Date.now() / 720 + i * 0.24) * 3.5;
                b.style.height = Math.max(4, h + off) + "px";
              });
            shimRaf = requestAnimationFrame(shimmer);
          }
          function startShim() {
            shimOn = true;
            shimmer();
          }
          function stopShim() {
            shimOn = false;
            cancelAnimationFrame(shimRaf);
            wfEl
              .querySelectorAll(".td-wf-bar:not(.played)")
              .forEach(function (b, i) {
                b.style.height = (heights[i] || 12) + "px";
              });
          }

          audio.addEventListener("loadedmetadata", function () {
            if (durEl) durEl.textContent = ftime(audio.duration);
          });
          audio.addEventListener("timeupdate", updWf);
          audio.addEventListener("play", function () {
            setPlay(true);
            stopShim();
          });
          audio.addEventListener("pause", function () {
            setPlay(false);
            startShim();
          });
          audio.addEventListener("ended", function () {
            setPlay(false);
            audio.currentTime = 0;
            updWf();
            startShim();
          });
          audio.volume = 0.8;
          setVolFill(80);
          startShim();

          playBtn.addEventListener("click", function () {
            audio.paused ? audio.play().catch(function () {}) : audio.pause();
          });

          if (seekEl)
            seekEl.addEventListener("input", function () {
              if (audio.duration) {
                audio.currentTime = (seekEl.value / 100) * audio.duration;
                setSeekFill(seekEl.value / 100);
              }
            });

          if (volEl)
            volEl.addEventListener("input", function () {
              audio.volume = volEl.value / 100;
              setVolFill(volEl.value);
            });

          /* click waveform to seek */
          if (wfEl)
            wfEl.addEventListener("click", function (e) {
              if (!audio.duration) return;
              var r = wfEl.getBoundingClientRect();
              audio.currentTime =
                ((e.clientX - r.left) / r.width) * audio.duration;
            });
          wfEl.style.cursor = "pointer";

          /* stop on tab hide / scroll away */
          document.addEventListener("visibilitychange", function () {
            if (document.hidden && !audio.paused) audio.pause();
          });
          var vs = document.getElementById("voice-demo");
          if (vs && window.IntersectionObserver) {
            new IntersectionObserver(
              function (e) {
                if (!e[0].isIntersecting && !audio.paused) audio.pause();
              },
              { threshold: 0.05 },
            ).observe(vs);
          }
        }

        /* ── KPI counters ── */
        function initKpi() {
          function animate(el) {
            var target = parseFloat(el.getAttribute("data-target")),
              prefix = el.getAttribute("data-prefix") || "",
              suffix = el.getAttribute("data-suffix") || "",
              isF = target % 1 !== 0,
              dur = 1800,
              start = null;
            function step(ts) {
              if (!start) start = ts;
              var p = Math.min((ts - start) / dur, 1),
                e = 1 - Math.pow(1 - p, 3),
                v = e * target;
              el.textContent =
                prefix + (isF ? v.toFixed(1) : Math.floor(v)) + suffix;
              if (p < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
          }
          if (window.IntersectionObserver) {
            var io = new IntersectionObserver(
              function (entries) {
                entries.forEach(function (e) {
                  if (e.isIntersecting && !e.target.dataset.counted) {
                    e.target.dataset.counted = "1";
                    animate(e.target);
                  }
                });
              },
              { threshold: 0.4 },
            );
            document
              .querySelectorAll(".td-kpi-val[data-target]")
              .forEach(function (el) {
                io.observe(el);
              });
          }
        }

        /* ── Charts ── */
        function initCharts() {
          if (typeof Chart === "undefined") {
            setTimeout(initCharts, 400);
            return;
          }
          Chart.defaults.font.family = "'Poppins',sans-serif";
          Chart.defaults.color = "#64748b";

          var c1 = document.getElementById("cMonthly");
          if (c1)
            new Chart(c1, {
              type: "bar",
              data: {
                labels: [
                  "Jan",
                  "Feb",
                  "Mar",
                  "Apr",
                  "May",
                  "Jun",
                  "Jul",
                  "Aug",
                  "Sep",
                  "Oct",
                  "Nov",
                  "Dec",
                ],
                datasets: [
                  {
                    label: "With Tatkal Doctor",
                    data: [
                      185, 192, 205, 218, 224, 230, 219, 242, 250, 244, 260,
                      271,
                    ],
                    backgroundColor: "#00c796",
                    borderRadius: 5,
                    borderSkipped: false,
                  },
                  {
                    label: "Before",
                    data: [82, 79, 88, 91, 86, 93, 87, 95, 98, 92, 96, 100],
                    backgroundColor: "#e2e8f0",
                    borderRadius: 5,
                    borderSkipped: false,
                  },
                ],
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                  x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } },
                  },
                  y: {
                    grid: { color: "#f1f5f9" },
                    ticks: { font: { size: 10 } },
                  },
                },
              },
            });

          var c2 = document.getElementById("cCalls");
          if (c2)
            new Chart(c2, {
              type: "line",
              data: {
                labels: [
                  "6am",
                  "9am",
                  "12pm",
                  "3pm",
                  "6pm",
                  "9pm",
                  "12am",
                  "3am",
                ],
                datasets: [
                  {
                    label: "AI Voice Agent",
                    data: [100, 100, 100, 100, 100, 100, 100, 100],
                    borderColor: "#0066ff",
                    backgroundColor: "rgba(0,102,255,.07)",
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: "#0066ff",
                  },
                  {
                    label: "Manual Staff",
                    data: [55, 90, 88, 85, 80, 60, 20, 15],
                    borderColor: "#fbbf24",
                    backgroundColor: "rgba(251,191,36,.07)",
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: "#fbbf24",
                    borderDash: [5, 5],
                  },
                ],
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                  x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } },
                  },
                  y: {
                    grid: { color: "#f1f5f9" },
                    min: 0,
                    max: 110,
                    ticks: {
                      font: { size: 10 },
                      callback: function (v) {
                        return v + "%";
                      },
                    },
                  },
                },
              },
            });

          var c3 = document.getElementById("cNoshow");
          if (c3)
            new Chart(c3, {
              type: "doughnut",
              data: {
                labels: ["Attended", "No-show (was)", "Saved"],
                datasets: [
                  {
                    data: [70, 0, 30],
                    backgroundColor: ["#00c796", "#e2e8f0", "#0066ff"],
                    borderWidth: 0,
                    hoverOffset: 6,
                  },
                ],
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "68%",
                plugins: { legend: { display: false } },
              },
            });

          var c4 = document.getElementById("cChannels");
          if (c4)
            new Chart(c4, {
              type: "bar",
              data: {
                labels: [
                  "WhatsApp Bot",
                  "AI Voice Agent",
                  "Walk-in",
                  "Manual Phone",
                ],
                datasets: [
                  {
                    label: "Share",
                    data: [54, 28, 12, 6],
                    backgroundColor: [
                      "#00c796",
                      "#0066ff",
                      "#f59e0b",
                      "#94a3b8",
                    ],
                    borderRadius: 6,
                    borderSkipped: false,
                  },
                ],
              },
              options: {
                indexAxis: "y",
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                  legend: { display: false },
                  tooltip: {
                    callbacks: {
                      label: function (c) {
                        return c.parsed.x + "%";
                      },
                    },
                  },
                },
                scales: {
                  x: {
                    grid: { color: "#f1f5f9" },
                    max: 70,
                    ticks: {
                      font: { size: 10 },
                      callback: function (v) {
                        return v + "%";
                      },
                    },
                  },
                  y: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } },
                  },
                },
              },
            });
        }

        /* ── boot ── */
        function boot() {
          initPlayer();
          initKpi();
          initCharts();
          /* start WA demo when section visible */
          var sec = document.getElementById("whatsapp-automation");
          if (sec && window.IntersectionObserver) {
            var obs = new IntersectionObserver(
              function (e) {
                if (e[0].isIntersecting) {
                  obs.disconnect();
                  sched(runNext, 600);
                }
              },
              { threshold: 0.08 },
            );
            obs.observe(sec);
          } else {
            sched(runNext, 600);
          }
        }

        if (document.readyState === "loading")
          document.addEventListener("DOMContentLoaded", boot);
        else boot();
      })();
    </script>
  
  @endpush

@endsection