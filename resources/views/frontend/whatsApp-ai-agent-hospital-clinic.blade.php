@extends('frontend.layout.app')

@section('title', 'WhatsApp AI Agent for Hospitals and Clinics | Tatkal Doctor')
@section('description', 'Automate patient appointment bookings with India, the best WhatsApp AI Agent for Hospitals and Clinics. send reminders, and provide 24/7 support on WhatsApp. Try for free!')
@section('canonical', 'https://www.tatkaldoctor.com/whatsApp-ai-agent-hospital-clinic')
@section('robots', 'index, follow')
@section('og:title', 'WhatsApp AI Agent for Hospitals and Clinics | Tatkal Doctor')
@section('og:url', 'https://www.tatkaldoctor.com/whatsApp-ai-agent-hospital-clinic')
@section('og:description', 'Automate patient appointment bookings with India, the best WhatsApp AI Agent for Hospitals and Clinics. send reminders, and provide 24/7 support on WhatsApp. Try for free!')
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

{{-- ══════════════════════════════════════════
     1. HERO
═══════════════════════════════════════════ --}}
<section id="wb-hero" class="wb-section">
  <div class="wb-container">
    <div class="hero-grid">

      {{-- Left --}}
      <div>
        <h1 class="hero-h1">
          Let patients book appointments<br/>
          <em>via WhatsApp in 2 minutes</em>
        </h1>
        <p class="hero-sub">
          Tatkal Doctor connects your clinic to WhatsApp using the official Meta Cloud API — eliminating phone-tag,
          missed calls, and manual slot management. Patients scan, select, and confirm. You focus on care.
        </p>
        <div class="hero-btns">
          <a href="#wb-cta" class="wb-btn wb-btn-primary" onclick="event.preventDefault()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
            </svg>
            Start Free — Get Your QR
          </a>
          <a href="{{ route('schedule.demo') }}" class="wb-btn wb-btn-outline">Book a Demo</a>
        </div>
        <div class="hero-trust">
          <div class="hero-trust-dots">
            <div class="hero-trust-dot">DR</div>
            <div class="hero-trust-dot">CL</div>
            <div class="hero-trust-dot">+</div>
          </div>
          Trusted by clinics across India to handle 100+ bookings daily
        </div>
      </div>

      {{-- Right: WhatsApp Chat UI --}}
      <div class="chat-phone">
        <div class="phone-shell">
          <div class="phone-header">
            <div class="phone-back">&#x2190;</div>
            <div class="phone-avatar">TD</div>
            <div class="phone-info">
              <div class="phone-name">Tatkal Doctor</div>
              <div class="phone-status">Online</div>
            </div>
            <div class="phone-icons">
              <div class="phone-icon">
  <!-- Call icon -->
  <svg viewBox="0 0 24 24" width="20" height="20">
    <path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2
    a1 1 0 0 1 1-.27c1.12.37 2.33.57 3.59.57
    a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1
    C10.07 21 3 13.93 3 5
    a1 1 0 0 1 1-1h3.5
    a1 1 0 0 1 1 1c0 1.26.2 2.47.57 3.59
    a1 1 0 0 1-.27 1l-2.18 2.2z"
    fill="currentColor"/>
  </svg>
</div>

<div class="phone-icon">
  <!-- 3 dots menu -->
  <svg viewBox="0 0 24 24" width="20" height="20">
    <circle cx="12" cy="5" r="2" fill="currentColor"/>
    <circle cx="12" cy="12" r="2" fill="currentColor"/>
    <circle cx="12" cy="19" r="2" fill="currentColor"/>
  </svg>
