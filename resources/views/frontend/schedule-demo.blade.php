@extends('frontend.layout.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════════════════
   TOKENS
═══════════════════════════════════════════════════ */
:root {
  --g:        #0CBD93;
  --g-dark:   #09a47c;
  --g-dim:    rgba(12,189,147,.10);
  --ink:      #0d1117;
  --ink-2:    #374151;
  --ink-3:    #6b7280;
  --ink-4:    #9ca3af;
  --border:   #e5e7eb;
  --border-2: #f3f4f6;
  --white:    #ffffff;
  --bg:       #f8fafc;
  --sh:       0 1px 3px rgba(0,0,0,.06), 0 6px 20px rgba(0,0,0,.05);
  --ease:     cubic-bezier(.4,0,.2,1);
  --card-r:   18px;
  /* Fixed card height - both cards always match */
  --card-h:   920px;
}

/* ═══════════════════════════════════════════════════
   BASE
═══════════════════════════════════════════════════ */
.sdp-page {
  font-family: 'DM Sans', sans-serif;
  color: var(--ink-2);
  background: var(--bg);
  -webkit-font-smoothing: antialiased;
}
.sdp-page *, .sdp-page *::before, .sdp-page *::after {
  box-sizing: border-box; margin: 0; padding: 0;
}

/* ═══════════════════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════════════════ */
.sdp-header {
  padding: 36px 5% 0;
  max-width: 1380px;
  margin: 0 auto;
}
.sdp-header__kicker {
  font-size: .67rem;
  font-weight: 700;
  letter-spacing: .14em;
  text-transform: uppercase;
  color: var(--g-dark);
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.sdp-header__kicker::before {
  content: '';
  width: 18px; height: 2px;
  background: var(--g);
  border-radius: 2px;
  flex-shrink: 0;
}
.sdp-header__h1 {
  font-family: 'Sora', sans-serif;
  font-size: clamp(1.45rem, 2.8vw, 2.1rem);
  font-weight: 800;
  color: var(--ink);
  letter-spacing: -.03em;
  line-height: 1.15;
}

/* ═══════════════════════════════════════════════════
   WRAPPER + GRID
═══════════════════════════════════════════════════ */
.demo-wrapper {
  padding: 24px 5% 72px;
  max-width: 1380px;
  margin: 0 auto;
}
.demo-grid {
  display: flex;
  align-items: stretch;   /* equal height */
  gap: 18px;
  width: 100%;
}

/* ═══════════════════════════════════════════════════
   LEFT INFO CARD
═══════════════════════════════════════════════════ */
.demo-left-card {
  flex: 0 0 268px;
  width: 268px;
  min-height: var(--card-h);
  background: var(--white);
  border: 1px solid #edf1f5;
  border-radius: var(--card-r);
  padding: 24px 20px;
  box-shadow: var(--sh);
  display: flex;
  flex-direction: column;
  /*position: sticky;*/
  /*top: 86px;*/
  align-self: flex-start;
  /* GPU layer - prevent repaints on scroll */
  will-change: transform;
  transform: translateZ(0);
  animation: sdpFadeUp .42s var(--ease) both;
}

@keyframes sdpFadeUp {
  from { opacity: 0; transform: translateY(14px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* logo row */
.info-logo {
  display: flex; align-items: center; gap: 8px;
  padding-bottom: 14px; margin-bottom: 14px;
  border-bottom: 1px solid var(--border-2);
  flex-shrink: 0;
}
.info-logo img { height: 26px; width: auto; }

/* badge */
.info-badge {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: .64rem; font-weight: 700; letter-spacing: .08em;
  text-transform: uppercase;
  color: var(--g-dark);
  background: var(--g-dim);
  border: 1px solid rgba(12,189,147,.22);
  padding: 3px 9px; border-radius: 40px;
  margin-bottom: 11px; width: fit-content; flex-shrink: 0;
}
.info-dot {
  width: 5px; height: 5px; border-radius: 50%;
  background: var(--g);
  animation: sdpBlink 2.2s ease infinite;
}
@keyframes sdpBlink { 0%,100%{opacity:1} 50%{opacity:.25} }

/* host / name */
.info-host {
  font-size: .72rem; color: var(--ink-4); font-weight: 500;
  margin-bottom: 3px;
}
.info-name {
  font-family: 'Sora', sans-serif;
  font-size: 1rem; font-weight: 800;
  color: var(--ink); line-height: 1.3;
  margin-bottom: 14px;
}

/* meta */
.info-meta {
  display: flex; flex-direction: column; gap: 7px;
  margin-bottom: 14px; padding-bottom: 14px;
  border-bottom: 1px solid var(--border-2);
}
.info-meta-row {
  display: flex; align-items: flex-start; gap: 7px;
  font-size: .77rem; color: var(--ink-3); line-height: 1.45;
}
.info-meta-row svg {
  flex-shrink: 0; width: 14px; height: 14px;
  margin-top: 1px; color: var(--ink-4);
}

/* desc */
.info-desc {
  font-size: .77rem; line-height: 1.68; color: var(--ink-3);
  margin-bottom: 14px; padding-bottom: 14px;
  border-bottom: 1px solid var(--border-2);
}

/* bullets */
.info-bullets {
  list-style: none; display: flex; flex-direction: column;
  gap: 7px; margin-bottom: 0; flex: 1;
}
.info-bullets li {
  display: flex; align-items: flex-start; gap: 7px;
  font-size: .76rem; color: var(--ink-2); line-height: 1.5;
}
.info-check {
  flex-shrink: 0; width: 14px; height: 14px;
  border-radius: 50%; background: var(--g);
  display: flex; align-items: center; justify-content: center;
  margin-top: 2px;
}
.info-check svg { width: 7px; height: 7px; color: #fff; }

/* trust footer */
.info-trust {
  display: flex; align-items: center; gap: 5px;
  font-size: .7rem; color: var(--ink-4);
  margin-top: auto; padding-top: 14px;
  border-top: 1px solid var(--border-2);
  flex-shrink: 0;
}
.info-trust svg { width: 11px; height: 11px; color: var(--g); flex-shrink: 0; }

/* ═══════════════════════════════════════════════════
   RIGHT CARD  —  Cal.com embed
   KEY: fixed height, no overflow on outer card,
   iframe scrolls internally via Cal.com itself.
═══════════════════════════════════════════════════ */
.demo-right-card {
  flex: 1;
  min-width: 0;
  min-height: var(--card-h);
  background: var(--white);
  border: 1px solid #edf1f5;
  border-radius: var(--card-r);
  box-shadow: var(--sh);
  /* overflow:hidden clips the iframe to the rounded card */
   overflow: visible;
  position: relative;
  will-change: transform;
  transform: translateZ(0);
  animation: sdpFadeUp .42s .08s var(--ease) both;
}

/* ── Calendar heading bar ── */
.cal-heading-bar {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 52px;
  display: flex;
  align-items: center;
  padding: 0 22px;
  background: var(--white);
  border-bottom: 1px solid var(--border-2);
  z-index: 6;
  pointer-events: none;   /* don't block clicks on iframe below */
}
.cal-heading-bar__title {
  font-family: 'Sora', sans-serif;
  font-size: .9rem;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: -.01em;
}
.cal-heading-bar__pill {
  margin-left: 10px;
  font-size: .62rem;
  font-weight: 700;
  letter-spacing: .07em;
  text-transform: uppercase;
  color: var(--g-dark);
  background: var(--g-dim);
  border: 1px solid rgba(12,189,147,.2);
  padding: 2px 8px;
  border-radius: 20px;
}

/* ── Cal embed shell (sits below heading bar) ── */
.calendar-shell {
  position: absolute;
  inset: 0;
  padding-top: 52px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  contain: layout paint;
  transform: translateZ(0);
}
/*new added*/
.calendar-shell {
  height: 100%;
  min-height: var(--card-h);
}

#my-cal-inline-15min {
  height: 100%;
  min-height: var(--card-h);
}

#my-cal-inline-15min iframe {
  min-height: var(--card-h) !important;
  height: var(--card-h) !important;
}
/* Prevent outer page scroll conflict */
.demo-grid {
  align-items: flex-start;
}

/* Left card fixed */
.demo-left-card {
  height: var(--card-h);
}

/* Right card fixed */
.demo-right-card {
  height: var(--card-h);
}

/* ── Loading skeleton ── */
.sdp-skeleton {
  position: absolute;
  inset: 0;
  top: 52px; /* below heading bar */
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 14px;
  background: var(--white);
  z-index: 8;
  transition: opacity .5s var(--ease), visibility .5s var(--ease);
  will-change: opacity;
}
.sdp-skeleton.hidden {
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
}
/* Branded animated loader */
.sdp-skeleton__logo-wrap {
  position: relative;
  width: 48px; height: 48px;
  display: flex; align-items: center; justify-content: center;
}
.sdp-skeleton__ring {
  position: absolute; inset: 0;
  border: 2.5px solid var(--border-2);
  border-top-color: var(--g);
  border-radius: 50%;
  animation: sdpSpin .8s linear infinite;
}
.sdp-skeleton__dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--g);
  animation: sdpBlink 1.4s ease infinite;
}
@keyframes sdpSpin { to { transform: rotate(360deg); } }
.sdp-skeleton__label {
  font-size: .8rem; color: var(--ink-4); font-weight: 500;
  letter-spacing: .01em;
}
.sdp-skeleton__sub {
  font-size: .7rem; color: var(--ink-4);
  margin-top: -6px;
}

/* ── Cal.com embed div ── */
#my-cal-inline-15min {
  width: 100%;
  height: 100%;
  min-height: calc(var(--card-h) - 52px);
  /* Cal.com renders its own scroll inside its iframe */
  overflow: hidden;
  opacity: 0;
  transition: opacity .55s var(--ease);
  will-change: opacity;
}
#my-cal-inline-15min.cal-loaded { opacity: 1; }

/* Strip Cal.com iframe of any extra border / scrollbar bleed */
#my-cal-inline-15min iframe {
  width: 100% !important;
  min-height: calc(var(--card-h) - 52px) !important;
  height: 100% !important;
  border: none !important;
  display: block !important;
  border-radius: 0 !important;
  /* Cal handles internal scrolling */
  overflow: hidden !important;
}

