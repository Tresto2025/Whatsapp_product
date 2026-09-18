@extends('layouts.admin', ['activePage' => 'all-doctors', 'titlePage' => __('Doctor Detail')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Doctor Detail</h3>
                    </div>
                    <div class="card-body view-listing">
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- Doctor Name -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Doctor Name:</span></div>
                                    <div class="col-sm-8">
                                        <p>{{ ucwords(strtolower($doctor->title)) }} {{ ucwords(strtolower($doctor->first_name)) }} {{ ucwords(strtolower($doctor->last_name)) }}</p>
                                    </div>
                                </div>
                                <hr>

                                <!-- Email -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Email:</span></div>
                                    <div class="col-sm-8"><p>{{ strtolower($doctor->email) }}</p></div>
                                </div>
                                <hr>

                                <!-- Phone -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Phone:</span></div>
                                    <div class="col-sm-8"><p>{{ $doctor->phone }}</p></div>
                                </div>
                                <hr>
                                <!-- Profession Type -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Profession Type:</span></div>
                                    <div class="col-sm-8"><p>{{ ucwords(strtolower($doctor->profession_type)) }}</p></div>
                                </div>
                                <hr>

                                <!-- Gender -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Gender:</span></div>
                                    <div class="col-sm-8"><p>{{ ucfirst(strtolower($doctor->gender)) }}</p></div>
                                </div>
                                <hr>

                                <!-- Profile Image -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Profile Image:</span></div>
                                    <div class="col-sm-8">
                                        @if($doctor->profile_image)
                                            <img src="{{ asset($doctor->profile_image) }}" alt="Profile Image" width="100">
                                        @else
                                            <p>No Image</p>
                                        @endif
                                    </div>
                                </div>
                                <hr>
                            </div>

                            <div class="col-sm-6">


                                <!-- City -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">City:</span></div>
                                    <div class="col-sm-8"><p>{{ ucwords(strtolower($doctor->city)) }}</p></div>
                                </div>
                                <hr>

                                <!-- Pan Number -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Pan Number:</span></div>
                                    <div class="col-sm-8"><p>{{ strtoupper($doctor->pan_number) }}</p></div>
                                </div>
                                <hr>

                                <!-- GST Number -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">GST Number:</span></div>
                                    <div class="col-sm-8"><p>{{ strtoupper($doctor->gst_number) }}</p></div>
                                </div>
                                <hr>

                                <!-- Start Time -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Start Time:</span></div>
                                    <div class="col-sm-8">
                                        <p>
                                            {{ $doctor->start_time ? date('h:i A', strtotime($doctor->start_time)) : 'Not Set' }}
                                        </p>
                                    </div>
                                </div>
                                <hr>

                                <!-- End Time -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">End Time:</span></div>
                                    <div class="col-sm-8">
                                        <p>
                                            {{ $doctor->end_time ? date('h:i A', strtotime($doctor->end_time)) : 'Not Set' }}
                                        </p>
                                    </div>
                                </div>
                                <hr>

                                <!-- Appointment Mode -->
                                <div class="row">
                                    <div class="col-sm-4"><span class="view-heading">Appointment Mode:</span></div>
                                    <div class="col-sm-8">
                                        <p>
                                            @if($doctor->appointment_mode == 1)
                                                Multiple Appointments
                                            @elseif($doctor->appointment_mode == 2)
                                                Single Appointment
                                            @else
                                                Not Set
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <hr>

                            </div>
                        </div>
                    </div> <!-- /.card-body -->
                </div> <!-- /.card -->
            </div>
        </div>
    </div>
@endsection
