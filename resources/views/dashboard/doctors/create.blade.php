@extends('layouts.admin', ['activePage' => 'all-doctors', 'titlePage' => __('Add Doctor')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Add Doctor</h4>

                        {{-- Display all validation errors at top --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="form-sample" action="{{ route('doctor.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                {{-- Title --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Title *</label>
                                        <select class="form-control" name="title" required>
                                            <option value="">Select Title</option>
                                            <option value="Mr" {{ old('title') == 'Mr' ? 'selected' : '' }}>Mr</option>
                                            <option value="Mrs" {{ old('title') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                            <option value="Miss" {{ old('title') == 'Miss' ? 'selected' : '' }}>Miss</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- First Name --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">First Name *</label>
                                        <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                                    </div>
                                </div>

                                {{-- Last Name --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Last Name *</label>
                                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                    </div>
                                </div>

                                {{-- Phone --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Phone *</label>
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Password * <span class="hint_pass">(Minimum 8 characters)</span></label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                </div>

                                {{-- Profession Type --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Profession Type *</label>
                                        <input type="text" class="form-control" name="profession_type" value="{{ old('profession_type') }}" required>
                                    </div>
                                </div>

                                {{-- Gender --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Gender *</label>
                                        <select class="form-control" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- City --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">City</label>
                                        <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                                    </div>
                                </div>

                                {{-- Profile Image --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Profile Image</label>
                                        <input type="file" class="form-control" name="profile_image" accept=".png,.jpg,.jpeg">
                                    </div>
                                </div>

                                {{-- PAN --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Pan Number</label>
                                        <input type="text" class="form-control" name="pan_number" value="{{ old('pan_number') }}">
                                    </div>
                                </div>

                                {{-- GST --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">GST Number</label>
                                        <input type="text" class="form-control" name="gst_number" value="{{ old('gst_number') }}">
                                    </div>
                                </div>

                                {{-- Address --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Address *</label>
                                        <input type="text" class="form-control" name="address" value="{{ old('address') }}" required>
                                    </div>
                                </div>

                                {{-- Services --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div id="services-wrapper">
                                        <label class="col-form-label" style="margin-bottom: 0;padding-bottom: 5px;">Services Types (Max 10)</label>
                                            @php $oldServices = old('services', ['']) @endphp
                                            @foreach($oldServices as $i => $service)
                                                <div class="input-group mb-2 service-item">
                                                    <input type="text" name="services[]" class="form-control service-input service_placeholder" placeholder="Enter Service Title" value="{{ $service }}" maxlength="50" required>
                                                    @if($i == 0)
                                                        <button type="button" class="btn btn-success btn-add-service">+</button>
                                                    @else
                                                        <button type="button" class="btn btn-danger btn-remove-service">-</button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- ================= WEEKLY AVAILABILITY ================= --}}
                                <div class="col-md-12 mt-4">
                                    <h5 class="mb-3">Weekly Availability</h5>
                                    @php
                                        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                                    @endphp
                                    @foreach($days as $day)
                                        @php $dayKey = strtolower($day); @endphp
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <label class="mb-0">
                                                    <input type="checkbox" class="day-checkbox" name="available_days[]" value="{{ $dayKey }}">
                                                    <strong class="ml-2">{{ $day }}</strong>
                                                </label>
                                            </div>
                                            <div class="card-body day-time-section d-none">
                                                <div class="row">
                                                    {{-- Slot Type --}}
                                                    <div class="col-md-4">
                                                        <label>Slot Type *</label>
                                                        <select name="timings[{{ $dayKey }}][slot_type]" class="form-control day-slot-type">
                                                            <option value="">Select Slot Type</option>
                                                            <option value="single">Single Slot</option>
                                                            <option value="double">Morning/Evening Slot</option>
                                                        </select>
                                                    </div>
                                                    {{-- Slot Gap --}}
                                                    <div class="col-md-4">
                                                        <label>Slot Gap *</label>
                                                        <select name="timings[{{ $dayKey }}][slot_time_gap]" class="form-control day-slot-gap">
                                                            <option value="">Select Slot Gap</option>
                                                            <option value="30">30 Minutes</option>
                                                            <option value="60">1 Hour</option>
                                                            <option value="120">2 Hours</option>
                                                            <option value="180">3 Hours</option>
                                                            <option value="240">4 Hours</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mt-3 single-slot-box d-none">
                                                    <div class="col-md-6">
                                                        <label>Start Time *</label>
                                                        <select class="form-control single-start" name="timings[{{ $dayKey }}][start_time]">
                                                            <option value="">-- Select Start Time --</option>
                                                            @foreach(range(9,19) as $hour)
                                                                @php $value = sprintf('%02d:00:00', $hour); @endphp
                                                                <option value="{{ $value }}">
                                                                    {{ date('h:i A', strtotime("$hour:00")) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>End Time *</label>
                                                        <select class="form-control single-end" name="timings[{{ $dayKey }}][end_time]">
                                                            <option value="">-- Select End Time --</option>
                                                            @foreach(range(9,21) as $hour)
                                                                @php $value = sprintf('%02d:00:00', $hour); @endphp
                                                                <option value="{{ $value }}">
                                                                    {{ date('h:i A', strtotime("$hour:00")) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- DOUBLE SLOT --}}
                                                <div class="row mt-3 double-slot-box d-none">
                                                    @foreach(['first_half_start','first_half_end','second_half_start','second_half_end'] as $field)
                                                        <div class="col-md-3">
                                                            <label>{{ ucwords(str_replace('_',' ',$field)) }} *</label>
                                                            <select class="form-control {{ $field }}" name="timings[{{ $dayKey }}][{{ $field }}]">
                                                                <option value="">-- Select Time --</option>
                                                                @foreach(range(9,21) as $hour)
                                                                    @php $value = sprintf('%02d:00:00', $hour); @endphp
                                                                    <option value="{{ $value }}">
                                                                        {{ date('h:i A', strtotime("$hour:00")) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>


                                {{-- Submit --}}
                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
