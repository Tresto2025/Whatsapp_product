@extends('layouts.admin', ['activePage' => 'home', 'titlePage' => __('Dashboard')])

@section('content')
<style>
    .mdc-card.info-card .card-inner .card-icon-wrapper i {
        font-size: 30px !important;
    }
    .center-content{
        justify-content: center;
    }
    .mdc-card.info-card{
        padding: 25px 25px;
    }
    
    /* Toggle Switch Style */
.switch {
  position: relative;
  display: inline-block;
  width: 46px;
  height: 24px;
}
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider {
  background-color: #4CAF50; /* green */
}
input:checked + .slider:before {
  transform: translateX(22px);
}
.slider.round {
  border-radius: 24px;
}

</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <!--<div class="col-xl-3 col-lg-6 stretch-card grid-margin">
                    <div class="card mdc-card info-card info-card--success">
                        <div class="card-inner">
                            <h5 class="card-title">Borrowed</h5>
                            <h5 class="font-weight-light pb-2 mb-1 border-bottom">$62,0076.00</h5>
                            <p class="text-small text-muted">48% target reached</p>
                            <div class="card-icon-wrapper">
                                <i class="mdi mdi-file-document-box"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 stretch-card grid-margin">
                    <div class="card mdc-card info-card info-card--danger">
                        <div class="card-inner">
                            <h5 class="card-title">Annual Profit</h5>
                            <h5 class="font-weight-light pb-2 mb-1 border-bottom">$1,958,104.00</h5>
                            <p class="text-small text-muted">55% target reached</p>
                            <div class="card-icon-wrapper">
                                <i class="mdi mdi-currency-eur"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 stretch-card grid-margin">
                    <div class="card mdc-card info-card info-card--primary">
                        <div class="card-inner">
                            <h5 class="card-title">Lead Conversion</h5>
                            <h5 class="font-weight-light pb-2 mb-1 border-bottom">$234,769.00</h5>
                            <p class="text-small text-muted">87% target reached</p>
                            <div class="card-icon-wrapper">
                                <i class="mdi mdi-trending-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 stretch-card grid-margin">
                    <div class="card mdc-card info-card info-card--info">
                        <div class="card-inner">
                            <h5 class="card-title">Average Income</h5>
                            <h5 class="font-weight-light pb-2 mb-1 border-bottom">$1,200.00</h5>
                            <p class="text-small text-muted">87% target reached</p>
                            <div class="card-icon-wrapper">
                                <i class="mdi mdi-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>-->
                <div class="col-xl-3 col-lg-6 stretch-card grid-margin">
                    <div class="card mdc-card info-card info-card--success center-content">
                            <a href="{{ route('doctor.appointments') }}" class="dashboard-a-tag">
                        <div class="card-inner">
                            <h5 class="card-title">Manage Appointments</h5>
                            <!--<h5 class="font-weight-light">1/1</h5>-->
                            <div class="card-icon-wrapper">
                                <i class="mdi mdi-account-multiple "></i>
                            </div>
                        </div>
                            </a>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 stretch-card grid-margin">
                    <div class="card mdc-card info-card info-card--danger center-content">
                        <a href="{{ route('doctor.my.balance') }}" class="dashboard-a-tag">
                        <div class="card-inner">
                            <h5 class="card-title">Manage Message Balance</h5>
                            <!--<h5 class="font-weight-light">1/1</h5>-->
                            <div class="card-icon-wrapper">
                                <i class="mdi mdi-whatsapp menu-icon"></i>
                            </div>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 stretch-card grid-margin">
                    <div class="card mdc-card info-card info-card--primary center-content">
                        <a href="{{ route('doctor.broadcast_messages.index') }}" class="dashboard-a-tag">
                            <div class="card-inner">
                                <h5 class="card-title">Broadcast message</h5>
                                <!--<h5 class="font-weight-light">1/1</h5>-->
                                <div class="card-icon-wrapper">
                                    <i class="mdi mdi-checkerboard menu-icon"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 stretch-card grid-margin">
                    <div class="card mdc-card info-card info-card--info center-content">
                        
                        <a href="{{ route('doctor.qr-pdf', $id) }}" class="dashboard-a-tag">
                        <div class="card-inner">
                            <h5 class="card-title">Download QR code</h5>
                            <!--<h5 class="font-weight-light">1/1</h5>-->
                            <div class="card-icon-wrapper">
                                <i class=" mdi mdi-qrcode-scan menu-icon"></i>
                            </div>
                        </div>
                         </a>
                    </div>
                </div>
               <!-- <div class="col-xl-3 col-lg-6 stretch-card grid-margin">
                    <div class="card mdc-card info-card info-card--info center-content">
                        <div class="card-inner dashboard-a-tag" style="cursor:pointer;" onclick="copyQrLink()">
                            <h5 class="card-title">Copy QR<br> Link</h5>
                            <div class="card-icon-wrapper">
                                <i class="mdi mdi-content-copy menu-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>-->

                
<!--<div class="col-xl-3 col-lg-6 stretch-card grid-margin">
    <div class="card mdc-card info-card info-card--primary center-content booking-card">
        <div class="card-inner d-flex align-items-center justify-content-between">
            
            <div class="d-flex align-items-center">
                <div class="card-icon-wrapper">
                    <i class="mdi mdi-calendar-check menu-icon"></i>
                </div>
                <h5 class="card-title mb-0">Booking Today</h5>
            </div>

            <div class="toggle-wrapper text-right">
                <label class="switch">
                    <input type="checkbox" id="bookingToggle" data-id="{{ $doctor->id }}" {{ $doctor->booking_enabled ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
                <small id="toggleStatus">{{ $doctor->booking_enabled ? 'Enabled' : 'Disabled' }}</small>
            </div>
        </div>
    </div>
</div>-->


            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function copyQrLink() {
    const doctorName = "{{ $doctor->first_name }} {{ $doctor->last_name }}";
    const whatsappLink = `https://wa.me/917011551429?text=${encodeURIComponent("Hi, Dr. {{ $doctor->first_name }} {{ $doctor->last_name }},\nDoctor code WAB-{{ $doctor->id }}")}`;

    // Full invitation text
    const invitationText = `You can book your doctor appointments with Dr. ${doctorName}.\nClick on this link: ${whatsappLink}`;

    // Copy to clipboard
    navigator.clipboard.writeText(invitationText).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'QR Link Copied!',
            text: 'You can now share this link with your clients.',
            timer: 2000,
            showConfirmButton: false
        });
    }).catch(err => {
        console.error('Could not copy text: ', err);
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Failed to copy invitation.'
        });
    });
}
</script>

@endsection