</div>
            </div>
          </div>

          <div class="chat-body">
            <div class="chat-date">Today</div>

            <div class="bubble bubble-in">
              <div class="bubble-name">Tatkal Doctor</div>
              Hello! Welcome to Dr. Sharma's Clinic. How can I help you today?
              <div class="chat-options" style="margin-top:8px;">
                <div class="chat-option-btn">Book an Appointment</div>
                <div class="chat-option-btn">View My Bookings</div>
                <div class="chat-option-btn">Talk to Staff</div>
              </div>
              <span class="bubble-time">10:02</span>
            </div>

            <div class="bubble bubble-out">
              Book an Appointment
              <span class="bubble-time">10:02 &#10003;&#10003;</span>
            </div>

            <div class="bubble bubble-in">
              <div class="bubble-name">Tatkal Doctor</div>
              Please select an available slot for <strong>Dr. Sharma</strong>:
              <div class="chat-options" style="margin-top:8px;">
                <div class="chat-option-btn">Today 11:00 AM — 2 slots left</div>
                <div class="chat-option-btn">Today 3:00 PM — Available</div>
                <div class="chat-option-btn">Tomorrow 10:00 AM — Available</div>
              </div>
              <span class="bubble-time">10:02</span>
            </div>

            <div class="bubble bubble-out">
              Today 11:00 AM
              <span class="bubble-time">10:03 &#10003;&#10003;</span>
            </div>

            <div class="confirm-bubble">
              <div class="confirm-title">✅ Appointment Confirmed</div>
              <div class="confirm-row"><span class="confirm-label">Doctor:</span> Dr. Sharma</div>
              <div class="confirm-row"><span class="confirm-label">Date:</span> Today</div>
              <div class="confirm-row"><span class="confirm-label">Time:</span> 11:00 AM</div>
              <div class="confirm-row"><span class="confirm-label">Token:</span> #07</div>
              <div style="margin-top:8px;font-size:0.72rem;color:#64748b;">A reminder will be sent 30 minutes before your appointment.</div>
            </div>
          </div>

          <div class="phone-input-bar">
            <div class="phone-input">Type a message</div>
            <div class="phone-send">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="#fff">
                <path d="M2 21l21-9L2 3v7l15 2-15 2v7z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     2. WHAT IS WHATSAPP APPOINTMENT BOOKING