/* ═══════════════════════════════════════════════════
   TABLET  ≤ 960px
═══════════════════════════════════════════════════ */
@media (max-width: 960px) {
  .demo-wrapper { padding: 20px 4% 56px; }

  .demo-grid {
    flex-direction: column;
    gap: 14px;
    align-items: stretch;
  }

  .demo-left-card {
    flex: none;
    width: 100%;
    position: static;
    min-height: auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 22px;
    border-radius: 14px;
    padding: 20px 18px;
  }
  .info-logo        { grid-column: 1/-1; }
  .info-badge       { grid-column: 1; }
  .info-host        { grid-column: 1; }
  .info-name        { grid-column: 1/-1; }
  .info-meta        { grid-column: 1; border-bottom: none; padding-bottom: 0; margin-bottom: 0; }
  .info-desc        { grid-column: 2; border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
  .info-bullets     { grid-column: 2; margin-bottom: 0; }
  .info-trust       { grid-column: 1/-1; margin-top: 12px; }

  .demo-right-card { min-height: 700px; border-radius: 14px; }
  #my-cal-inline-15min { min-height: calc(700px - 52px); }
  #my-cal-inline-15min iframe { min-height: calc(700px - 52px) !important; }
}

/* ═══════════════════════════════════════════════════
   MOBILE  ≤ 600px
═══════════════════════════════════════════════════ */
@media (max-width: 600px) {
  .sdp-header { padding: 22px 4% 0; }
  .sdp-header__h1 { font-size: 1.35rem; }

  .demo-wrapper { padding: 14px 4% 48px; }
  .demo-grid { gap: 12px; }

  .demo-left-card {
    display: flex; flex-direction: column;
    border-radius: 12px; padding: 16px 14px;
    min-height: auto; width: 100%;
  }
  .info-trust { margin-top: 12px; padding-top: 12px; }

  .demo-right-card {
    border-radius: 12px;
    min-height: 740px;
    overflow: hidden; width: 100%;
  }
  .calendar-shell { min-height: 740px; }
  #my-cal-inline-15min { min-height: calc(740px - 52px); }
  #my-cal-inline-15min iframe {
    min-height: calc(740px - 52px) !important;
    max-width: 100% !important;
  }
}

