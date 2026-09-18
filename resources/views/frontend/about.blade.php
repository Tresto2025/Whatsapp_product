@extends('frontend.layout.app');


@section('title', 'About Us - My Company')
@section('description', 'Test')
@section('keywords', '')
@section('canonical', '')
@section('robots', 'index, follow')
@section('og:title', 'Tatkal Doctor - WhatsApp Appointment Booking System')
@section('og:url', 'https://tatkaldoctor.com/')
@section('og:description', 'Tatkal Doctor - WhatsApp Appointment Booking System')
@section('og:Keywords', '')
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

<style>
:root{
--green: var(--primary-green);
--blue: var(--primary-blue);
--border:#e5e7eb;
}

.about{
font-family:'Poppins',sans-serif;
background:#fafafa;
}

/* HERO */
.hero{
background:linear-gradient(135deg,var(--green),#0f766e);
color:#fff;
padding:80px 20px;
text-align:center;
}
.hero h1{font-size:42px;font-weight:700;}
.hero p{margin-top:12px;font-size:16px;opacity:.95;}

/* SECTION */
.section{padding:45px 20px;}
.container{max-width:1100px;margin:auto;}

/* GRID */
.grid{display:grid;gap:18px;}
.grid-2{grid-template-columns:repeat(auto-fit,minmax(320px,1fr));}
.grid-3{grid-template-columns:repeat(auto-fit,minmax(280px,1fr));}
.grid-4{grid-template-columns:repeat(auto-fit,minmax(220px,1fr));}

/* CARD */
.card{
background:#fff;
border:1px solid var(--border);
border-radius:12px;
padding:20px;
transition:.3s;
}
.card:hover{
transform:translateY(-4px);
border-color:var(--green);
}

/* STATS */
.stat-card{text-align:center;}
.stat-card h2{color:var(--green);font-size:28px;}

/* STEPS */
.step{display:flex;gap:12px;margin-bottom:14px;}
.step-num{
background:var(--green);
color:#fff;
width:28px;height:28px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
font-size:12px;
}

/* FAQ */
.faq-item{
border:1px solid var(--border);
border-radius:10px;
margin-bottom:10px;
background:#fff;
overflow:hidden;
}
.faq-q{
padding:16px;
cursor:pointer;
display:flex;
justify-content:space-between;
align-items:center;
font-weight:600;
}
.faq-a{
max-height:0;
overflow:hidden;
transition:max-height .3s ease;
padding:0 16px;
}
.faq-item.active .faq-a{
max-height:200px;
padding:16px;
}
.arrow{
transition:.3s;
}
.faq-item.active .arrow{
transform:rotate(180deg);
}

</style>

<div class="about">

<!-- HERO -->
<div class="hero">
<h1>India's AI Receptionist for Clinics & Hospitals</h1>
<p>Automate bookings, handle calls, reduce no-shows, and manage patient communication 24/7 without extra staff.</p>
</div>

<!-- ABOUT -->
<div class="section">
<div class="container">

<h2>What is Tatkal Doctor?</h2>

<p>
Tatkal Doctor is an AI-powered automation platform built for clinics and hospitals to manage patient communication efficiently without manual effort.
</p>

<ul style="margin-top:15px; line-height:1.8;">
<li><strong>WhatsApp Appointment System:</strong> Patients can book, reschedule, and confirm appointments directly through WhatsApp with real-time availability.</li>

<li><strong>AI Voice Call Handling:</strong> Incoming calls are automatically answered, patient queries are understood, and calls are routed or converted into bookings.</li>

<li><strong>24/7 Availability:</strong> No missed calls or messages — the system works round-the-clock.</li>

<li><strong>End-to-End Automation:</strong> From booking to reminders and follow-ups, everything is handled automatically.</li>

<li><strong>Built for Indian Clinics:</strong> Designed for real clinic workflows, high patient volume, and operational challenges.</li>
</ul>

</div>
</div>

<!-- STATS -->
<div class="section">
<div class="container">

<h2>Built for Clinics That Want to Scale Efficiently</h2>
<p>Improve patient experience while reducing manual workload.</p>

<div class="grid grid-4">

<div class="card stat-card">
<h2>100+</h2>
<p>Clinics using Tatkal Doctor</p>
</div>

<div class="card stat-card">
<h2>40%</h2>
<p>Reduction in missed appointments</p>
</div>

<div class="card stat-card">
<h2>2.5×</h2>
<p>Increase in bookings</p>
</div>

<div class="card stat-card">
<h2>24/7</h2>
<p>Automated patient communication</p>
</div>

</div>

</div>
</div>

<!-- FEATURES -->
<div class="section">
<div class="container">

<h2>Everything Your Clinic Needs to Manage Patient Communication</h2>
<p>Designed to automate routine operations and improve efficiency.</p>

<div class="grid grid-3">

<div class="card">
<h3>WhatsApp Appointment Management</h3>
<p>Patients can book, reschedule, or cancel appointments instantly through WhatsApp with real-time slot availability.</p>
</div>

<div class="card">
<h3>AI Voice Receptionist</h3>
<p>Automatically answers calls, understands patient intent, and routes calls or creates bookings without human intervention.</p>
</div>

<div class="card">
<h3>Automated Reminders & Follow-Ups</h3>
<p>Reduce no-shows with timely confirmations and reminders sent automatically.</p>
</div>

<div class="card">
<h3>Multi-Clinic & Doctor Management</h3>
<p>Manage multiple branches, doctors, and schedules from one centralized system.</p>
</div>

<div class="card">
<h3>Instant Reports & Prescription Delivery</h3>
<p>Send reports, prescriptions, and updates directly to patients via WhatsApp.</p>
</div>

<div class="card">
<h3>No-Code Workflow Setup</h3>
<p>Configure workflows and communication flows easily without technical knowledge.</p>
</div>

</div>

</div>
</div>

<!-- HOW IT WORKS -->
<div class="section">
<div class="container">

<h2>How Tatkal Doctor Works</h2>

<div class="grid grid-2">

<div>
<h3>WhatsApp Appointment Flow</h3>

<div class="step"><div class="step-num">1</div><div>Patient starts conversation on WhatsApp</div></div>
<div class="step"><div class="step-num">2</div><div>Selects service or doctor</div></div>
<div class="step"><div class="step-num">3</div><div>Chooses available time slot</div></div>
<div class="step"><div class="step-num">4</div><div>Receives confirmation & reminders</div></div>

</div>

<div>
<h3>AI Voice Call Handling</h3>

<div class="step"><div class="step-num">1</div><div>Call answered instantly</div></div>
<div class="step"><div class="step-num">2</div><div>Patient requirement understood</div></div>
<div class="step"><div class="step-num">3</div><div>Call routed or booking created</div></div>
<div class="step"><div class="step-num">4</div><div>No missed calls, even after hours</div></div>

</div>

</div>

</div>
</div>

<!-- FAQ -->
<div class="section">
<div class="container">

<h2>Frequently Asked Questions</h2>

<div class="faq-item">
<div class="faq-q">
<span>What does Tatkal Doctor do?</span>
<svg class="arrow" width="16" height="16" viewBox="0 0 24 24">
<path d="M6 9l6 6 6-6" stroke="#444" stroke-width="2" fill="none"/>
</svg>
</div>
<div class="faq-a">
Tatkal Doctor automates patient communication, appointment booking, reminders, and call handling using WhatsApp and AI voice technology.
</div>
</div>

<div class="faq-item">
<div class="faq-q">
<span>Will this replace my receptionist?</span>
<svg class="arrow" width="16" height="16" viewBox="0 0 24 24">
<path d="M6 9l6 6 6-6" stroke="#444" stroke-width="2" fill="none"/>
</svg>
</div>
<div class="faq-a">
It reduces workload significantly and can work alongside your staff to improve efficiency.
</div>
</div>

<div class="faq-item">
<div class="faq-q">
<span>Does it work after clinic hours?</span>
<svg class="arrow" width="16" height="16" viewBox="0 0 24 24">
<path d="M6 9l6 6 6-6" stroke="#444" stroke-width="2" fill="none"/>
</svg>
</div>
<div class="faq-a">
Yes, it handles calls and messages 24/7, ensuring no patient query is missed.
</div>
</div>

<div class="faq-item">
<div class="faq-q">
<span>Do I need new software or hardware?</span>
<svg class="arrow" width="16" height="16" viewBox="0 0 24 24">
<path d="M6 9l6 6 6-6" stroke="#444" stroke-width="2" fill="none"/>
</svg>
</div>
<div class="faq-a">
No, Tatkal Doctor integrates with your existing systems and workflows.
</div>
</div>

<div class="faq-item">
<div class="faq-q">
<span>Is patient data secure?</span>
<svg class="arrow" width="16" height="16" viewBox="0 0 24 24">
<path d="M6 9l6 6 6-6" stroke="#444" stroke-width="2" fill="none"/>
</svg>
</div>
<div class="faq-a">
Yes, all communication is encrypted and follows high security standards.
</div>
</div>

</div>
</div>

</div>

<script>
document.querySelectorAll('.faq-q').forEach(q=>{
q.addEventListener('click',()=>{
let item=q.parentElement;

// close others
document.querySelectorAll('.faq-item').forEach(i=>{
if(i!==item) i.classList.remove('active');
});

item.classList.toggle('active');
});
});
</script>
<!-- Structured Data for SEO + AI Parsing -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Tatkal Doctor",
  "url": "https://tatkaldoctor.com",
  "logo": "https://tatkaldoctor.com/logo.png",
  "description": "Tatkal Doctor is an AI-powered patient communication platform for clinics and hospitals offering WhatsApp appointment booking, AI voice call handling, and automated reminders.",
  "sameAs": [
    "https://tatkaldoctor.com"
  ]
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "name": "Tatkal Doctor AI Clinic Automation",
  "provider": {
    "@type": "Organization",
    "name": "Tatkal Doctor"
  },
  "areaServed": "India",
  "description": "AI-based WhatsApp appointment system and voice call automation for clinics and hospitals. Helps reduce no-shows, automate booking, and manage patient communication 24/7.",
  "serviceType": "Healthcare Communication Automation"
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "What does Tatkal Doctor do?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Tatkal Doctor automates patient communication, appointment booking, reminders, and call handling using WhatsApp and AI voice technology."
      }
    },
    {
      "@type": "Question",
      "name": "Does Tatkal Doctor reduce no-shows?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes, automated reminders and confirmations significantly reduce missed appointments."
      }
    },
    {
      "@type": "Question",
      "name": "Does it work after clinic hours?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes, Tatkal Doctor operates 24/7 to handle calls and messages automatically."
      }
    },
    {
      "@type": "Question",
      "name": "Is Tatkal Doctor suitable for clinics?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes, it is designed specifically for clinics and hospitals to automate patient communication and booking."
      }
    }
  ]
}
</script>
@endsection