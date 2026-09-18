@extends('frontend.layout.app')

@section('content')

  {{-- ============================================================
       HERO SECTION
  ============================================================ --}}
  <section class="hero" aria-label="Hero">
    <div class="hero__bg" aria-hidden="true"></div>
    <div class="hero__inner">
      <h1 class="hero__title">AI Voice Receptionist for Your Clinic</h1>
      <h2 class="hero__subtitle">Tatkal Doctor acts as your AI powered receptionist that answers calls, schedules
        appointments, and follows up with patients automatically — helping your clinic run smoothly without extra
        staff.</h2>
      <p class="hero__typed-line">
        <span class="hero__typed" id="typed-text" aria-live="polite"></span>
        <span class="hero__cursor" aria-hidden="true">|</span>
      </p>

      <p class="hero__demo-label">Experience our demo!</p>

      <form class="hero__form" action="#" method="get" novalidate>
        <div class="hero__cta-pill">
          <label class="sr-only" for="country-code">Country code</label>
          <div class="hero__country-wrap">
            <select id="country-code" name="country_code" class="hero__flag" aria-label="Select country code">
              <option value="+91" selected>🇮🇳 +91</option>
              <option value="+1">🇺🇸 +1</option>
              <option value="+44">🇬🇧 +44</option>
              <option value="+61">🇦🇺 +61</option>
              <option value="+971">🇦🇪 +971</option>
              <option value="+65">🇸🇬 +65</option>
            </select>
          </div>
          <input type="tel" name="phone" class="hero__input" placeholder="Enter number"
            inputmode="numeric" autocomplete="tel">
          <button type="submit" class="hero__submit">
            <svg class="hero__phone-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
              xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path
                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Try Demo
          </button>
        </div>
      </form>
    </div>

    <a href="tel:" class="hero__fab" aria-label="Call us">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path
          d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </a>
  </section>

  {{-- ============================================================
       FEATURES / IMPACT CARDS
  ============================================================ --}}
  <section class="features" aria-labelledby="features-heading">
    <div class="features__inner">
      <h2 class="features__title" id="features-heading">When Every Call Gets Answered, Everything Changes</h2>
      <p class="features__lead">Tatkal Doctor helps clinics capture more patients, streamline operations, and deliver
        faster responses without increasing staff.</p>

      <ul class="features__grid">

        <li class="features-card features-card--blue">
          <div class="features-card__icon-wrap" aria-hidden="true">
            <svg class="features-card__icon" width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
            </svg>
          </div>
          <p class="features-card__stat">80%</p>
          <h3 class="features-card__headline">Fewer missed patient calls</h3>
          <p class="features-card__body">Every incoming call is answered instantly, so no potential patient is lost due to unavailability</p>
        </li>

        <li class="features-card features-card--green">
          <div class="features-card__icon-wrap" aria-hidden="true">
            <svg class="features-card__icon" width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M13 2L3 14h8l-1 8 10-12h-8l1-8z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <p class="features-card__stat">3x</p>
          <h3 class="features-card__headline">Faster appointment booking</h3>
          <p class="features-card__body">Patients are scheduled during the call itself, reducing delays and back-and-forth coordination</p>
        </li>

        <li class="features-card features-card--blue">
          <div class="features-card__icon-wrap" aria-hidden="true">
            <svg class="features-card__icon" width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.75" />
              <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <p class="features-card__stat">24/7</p>
          <h3 class="features-card__headline">Always available for patients</h3>
          <p class="features-card__body">Even after clinic hours, your AI receptionist continues handling calls and enquiries</p>
        </li>

        <li class="features-card features-card--green">
          <div class="features-card__icon-wrap" aria-hidden="true">
            <svg class="features-card__icon" width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
              <circle cx="9" cy="7" r="3" stroke="currentColor" stroke-width="1.75" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <p class="features-card__stat">60%</p>
          <h3 class="features-card__headline">Reduction in front desk workload</h3>
          <p class="features-card__body">Repetitive tasks like answering calls, booking, and follow-ups are fully automated</p>
        </li>

      </ul>
    </div>
  </section>

  {{-- ============================================================
       PAIN POINTS SECTION
  ============================================================ --}}
  <section class="pain-points" aria-labelledby="pain-points-heading">
    <div class="pain-points__inner">
      <h2 class="pain-points__title" id="pain-points-heading">Your Front Desk Is Losing Patients Every Day</h2>
      <p class="pain-points__lead">Missed calls, manual booking, and limited staff availability quietly cost clinics valuable patient opportunities.</p>

      @php
        $painCards = [
          [
            'icon_path' => 'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z',
            'extra_path' => 'M2 2l20 20',
            'headline' => 'Missed Calls Turn Into Lost Patients',
            'text' => 'When calls go unanswered, patients move on instead of waiting for a callback',
          ],
          [
            'icon_path' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2',
            'extra_path' => 'M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75',
            'circle' => true,
            'headline' => 'Reception Overload During Peak Hours',
            'text' => 'Staff struggle to manage multiple calls, walk-ins, and bookings at the same time',
          ],
          [
            'rect' => true,
            'headline' => 'Manual Booking Slows Everything Down',
            'text' => 'Appointments require back-and-forth coordination, creating delays and errors',
          ],
          [
            'chat_icon' => true,
            'headline' => 'Follow-ups Are Often Missed',
            'text' => 'Patients forget appointments and clinics lose repeat visits without reminders',
          ],
        ];
      @endphp

      <ul class="pain-points__grid">

        <li class="pain-card">
          <span class="pain-card__icon" aria-hidden="true">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M2 2l20 20" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
            </svg>
          </span>
          <div class="pain-card__content">
            <h3 class="pain-card__headline">Missed Calls Turn Into Lost Patients</h3>
            <p class="pain-card__text">When calls go unanswered, patients move on instead of waiting for a callback</p>
          </div>
        </li>

        <li class="pain-card">
          <span class="pain-card__icon" aria-hidden="true">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
              <circle cx="9" cy="7" r="3" stroke="currentColor" stroke-width="1.75" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          <div class="pain-card__content">
            <h3 class="pain-card__headline">Reception Overload During Peak Hours</h3>
            <p class="pain-card__text">Staff struggle to manage multiple calls, walk-ins, and bookings at the same time</p>
          </div>
        </li>

        <li class="pain-card">
          <span class="pain-card__icon" aria-hidden="true">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.75" />
              <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
              <circle cx="12" cy="15" r="1" fill="currentColor" />
              <path d="M12 12v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
          </span>
          <div class="pain-card__content">
            <h3 class="pain-card__headline">Manual Booking Slows Everything Down</h3>
            <p class="pain-card__text">Appointments require back-and-forth coordination, creating delays and errors</p>
          </div>
        </li>

        <li class="pain-card">
          <span class="pain-card__icon" aria-hidden="true">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M9 9h.01M15 9h.01M9 13h6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
            </svg>
          </span>
          <div class="pain-card__content">
            <h3 class="pain-card__headline">Follow-ups Are Often Missed</h3>
            <p class="pain-card__text">Patients forget appointments and clinics lose repeat visits without reminders</p>
          </div>
        </li>

      </ul>

      <p class="pain-points__footer">These problems lead to patient loss.</p>
    </div>
  </section>

  {{-- ============================================================
       VOICE CAPABILITIES SECTION
  ============================================================ --}}
  <section class="voice-capabilities" aria-labelledby="voice-capabilities-heading">
    <div class="voice-capabilities__inner">
      <h2 class="voice-capabilities__title" id="voice-capabilities-heading">What Can Tatkal Doctor Voice AI Handle?</h2>
      <p class="voice-capabilities__lead">Tatkal Doctor Voice AI is designed to manage the most common and critical patient call scenarios in hospitals and clinics.</p>

      <ul class="voice-capabilities__grid">

        {{-- Card 1: Doctor Appointment Booking --}}
        <li class="cap-card">
          <div class="cap-card__icon-wrap" aria-hidden="true">
            <svg class="cap-card__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.75" />
              <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
            </svg>
          </div>
          <h3 class="cap-card__title">Doctor Appointment Booking</h3>
          <p class="cap-card__intro">Patients can:</p>
          <ul class="cap-card__list">
            @foreach(['Book new OPD appointments', 'Schedule follow-up visits', 'Select departments or specific doctors', 'Confirm availability instantly'] as $item)
              <li>
                <span class="cap-card__check" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" />
                    <path d="M8 12l2.5 3L16 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </span>
                {{ $item }}
              </li>
            @endforeach
          </ul>
          <p class="cap-card__footer">No waiting. No manual call handling. No missed bookings.</p>
        </li>

        {{-- Card 2: Rescheduling --}}
        <li class="cap-card">
          <div class="cap-card__icon-wrap" aria-hidden="true">
            <svg class="cap-card__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.75" />
              <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <h3 class="cap-card__title">Appointment Rescheduling &amp; Cancellation Calls</h3>
          <p class="cap-card__intro">When doctors go on leave or schedules change, the AI:</p>
          <ul class="cap-card__list">
            @foreach(['Notifies patients proactively', 'Calls patients automatically', 'Reschedules based on availability', 'Confirms or cancels bookings smoothly'] as $item)
              <li>
                <span class="cap-card__check" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" />
                    <path d="M8 12l2.5 3L16 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </span>
                {{ $item }}
              </li>
            @endforeach
          </ul>
          <p class="cap-card__footer">Reduces chaos at the front desk and improves patient trust.</p>
        </li>

        {{-- Card 3: FAQs --}}
        <li class="cap-card">
          <div class="cap-card__icon-wrap" aria-hidden="true">
            <svg class="cap-card__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M4 22v-7" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
            </svg>
          </div>
          <h3 class="cap-card__title">Hospital Enquiries &amp; Front Desk FAQs</h3>
          <p class="cap-card__intro">Tatkal Doctor Voice AI handles routine questions like:</p>
          <ul class="cap-card__list">
            @foreach(['OPD timings', 'Doctor availability', 'Department locations', 'Admission procedures', 'Consultation fees'] as $item)
              <li>
                <span class="cap-card__check" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" />
                    <path d="M8 12l2.5 3L16 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </span>
                {{ $item }}
              </li>
            @endforeach
          </ul>
          <p class="cap-card__footer">Your staff focuses on care — not repetitive calls.</p>
        </li>

      </ul>
    </div>
  </section>

  {{-- ============================================================
       HOW IT WORKS SECTION
  ============================================================ --}}
  <section class="how-it-works" aria-labelledby="how-it-works-heading">
    <div class="how-it-works__inner">
      <p class="how-it-works__badge">Simple by design</p>
      <h2 class="how-it-works__title" id="how-it-works-heading">How it works</h2>
      <p class="how-it-works__lead">Four steps. Fully automated. Zero friction for your patients or your staff.</p>

      <div class="how-it-works__track">
        <div class="how-it-works__line how-it-works__line--horizontal" aria-hidden="true"></div>
        <ol class="how-it-works__steps">

          <li class="how-step">
            <div class="how-step__line how-step__line--vertical" aria-hidden="true"></div>
            <div class="how-step__head">
              <div class="how-step__icon-box how-step__icon-box--blue">
                <span class="how-step__num" aria-hidden="true">01</span>
                <svg class="how-step__glyph" width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M16 3h6v6M22 3l-7 7" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </div>
            <div class="how-step__card how-step__card--blue">
              <h3 class="how-step__card-title">Patient calls or messages</h3>
              <p class="how-step__card-text">Via phone call or WhatsApp. Any time of day or night. Tatkal Doctor picks up immediately — no hold time.</p>
            </div>
          </li>

          <li class="how-step">
            <div class="how-step__line how-step__line--vertical" aria-hidden="true"></div>
            <div class="how-step__head">
              <div class="how-step__icon-box how-step__icon-box--purple">
                <span class="how-step__num" aria-hidden="true">02</span>
                <svg class="how-step__glyph" width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.65" />
                  <path d="M9 9h.01M15 9h.01M9 15h6" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" />
                  <path d="M4 8h2M18 8h2M4 16h2M18 16h2" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" />
                </svg>
              </div>
            </div>
            <div class="how-step__card how-step__card--purple">
              <h3 class="how-step__card-title">AI understands the request</h3>
              <p class="how-step__card-text">Natural language AI processes the patient's need in Hindi or English and retrieves available slots in real-time.</p>
            </div>
          </li>

          <li class="how-step">
            <div class="how-step__line how-step__line--vertical" aria-hidden="true"></div>
            <div class="how-step__head">
              <div class="how-step__icon-box how-step__icon-box--teal">
                <span class="how-step__num" aria-hidden="true">03</span>
                <svg class="how-step__glyph" width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.65" />
                  <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" />
                  <path d="M9 16l2 2 4-4" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </div>
            <div class="how-step__card how-step__card--teal">
              <h3 class="how-step__card-title">Appointment is booked</h3>
              <p class="how-step__card-text">The AI books the appointment in your calendar system automatically. No manual entry. No errors. Instant.</p>
            </div>
          </li>

          <li class="how-step">
            <div class="how-step__line how-step__line--vertical" aria-hidden="true"></div>
            <div class="how-step__head">
              <div class="how-step__icon-box how-step__icon-box--green">
                <span class="how-step__num" aria-hidden="true">04</span>
                <svg class="how-step__glyph" width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
            </div>
            <div class="how-step__card how-step__card--green">
              <h3 class="how-step__card-title">Confirmation &amp; reminders sent</h3>
              <p class="how-step__card-text">Patient receives a WhatsApp confirmation immediately. Automated reminders follow at 24 hours and 1 hour before.</p>
            </div>
          </li>

        </ol>
      </div>
    </div>
  </section>

  {{-- ============================================================
       FAQ SECTION — voice_agent.js custom accordion
  ============================================================ --}}
  <section class="faq" aria-labelledby="faq-heading">
    <div class="faq__inner">
      <h2 class="faq__title" id="faq-heading">Frequently Asked Questions</h2>

      @php
        $faqs = [
          ['id'=>1, 'open'=>true,  'q'=>'What is Tatkal Doctor Voice AI?',
           'a'=>'Tatkal Doctor Voice AI is an AI-powered virtual medical receptionist that answers patient calls automatically and intelligently. It understands patient requests, responds naturally like a trained hospital receptionist, and resolves common requests end-to-end.'],
          ['id'=>2, 'open'=>false, 'q'=>'Can Tatkal Doctor Voice AI handle Hindi and regional languages?',
           'a'=>'Yes. It is built for multilingual conversations—including Hindi and English out of the box—and can be configured for additional regional languages based on your clinic\'s needs.'],
          ['id'=>3, 'open'=>false, 'q'=>'Does Tatkal Doctor Voice AI replace hospital staff?',
           'a'=>'No. It supports your front desk by handling repetitive calls, bookings, and FAQs so staff can focus on in-person patients and clinical coordination.'],
          ['id'=>4, 'open'=>false, 'q'=>'Is Tatkal Doctor Voice AI secure and compliant?',
           'a'=>'Security and privacy are built into the platform. We work with you to align with your hospital\'s policies and applicable healthcare data requirements for your region.'],
          ['id'=>5, 'open'=>false, 'q'=>'How quickly can we deploy Tatkal Doctor Voice AI?',
           'a'=>'Typical deployments complete in a few weeks once your phone lines, calendar or HMS integration, and workflows are mapped—timing depends on your systems and approvals.'],
        ];
      @endphp

      <div class="faq__list">
        @foreach($faqs as $faq)
          <div class="faq-item">
            <button type="button" class="faq-item__trigger"
              id="faq-trigger-{{ $faq['id'] }}"
              aria-expanded="{{ $faq['open'] ? 'true' : 'false' }}"
              aria-controls="faq-panel-{{ $faq['id'] }}">
              <span class="faq-item__question">{{ $faq['q'] }}</span>
              <span class="faq-item__chevron" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </span>
            </button>
            <div class="faq-item__panel" id="faq-panel-{{ $faq['id'] }}"
              role="region" aria-labelledby="faq-trigger-{{ $faq['id'] }}">
              <div class="faq-item__panel-inner">
                <p class="faq-item__answer">{{ $faq['a'] }}</p>
              </div>
            </div>
          </div>
        @endforeach
      </div>

    </div>
  </section>

  {{-- ============================================================
       PAGE-SPECIFIC ASSETS
       
  ============================================================ --}}
  @push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/voice_agent.css') }}" />
  @endpush

  @push('scripts')
    <script src="{{ asset('frontend/js/voice_agent.js') }}"></script>
  @endpush

@endsection