/* ═══════════════════════════════════════════════════
   EXTRA SMALL  ≤ 380px
═══════════════════════════════════════════════════ */
@media (max-width: 380px) {
  .demo-wrapper { padding: 10px 3% 40px; }
  .demo-left-card { padding: 13px 12px; }
  .sdp-header__h1 { font-size: 1.15rem; }
}
</style>
@endpush


@section('content')
<div class="sdp-page">

  {{-- PAGE HEADER --}}
  <header class="sdp-header">
    <div class="sdp-header__kicker">Book a Demo</div>
    <h1 class="sdp-header__h1">Schedule a Quick 30-Minute Demo</h1>
  </header>

  <section class="demo-wrapper">
    <div class="demo-grid">

      {{-- ═══ LEFT INFO CARD ═══ --}}
      <aside class="demo-left-card" aria-label="Meeting information">

        <div class="info-logo">
          <img src="{{ asset('frontend/images/logo.png') }}" alt="TatkalDoctor"
               onerror="this.style.display='none'">
        </div>

        <div class="info-badge">
          <span class="info-dot"></span>
          30-min free demo
        </div>

        <p class="info-host">Tatkal Doctor</p>

        <h2 class="info-name">AI Demo: WhatsApp &amp; Voice Automation for Clinics</h2>

        <div class="info-meta">
          <div class="info-meta-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            <span>30 minutes</span>
          </div>
          <div class="info-meta-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><rect x="2" y="7" width="15" height="10" rx="2"/><path d="M17 10l5-3v10l-5-3"/></svg>
            <span>Google Meet &middot; Link sent on confirmation</span>
          </div>
          <div class="info-meta-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10A15.3 15.3 0 0 1 12 2z"/></svg>
            <span>India Standard Time (IST)</span>
          </div>
        </div>

        <p class="info-desc">
          Book a short call to see how Tatkal Doctor helps clinics manage patients more efficiently with WhatsApp and voice call automation. No pitch &mdash; just a real product walkthrough.
        </p>

        <ul class="info-bullets">
          @foreach([
            'Reduce no-shows with automated reminders',
            'Patients book via WhatsApp 24/7',
            'AI voice agent handles inbound calls',
            'One dashboard for all appointments',
          ] as $point)
          <li>
            <span class="info-check">
              <svg viewBox="0 0 10 8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                <path d="M1 4l2.5 2.5L9 1"/>
              </svg>
            </span>
            {{ $point }}
          </li>
          @endforeach
        </ul>

        <p class="info-trust">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round">
            <path d="M12 2L3 7v5c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7L12 2z"/>
          </svg>
          No commitment &middot; Quick walk through.
        </p>

      </aside>

      {{-- ═══ RIGHT CALENDAR CARD ═══ --}}
      <div class="demo-right-card" aria-label="Scheduling calendar">

        {{-- Heading bar — always visible above iframe --}}
        <div class="cal-heading-bar">
          <span class="cal-heading-bar__title">Select a Date &amp; Time</span>
          <!--<span class="cal-heading-bar__pill">15 min</span>-->
        </div>

        <div class="calendar-shell">

          {{-- Branded loading skeleton --}}
          <div class="sdp-skeleton" id="sdpSkeleton" aria-live="polite">
            <div class="sdp-skeleton__logo-wrap">
              <div class="sdp-skeleton__ring"></div>
              <div class="sdp-skeleton__dot"></div>
            </div>
            <p class="sdp-skeleton__label">Loading calendar&hellip;</p>
            <p class="sdp-skeleton__sub">Fetching available slots</p>
          </div>

          {{--
            Cal.com REAL inline embed — DO NOT MODIFY.
            Creates actual bookings in Cal.com dashboard.
            Live availability. Sends real confirmation emails.
          --}}
          <div id="my-cal-inline-15min"></div>

        </div>

      </div>

    </div>
  </section>

