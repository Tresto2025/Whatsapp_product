@extends('layouts.admin', ['activePage' => 'all-appointment', 'titlePage' => __('All Appointment')])

@section('content')
<style>
    .col-sm-8 p {
        margin-bottom: 0;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Patient Detail</h3>
                    </div>
                    <div class="card-body view-listing">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Patient Name:</span></div>
                                    <div class="col-sm-8"><p>{{ ucwords(strtolower($detail->name)) }}</p></div>
                                </div><hr>

                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Patient Phone:</span></div>
                                    <div class="col-sm-8"><p>{{ $detail->phone }}</p></div>
                                </div><hr>

                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Service Type:</span></div>
                                    <div class="col-sm-8"><p>{{ ucwords(strtolower($detail->service_type)) }}</p></div>
                                </div><hr>

                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Purpose:</span></div>
                                    <div class="col-sm-8"><p>{{ ucwords(strtolower($detail->purpose)) }}</p></div>
                                </div><hr>
                            </div>

                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Appointment Date:</span></div>
                                    <div class="col-sm-8"><p>{{ \Carbon\Carbon::parse($detail->date)->format('d-M-Y') }}</p></div>
                                </div><hr>

                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Appointment Time:</span></div>
                                    <div class="col-sm-8"><p>{{ $detail->time }}</p></div>
                                </div><hr>

                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Status:</span></div>
                                    <div class="col-sm-8">
                                        <p>
                                            @if($detail->status == 1)
                                                Confirmed
                                            @elseif($detail->status == 2)
                                                Reschedule Request
                                            @elseif($detail->status == 3)
                                                Check In
                                            @elseif($detail->status == 4)
                                                Missed
                                            @else
                                                Cancelled
                                            @endif
                                        </p>
                                    </div>
                                </div><hr>

                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Created At:</span></div>
                                    <div class="col-sm-8"><p>{{ \Carbon\Carbon::parse($detail->created_at)->format('d-M-Y h:i A') }}</p></div>
                                </div><hr>
                            </div>
                        </div>
                    </div> <!-- /.card-body -->
                </div> <!-- /.card -->
            </div>
        </div>
    </div>
@endsection
