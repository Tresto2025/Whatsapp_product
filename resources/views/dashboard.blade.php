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
                    <div class="card mdc-card info-card info-card--danger center-content">
                         <a href="{{ route('doctor.index') }}" class="dashboard-a-tag">
                        <div class="card-inner">
                            <h5 class="card-title">Manage Doctors</h5>
                            <!--<h5 class="font-weight-light">1/1</h5>-->
                            <div class="card-icon-wrapper">
                                <i class="mdi mdi-account-multiple menu-icon"></i>
                            </div>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 stretch-card grid-margin">
                    <div class="card mdc-card info-card info-card--primary center-content">
                        <a href="{{ route('broadcast_messages.index') }}" class="dashboard-a-tag">
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
                        <a href="{{ route('admin.message.price') }}" class="dashboard-a-tag">
                        <div class="card-inner">
                            <h5 class="card-title">Message Price</h5>
                            <!--<h5 class="font-weight-light">1/1</h5>-->
                            <div class="card-icon-wrapper">
                                <i class="mdi mdi-package-variant menu-icon"></i>
                            </div>
                        </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script>

</script>
@endsection