</div>
@endsection


@push('scripts')

{{-- ════════════════════════════════════════════════════════════
     CAL.COM REAL INLINE EMBED — DO NOT CHANGE BOOKING LOGIC
════════════════════════════════════════════════════════════ --}}
<script>
(function (C, A, L) {
  let p = function (a, ar) { a.q.push(ar); };
  let d = C.document;
  C.Cal = C.Cal || function () {
    let cal = C.Cal; let ar = arguments;
    if (!cal.loaded) {
      cal.ns = {}; cal.q = cal.q || [];
      d.head.appendChild(d.createElement("script")).src = A;
      cal.loaded = true;
    }
    if (ar[0] === L) {
      const api = function () { p(api, arguments); };
      const namespace = ar[1];
      api.q = api.q || [];
      if (typeof namespace === "string") {
        cal.ns[namespace] = cal.ns[namespace] || api;
        p(cal.ns[namespace], ar);
        p(cal, ["initNamespace", namespace]);
      } else p(cal, ar);
      return;
    }
    p(cal, ar);
  };
})(window, "https://app.cal.com/embed/embed.js", "init");

Cal("init", "15min", { origin: "https://app.cal.com" });

Cal.ns["15min"]("inline", {
  elementOrSelector: "#my-cal-inline-15min",
  config: {
    layout: "month_view",
    useSlotsViewOnSmallScreen: "true"
  },
  calLink: "vinay-jain-auzfcq/15min",
});