═══════════════════════════════════════════ --}}
<section id="wb-what" class="wb-section">
  <div class="wb-container">
    <div class="what-grid">
      <div class="reveal" style="padding-top:40px; max-width:540px;">
        <h2>What is WhatsApp appointment booking for clinics?</h2>
        <p style="margin-top:18px;">
          WhatsApp appointment booking is a structured automation system that replaces the traditional "call the
          clinic, wait for staff, check the diary, call back" cycle with a seamless, self-serve flow entirely inside
          WhatsApp. When a patient sends a message or scans a QR code, an automated conversation begins — one that
          shows available slots, accepts the patient's selection, and issues a confirmed appointment with a unique
          token, all without any human intervention on the clinic side.
        </p>
        <p style="margin-top:14px;">
          For Indian clinics, where front-desk staff manage hundreds of calls daily and missed bookings directly impact
          revenue, this system acts as a 24/7 digital receptionist. Patients are already comfortable using WhatsApp,
          which means adoption is instant — no learning curve, no app download, no new platform to understand. The
          entire flow from scan to confirmation takes under two minutes.
        </p>
      </div>

      <div class="reveal reveal-delay-2">
        <div class="stat-cards">
          @php
            $stats = [
              ['num' => '2 min', 'label' => 'Average time from first message to confirmed booking'],
              ['num' => '0',     'label' => 'Apps to download. Patients use WhatsApp they already have'],
              ['num' => '24/7',  'label' => 'Appointment booking runs even when the clinic is closed'],
              ['num' => '100%',  'label' => 'Official Meta Cloud API — no unofficial tools or BSP'],
            ];
          @endphp
          @foreach($stats as $s)
            <div class="stat-card">
              <div class="stat-num">{{ $s['num'] }}</div>
              <div class="stat-label">{{ $s['label'] }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     3. HOW IT WORKS
═══════════════════════════════════════════ --}}
<section id="wb-how" class="wb-section">
  <div class="wb-container">
    <div class="reveal how-head">
      <h2>QR-based booking.</h2>
      <p style="margin-top:14px;">
        Clinics display a single QR code — at the reception desk, on prescriptions, on the clinic door. Patients scan
        it and the entire booking process is handled automatically inside WhatsApp. No phone calls. No portal. No paper
        forms.
      </p>
    </div>

    <div class="steps-grid">
      <div class="steps-connector"></div>

      @php
        $steps = [
          ['num'=>1, 'delay'=>'reveal-delay-1', 'title'=>'Scan the QR or Send "Hi"',
           'text'=>'The patient scans the clinic\'s unique QR code or messages the WhatsApp number directly. This instantly opens a chat with the Tatkal Doctor system and begins the booking conversation.',
           'benefit'=>'No app. No login. Just WhatsApp.'],
          ['num'=>2, 'delay'=>'reveal-delay-2', 'title'=>'System Shows Available Slots',
           'text'=>'The automation instantly displays the doctor\'s real-time available time slots. Fully booked slots are never shown. The patient sees exactly what is open — removing all back-and-forth negotiation.',
           'benefit'=>'Live availability. Zero double-bookings.'],
          ['num'=>3, 'delay'=>'reveal-delay-3', 'title'=>'Patient Selects a Slot',
           'text'=>'The patient taps their preferred time with a single tap on the interactive button. The slot is locked in their name immediately. No waiting for confirmation, no call-back required from clinic staff.',
           'benefit'=>'One tap to book. Slot reserved instantly.'],
          ['num'=>4, 'delay'=>'reveal-delay-4', 'title'=>'Confirmation & Automated Reminder',
           'text'=>'The patient receives a WhatsApp confirmation with their token number, doctor name, date, and time. A reminder message is sent automatically 30–60 minutes before the appointment to reduce no-shows.',
           'benefit'=>'Automated reminders. Fewer no-shows.'],
        ];
      @endphp

      @foreach($steps as $step)
        <div class="step-card reveal {{ $step['delay'] }}">
          <div class="step-num">{{ $step['num'] }}</div>
          <h3>{{ $step['title'] }}</h3>
          <p style="font-size:0.87rem; margin-top:8px;">{{ $step['text'] }}</p>
          <div class="step-benefit">{{ $step['benefit'] }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     4. FEATURES
═══════════════════════════════════════════ --}}
<section id="wb-features" class="wb-section">
  <div class="wb-container">
    <div class="reveal" style="max-width:600px;">
      <h2>Everything your clinic needs to run on WhatsApp</h2>
      <p style="margin-top:14px;">
        Tatkal Doctor is not just an appointment tool. It is a complete clinic communication layer — handling bookings,
        scheduling changes, patient updates, and reminder automation from a single connected WhatsApp number.
      </p>
    </div>

    @php
      $features = [
        ['delay'=>'reveal-delay-1', 'tag'=>'Core Feature', 'title'=>'Appointment Booking',
         'icon'=>'<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
         'text'=>'Patients book their appointments through a structured WhatsApp conversation in under two minutes. The system manages slot availability in real time, preventing double-bookings and ensuring the clinic\'s daily schedule remains accurate without any manual entry.'],
        ['delay'=>'reveal-delay-2', 'tag'=>'Self-Service', 'title'=>'Reschedule and Cancellation',
         'icon'=>'<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5l3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
         'text'=>'Patients can reschedule or cancel their appointment directly through WhatsApp without calling the clinic. The freed slot is automatically made available to other patients, keeping the appointment calendar optimized without staff intervention.'],
        ['delay'=>'reveal-delay-3', 'tag'=>'2-Way Messaging', 'title'=>'Patient Communication',
         'icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
         'text'=>'Clinic staff can send direct messages to patients — sharing test instructions, pre-visit guidelines, or simple check-ins — all through the same WhatsApp interface. No separate app or portal is needed for the patient to receive and respond.'],
        ['delay'=>'reveal-delay-1', 'tag'=>'Automated', 'title'=>'Reminder Automation',
         'icon'=>'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
         'text'=>'The system automatically dispatches appointment reminders to patients before their scheduled visit. Reminders are configurable by timing and include the doctor\'s name, appointment time, and clinic location — significantly reducing the no-show rate that costs clinics revenue daily.'],
        ['delay'=>'reveal-delay-2', 'tag'=>'Unlimited Concurrent', 'title'=>'Multi-Patient Handling',
         'icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
         'text'=>'Tatkal Doctor manages hundreds of simultaneous patient conversations without any degradation in speed or accuracy. Each patient is in their own isolated WhatsApp thread, and the system handles all of them concurrently — something no human receptionist team can match.'],
      ];
    @endphp

    <div class="features-grid">
      @foreach($features as $f)
        <div class="feature-card reveal {{ $f['delay'] }}">
          <div class="feature-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0CBD93" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">{!! $f['icon'] !!}</svg>
          </div>
          <h3>{{ $f['title'] }}</h3>
          <p>{{ $f['text'] }}</p>
          <span class="feature-tag">{{ $f['tag'] }}</span>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     5. WHAT DOCTORS CAN DO