Cal.ns["15min"]("ui", {
  cssVarsPerTheme: {
    light: { "cal-brand": "#0CBD93" },
    dark:  { "cal-brand": "#0CBD93" }
  },
  hideEventTypeDetails: true,
  layout: "month_view"
});
</script>

<script>
/* ═══════════════════════════════════════════════════════════════
  
══════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  var skeleton = document.getElementById('sdpSkeleton');
  var calDiv   = document.getElementById('my-cal-inline-15min');
  var revealed = false;

  if (!skeleton || !calDiv) return;

  /* ── reveal: crossfade skeleton out, iframe in ── */
  function reveal() {
    if (revealed) return;
    revealed = true;

    /* Fade in embed */
    calDiv.classList.add('cal-loaded');

    /* Slight delay so crossfade overlaps skeleton fade */
    setTimeout(function () {
      skeleton.classList.add('hidden');
    }, 100);
  }

  /* ── postMessage from app.cal.com ── */
  function onMsg(e) {
    try {
      var d = (typeof e.data === 'string') ? JSON.parse(e.data) : e.data;
      if (d && (d.type || d.__type || d.action)) {
        reveal();
        window.removeEventListener('message', onMsg);
      }
    } catch (_) {}
  }
  window.addEventListener('message', onMsg);

  /* ── MutationObserver: watch for iframe element ── */
  var mo = new MutationObserver(function (muts) {
    muts.forEach(function (m) {
      m.addedNodes.forEach(function (n) {
        if (n.tagName === 'IFRAME') {
          n.addEventListener('load', function () {
            setTimeout(reveal, 280);
          });
        }
      });
    });
  });
  mo.observe(calDiv, { childList: true, subtree: true });

  /* ── Hard failsafe ── */
  setTimeout(reveal, 6000);

  /* ── Equal-height sync (desktop only) ── */
  function syncH() {
    var left  = document.querySelector('.demo-left-card');
    var right = document.querySelector('.demo-right-card');
    var shell = document.querySelector('.calendar-shell');
    var embed = calDiv;
    if (!left || !right) return;

    if (window.innerWidth > 960) {
      /* Match right card to left card height, min 680px */
      var h = Math.max(left.getBoundingClientRect().height, 680);
      var hpx = h + 'px';
      var innerH = Math.max(h - 52, 628) + 'px';  /* minus heading bar */

      right.style.minHeight = hpx;
      if (shell)  shell.style.minHeight = hpx;
      if (embed)  embed.style.minHeight = innerH;

      /* Also patch iframe min-height inline */
      var iframes = calDiv.querySelectorAll('iframe');
      iframes.forEach(function (f) {
        f.style.minHeight = innerH;
        f.style.height    = innerH;
      });
    } else {
      /* Reset on mobile — CSS takes over */
      right.style.minHeight = '';
      if (shell)  shell.style.minHeight = '';
      if (embed)  embed.style.minHeight = '';
    }
  }

  /* Debounced resize */
  var rt;
  window.addEventListener('resize', function () {
    clearTimeout(rt);
    rt = setTimeout(syncH, 100);
  });

  /* Run at multiple points to catch async Cal.com paint */
  window.addEventListener('load', syncH);
  [300, 800, 1600, 3000].forEach(function (t) { setTimeout(syncH, t); });

})();
</script>

@endpush