═══════════════════════════════════════════ --}}
<section id="wb-doctors" class="wb-section">
  <div class="wb-container">
    <div class="doctor-grid">
      <div class="reveal">
        <h2>What doctors can send and do via WhatsApp</h2>
        <p style="margin-top:18px;">
          Tatkal Doctor's WhatsApp layer is not limited to booking management. Doctors and clinic staff gain a direct,
          reliable communication channel with every patient — one that works on the same platform patients already
          trust, without requiring any additional software on either end.
        </p>
        <p style="margin-top:14px;">
          This matters because post-consultation follow-through is where most clinics lose engagement. When a doctor
          can share a prescription or lab report directly on WhatsApp seconds after the consultation, the patient
          receives it, reads it, and acts on it — instead of losing a paper copy or waiting for an email they might
          not open.
        </p>
      </div>

      @php
        $doctorItems = [
          ['icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>',
           'title'=>'Send Lab Reports and Test Results',
           'text'=>'Doctors or staff upload lab reports as PDF or image files directly to the patient\'s WhatsApp conversation. The patient receives the report instantly without visiting the clinic or waiting for courier delivery.'],
          ['icon'=>'<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
           'title'=>'Share Digital Prescriptions',
           'text'=>'Digital prescriptions can be sent post-consultation through WhatsApp, giving patients a permanent, legible record they can share with any pharmacy — eliminating the "lost prescription" problem that leads to missed medication adherence.'],
          ['icon'=>'<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
           'title'=>'Send Follow-Up Instructions',
           'text'=>'Post-visit care instructions, medication schedules, or diet advice can be sent as formatted WhatsApp messages immediately after the appointment — when the patient\'s attention is highest and compliance is most likely.'],
          ['icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
           'title'=>'Initiate Patient Follow-Ups',
           'text'=>'Clinics can proactively reach out to patients for scheduled check-ups, seasonal health campaigns, or post-treatment reviews — all through templated WhatsApp messages that are compliant with Meta\'s official messaging policies.'],
        ];
      @endphp

      <div class="doctor-items reveal reveal-delay-2">
        @foreach($doctorItems as $item)
          <div class="doctor-item">
            <div class="doctor-item-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0CBD93" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">{!! $item['icon'] !!}</svg>
            </div>
            <div>
              <h3>{{ $item['title'] }}</h3>
              <p style="font-size:0.85rem;">{{ $item['text'] }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     6. FREE MODEL
═══════════════════════════════════════════ --}}
<section id="wb-free" class="wb-section">
  <div class="wb-container">
    <div class="reveal how-head">
      <h2>The Free Shared WhatsApp System for Individual Doctors</h2>
      <p style="margin-top:14px;">
        Tatkal Doctor's free plan gives individual doctors access to a shared WhatsApp booking system with no upfront
        cost. Every registered doctor receives their own unique QR code — the only setup required is printing it and
        putting it where patients can see it.
      </p>
    </div>

    <div class="free-card reveal">
      <div>
        <h3 style="font-size:1.3rem; margin-bottom:8px;">How the free plan works</h3>
        <p style="font-size:0.9rem;">
          Doctors share a managed WhatsApp infrastructure, with each clinic's booking flow completely isolated.
          Patients interact only with the specific doctor's booking flow — they never see or interact with any other
          clinic's data. Setup is completed on the same day.
        </p>
        <div class="free-steps">
          @php
            $freeSteps = [
              ['title'=>'Register on Tatkal Doctor',
               'text'=>'Fill in your clinic details, set your consultation hours, and define your daily appointment slots. No technical knowledge is required.'],
              ['title'=>'Get Added to the Shared System',
               'text'=>'Your clinic is onboarded to the shared WhatsApp infrastructure. All booking flows are individually isolated — patients only reach your booking journey.'],
              ['title'=>'Receive Your Unique QR Code',
               'text'=>'A personalized QR code is generated for your clinic. Display it at the reception, on a standee, or share it digitally on your social media profiles.'],
              ['title'=>'Patients Scan and Book Instantly',
               'text'=>'From day one, patients scan the QR code and get into the automated booking flow. No additional configuration is needed on the clinic\'s end.'],
            ];
          @endphp
          @foreach($freeSteps as $i => $fs)
            <div class="free-step">
              <div class="free-step-num">{{ $i + 1 }}</div>
              <div>
                <h3>{{ $fs['title'] }}</h3>
                <p>{{ $fs['text'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="qr-mock">
        <div class="qr-box">
          <!--<svg class="qr-inner" viewBox="0 0 140 140" xmlns="http://www.w3.org/2000/svg">-->
          <!--  <rect width="140" height="140" fill="#fff"/>-->
          <!--  <rect x="8" y="8" width="38" height="38" rx="4" fill="none" stroke="#0CBD93" stroke-width="4"/>-->
          <!--  <rect x="16" y="16" width="22" height="22" rx="2" fill="#0CBD93"/>-->
          <!--  <rect x="94" y="8" width="38" height="38" rx="4" fill="none" stroke="#0CBD93" stroke-width="4"/>-->
          <!--  <rect x="102" y="16" width="22" height="22" rx="2" fill="#0CBD93"/>-->
          <!--  <rect x="8" y="94" width="38" height="38" rx="4" fill="none" stroke="#0CBD93" stroke-width="4"/>-->
          <!--  <rect x="16" y="102" width="22" height="22" rx="2" fill="#0CBD93"/>-->
          <!--  <rect x="56" y="8" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="66" y="8" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="56" y="18" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="76" y="14" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="56" y="56" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="68" y="56" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="80" y="56" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="56" y="68" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="68" y="68" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="80" y="68" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="56" y="80" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="80" y="80" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="56" y="92" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="68" y="92" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="80" y="92" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="94" y="56" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="106" y="56" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="118" y="56" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="94" y="68" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="118" y="68" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="94" y="80" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="106" y="80" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="94" y="92" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="106" y="92" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="118" y="92" width="6" height="6" fill="#0f172a"/>-->
          <!--  <rect x="60" y="60" width="20" height="20" rx="4" fill="#E6FBF5"/>-->
          <!--  <text x="70" y="74" text-anchor="middle" font-size="10" font-weight="900" fill="#0CBD93" font-family="sans-serif">TD</text>-->
          <!--</svg>-->
  <img src="{{ asset('frontend/images/doctor-qrcode.jpg') }}" alt="Clinic QR Code" class="qr-inner">
        </div>
        <div class="qr-label">
          <strong>Dr. Hari Goyal's Clinic</strong>
          Scan to book your appointment on WhatsApp
        </div>
        <a href="{{ route('schedule.demo') }}" class="wb-btn wb-btn-primary" style="margin-top:8px;">Get My Free QR Code</a>
      </div>
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     7. WHITE LABEL
═══════════════════════════════════════════ --}}
<section id="wb-white-label" class="wb-section">
  <div class="wb-container">
    <div class="white-label-grid">
      <div class="reveal">
        <div class="section-label">White-Label Solution</div>
        <h2>Your clinic's own WhatsApp number. Fully branded.</h2>
        <p style="margin-top:18px;">
          For clinics and hospital groups that want their own identity on WhatsApp, the white-label plan provides a
          dedicated, verified WhatsApp Business number — registered under your clinic's name and linked exclusively to
          your patients. Patients see your clinic's name in their WhatsApp contact, not a generic service number.
        </p>
        <p style="margin-top:14px;">
          Advanced automation flows, custom message templates, appointment categories, and multi-doctor management are
          all configured under your brand. This is the infrastructure that established clinics and multi-specialty
          centers use to run their entire patient communication on WhatsApp.
        </p>

        @php
          $wlCards = [
            ['icon'=>'<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>',
             'title'=>'Dedicated WhatsApp Business Number',
             'text'=>'A verified number registered to your clinic — so patients recognize and trust the sender in their WhatsApp contact list.'],
            ['icon'=>'<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>',
             'title'=>'Advanced Automation and Custom Flows',
             'text'=>'Multi-step conversation flows, department-based routing, and appointment category management — all configured to your clinic\'s exact workflow.'],
            ['icon'=>'<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
             'title'=>'Full Branding — Your Clinic\'s Identity',
             'text'=>'Custom display names, branded confirmation messages, and clinic-specific QR codes that reflect your practice\'s professional image.'],
          ];
        @endphp

        <div class="wl-cards" style="margin-top:28px;">
          @foreach($wlCards as $card)
            <div class="wl-card">
              <div class="wl-card-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0CBD93" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">{!! $card['icon'] !!}</svg>
              </div>
              <div>
                <h3>{{ $card['title'] }}</h3>
                <p style="font-size:0.82rem;">{{ $card['text'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="reveal reveal-delay-2">
        <div class="wl-highlight-box">
          <div class="section-label" style="margin-bottom:16px;">Add-On Capability</div>
          <h3>AI Voice Calling — When WhatsApp Is Not Enough</h3>
          <p>
            For patients who are less comfortable with text-based chat, Tatkal Doctor's AI voice calling feature
            provides an automated phone call experience that handles appointment booking through voice commands. The
            same slot management system is shared — meaning bookings made over voice calls appear in the same clinic
            dashboard as WhatsApp bookings.
          </p>
          <p style="margin-top:12px;">
            This is particularly relevant for clinics serving elderly patients or populations in areas with lower
            smartphone literacy, where WhatsApp automation alone may not achieve full coverage.
          </p>
          <a href="{{ route('features') }}" class="wl-voice-link" style="margin-top:20px; display:inline-flex;">
            Explore AI voice automation
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14M12 5l7 7-7 7" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     8. COMPARISON TABLE
═══════════════════════════════════════════ --}}
<section id="wb-compare" class="wb-section">
  <div class="wb-container">
    <div class="reveal" style="text-align:center; max-width:580px; margin:0 auto;">
      <div class="section-label">Why Tatkal Doctor</div>
      <h2>Tatkal Doctor vs BSP solutions vs manual booking</h2>
      <p style="margin-top:14px;">
        Not all WhatsApp solutions are built the same. The difference between direct Meta Cloud API access and
        BSP-mediated access has real cost and reliability consequences for your clinic.
      </p>
    </div>

    <div class="table-wrap reveal">
      <table class="wb-table">
        <thead>
          <tr>
            <th>Factor</th>
            <th class="col-highlight">Tatkal Doctor</th>
            <th>Via BSP</th>
            <th>Manual / Phone</th>
          </tr>
        </thead>
        <tbody>
          @php
            $tableRows = [
              ['factor'=>'Cost per booking',
               'td'=>'Direct Meta pricing — no BSP markup',
               'bsp'=>'Higher due to BSP margin layered on top',
               'bsp_class'=>'td-mid',
               'manual'=>'Staff salary cost per call; no automation savings',
               'manual_class'=>'td-bad'],
              ['factor'=>'Setup time',
               'td'=>'Same day (free) / 2–5 days (white-label)',
               'bsp'=>'1–4 weeks including BSP onboarding',
               'bsp_class'=>'td-mid',
               'manual'=>'Immediate but zero automation benefit',
               'manual_class'=>'td-good'],
              ['factor'=>'Booking automation',
               'td'=>'Full — slot management, confirmation, reminders',
               'bsp'=>'Partial — depends on BSP\'s platform capabilities',
               'bsp_class'=>'td-mid',
               'manual'=>'None — entirely manual per booking',
               'manual_class'=>'td-bad'],
              ['factor'=>'Platform dependency',
               'td'=>'Only Meta — no third-party BSP dependency',
               'bsp'=>'Dependent on BSP availability and uptime',
               'bsp_class'=>'td-mid',
               'manual'=>'Dependent on staff availability and office hours',
               'manual_class'=>'td-bad'],
            ];
          @endphp
          @foreach($tableRows as $row)
            <tr>
              <td>{{ $row['factor'] }}</td>
              <td class="td-highlight">{{ $row['td'] }}</td>
              <td class="{{ $row['bsp_class'] }}">{{ $row['bsp'] }}</td>
              <td class="{{ $row['manual_class'] }}">{{ $row['manual'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     9. FAQ
═══════════════════════════════════════════ --}}
<section id="wb-faq" class="wb-section">
  <div class="wb-container">
    <div class="reveal" style="text-align:center; max-width:580px; margin:0 auto;">
      <div class="section-label">Common Questions</div>
      <h2>Frequently asked questions</h2>
      <p style="margin-top:14px;">
        Clear answers to the questions Indian clinic owners and doctors ask most often about WhatsApp appointment
        booking.
      </p>
    </div>

    @php
      $faqs = [
        ['q'=>'What is WhatsApp appointment booking for clinics?',
         'a'=>'WhatsApp appointment booking is a system that enables clinic patients to book, reschedule, and confirm appointments directly through WhatsApp — by scanning a QR code or sending a message to the clinic\'s number. The automated flow guides the patient through available slot selection and issues a booking confirmation, all without any manual involvement from clinic staff. Tatkal Doctor\'s implementation is built on the official Meta Cloud API, making it the most reliable and cost-effective version of this system available for Indian clinics.'],
        ['q'=>'How does it reduce missed calls in clinics?',
         'a'=>'Clinics lose patients every day because their phone lines are engaged, staff are unavailable during peak hours, or the call is not answered promptly. WhatsApp appointment booking removes the phone call entirely — patients book asynchronously, at any time, without needing anyone to pick up. Because the system operates 24/7, a patient who wants to book at 10 PM for the next morning can do so without waiting for the clinic to open. Automated reminders further reduce no-shows that typically follow after a missed or forgotten verbal booking.'],
        ['q'=>'Do patients need to download an app to use this?',
         'a'=>'No. Patients use WhatsApp, which is already installed on virtually every smartphone in India. There is nothing new to download, no account to create on a separate platform, and no clinic portal to log into. The entire booking experience — from scanning the QR code to receiving the appointment confirmation — happens inside WhatsApp.'],
        ['q'=>'Is Tatkal Doctor using the official WhatsApp API?',
         'a'=>'Yes. Tatkal Doctor is built directly on the Meta Cloud API — the official, Meta-hosted version of the WhatsApp Business API. This is distinct from solutions that route through a BSP (Business Solution Provider), which adds cost and dependency on a third-party intermediary. By connecting directly to Meta, Tatkal Doctor passes on lower costs to clinics, operates with higher reliability, and is fully compliant with Meta\'s WhatsApp Business policies.'],
        ['q'=>'Can small clinics or solo doctors use this system?',
         'a'=>'Absolutely. The free shared system is specifically designed for solo practitioners and small clinics who want the full benefit of WhatsApp appointment booking without any infrastructure cost. After completing a simple registration, the doctor receives a unique QR code and is immediately live.'],
        ['q'=>'How fast is the initial setup?',
         'a'=>'For the free shared plan, setup is typically completed on the same day the doctor registers. The Tatkal Doctor team handles all backend configuration and delivers the unique QR code within hours of registration. For the white-label plan, the setup timeline is 2–5 business days.'],
        ['q'=>'Can the system handle multiple patients booking simultaneously?',
         'a'=>'Yes. The Tatkal Doctor system is built to manage concurrent patient conversations at scale. Each patient\'s booking interaction is isolated in their own WhatsApp thread, and slot availability is managed centrally — so if two patients attempt to book the same slot simultaneously, only one is confirmed and the other is immediately shown the next available option.'],
      ];
    @endphp

    <div class="faq-grid">
      @foreach($faqs as $faq)
        <div class="wb-faq-item reveal">
          <details>
            <summary class="faq-q">
              {{ $faq['q'] }}
              <div class="faq-chevron">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#0CBD93" stroke-width="3"
                  stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6 9 12 15 18 9" />
                </svg>
              </div>
            </summary>
            <div class="faq-a">{{ $faq['a'] }}</div>
          </details>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     10. CTA SECTION
═══════════════════════════════════════════ --}}
<section id="wb-cta">
  <div class="wb-container">
    <div class="cta-inner reveal">
      <div class="section-label" style="background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.7);">Get Started Today</div>
      <h2 style="margin-top:16px;">Your clinic is losing bookings to busy phone lines. Fix it today.</h2>
      <p class="wb-lead" style="margin-top:16px; color:rgba(255,255,255,0.55);">
        Start with the free shared WhatsApp system — get your QR code today and let patients book appointments while
        you see patients. No technical setup. No BSP contracts. No missed calls.
      </p>
      <div class="cta-btns">
        <a href="{{ route('schedule.demo') }}" class="wb-btn wb-btn-primary" style="font-size:0.95rem; padding:15px 30px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
          Book a Free Demo
        </a>
        <a href="#" class="wb-btn wb-btn-white" style="font-size:0.95rem; padding:15px 30px;">
          Get My QR Code — Free
        </a>
        <a href="{{ route('contact') }}" class="wb-btn wb-btn-ghost" style="font-size:0.95rem; padding:15px 30px;">
          Start WhatsApp Booking
        </a>
      </div>
      <p class="cta-note">Free plan available for all registered doctors. No credit card required. Setup in one business day.</p>
    </div>
  </div>
</section>

{{-- ══════════════════════════════════════════
     PAGE-SPECIFIC ASSETS
═══════════════════════════════════════════ --}}
@push('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('frontend/css/whatsapp-booking.css') }}" />
@endpush

@push('scripts')
<script>
  // Scroll reveal
  const reveals = document.querySelectorAll('.reveal');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
  reveals.forEach(el => revealObserver.observe(el));

  // FAQ accordion — one open at a time
  document.querySelectorAll('#wb-faq details').forEach(detail => {
    detail.addEventListener('toggle', () => {
      if (detail.open) {
        document.querySelectorAll('#wb-faq details').forEach(other => {
          if (other !== detail && other.open) other.removeAttribute('open');
        });
      }
    });
  });
</script>
@endpush

@endsection