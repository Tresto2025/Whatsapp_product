@extends('layouts.admin', ['activePage' => 'doctor-profile', 'titlePage' => __('Edit Profile')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Edit Profile</h4>

                        {{-- Display all validation errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="form-sample" action="{{ route('doctor-my-profile-update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $user->id }}">

                            <div class="row">
                                {{-- Title --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Title *</label>
                                        <select class="form-control" name="title" required>
                                            <option value="">Select Title</option>
                                            <option value="Mr" {{ old('title', $user->title) == 'Mr' ? 'selected' : '' }}>Mr</option>
                                            <option value="Mrs" {{ old('title', $user->title) == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                            <option value="Miss" {{ old('title', $user->title) == 'Miss' ? 'selected' : '' }}>Miss</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- First Name --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">First Name *</label>
                                        <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                    </div>
                                </div>

                                {{-- Last Name --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Last Name *</label>
                                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>

                                {{-- Phone --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Phone *</label>
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                    </div>
                                </div>

                                {{-- Profession Type --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Profession Type *</label>
                                        <input type="text" class="form-control" name="profession_type" value="{{ old('profession_type', $user->profession_type) }}" required>
                                    </div>
                                </div>

                                {{-- Gender --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Gender *</label>
                                        <select class="form-control" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- City --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">City</label>
                                        <input type="text" class="form-control" name="city" value="{{ old('city', $user->city) }}">
                                    </div>
                                </div>

                                {{-- Profile Image --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Profile Image</label>
                                        <input type="file" class="form-control" name="profile_image" accept=".png,.jpg,.jpeg">
                                        @if($user->profile_image)
                                            <img src="{{ asset($user->profile_image) }}" alt="Profile Image" width="80" class="mt-2 rounded">
                                        @endif
                                    </div>
                                </div>

                                {{-- PAN --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Pan Number</label>
                                        <input type="text" class="form-control" name="pan_number" value="{{ old('pan_number', $user->pan_number) }}">
                                    </div>
                                </div>

                                {{-- GST --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">GST Number</label>
                                        <input type="text" class="form-control" name="gst_number" value="{{ old('gst_number', $user->gst_number) }}">
                                    </div>
                                </div>

                                {{-- Address --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Address *</label>
                                        <input type="text" class="form-control" name="address" value="{{ old('address', $user->address) }}" required>
                                    </div>
                                </div>

                                @php
                                    // Fetch existing services
                                    $services = old('services') ?? \App\Models\DoctorService::where('doctor_id', $user->id)->pluck('service_name')->toArray();
                                    // Fetch existing timings
                                    $timing = old('slot_type') 
                                        ? (object) [
                                            'slot_type' => old('slot_type'),
                                            'slot_time_gap' => old('slot_time_gap'),
                                            'start_time' => old('start_time'),
                                            'end_time' => old('end_time'),
                                            'first_half_start' => old('first_half_start'),
                                            'first_half_end' => old('first_half_end'),
                                            'second_half_start' => old('second_half_start'),
                                            'second_half_end' => old('second_half_end')
                                        ]
                                        : \App\Models\DoctorTimings::where('doctor_id', $user->id)->first() ?? (object)[];
                                @endphp

                                {{-- Services --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Services Types (Max 10)</label>
                                        <div id="services-wrapper">
                                            @forelse($services as $index => $svc)
                                                <div class="input-group mb-2 service-item">
                                                    <input type="text" name="services[]" class="form-control" value="{{ $svc }}" placeholder="Enter Service Title" maxlength="50" required>
                                                    @if($loop->first)
                                                        <button type="button" class="btn btn-success btn-add-service">+</button>
                                                    @else
                                                        <button type="button" class="btn btn-danger btn-remove-service">-</button>
                                                    @endif
                                                </div>
                                            @empty
                                                <div class="input-group mb-2 service-item">
                                                    <input type="text" name="services[]" class="form-control" placeholder="Enter Service Title" maxlength="50" required>
                                                    <button type="button" class="btn btn-success btn-add-service">+</button>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                {{-- ================= WEEKLY AVAILABILITY ================= --}}
                                @php
                                    $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                                
                                    // Fetch saved timings from DB
                                    $savedTimings = \App\Models\DoctorTimings::where('doctor_id', $user->id)
                                                    ->get()
                                                    ->keyBy('day'); // assuming column 'day' exists
                                @endphp
                                
                                <div class="col-md-12 mt-4">
                                    <h5 class="mb-3">Weekly Availability</h5>
                                
                                    @foreach($days as $day)
                                        @php
                                            $dayKey = strtolower($day);
                                
                                            $timing = old("timings.$dayKey")
                                                ? (object) old("timings.$dayKey")
                                                : ($savedTimings[$dayKey] ?? null);
                                
                                            $isChecked = old('available_days')
                                                ? in_array($dayKey, old('available_days'))
                                                : isset($savedTimings[$dayKey]);
                                        @endphp
                                
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <label class="mb-0">
                                                    <input type="checkbox" class="day-checkbox" name="available_days[]" value="{{ $dayKey }}" {{ $isChecked ? 'checked' : '' }}>
                                                    <strong class="ml-2">{{ $day }}</strong>
                                                </label>
                                            </div>
                                
                                            <div class="card-body day-time-section {{ $isChecked ? '' : 'd-none' }}">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>Slot Type *</label>
                                                        <select name="timings[{{ $dayKey }}][slot_type]" class="form-control day-slot-type">
                                                            <option value="">Select Slot Type</option>
                                                            <option value="single"
                                                                {{ ($timing->slot_type ?? '') == 'single' ? 'selected' : '' }}>
                                                                Single Slot
                                                            </option>
                                                            <option value="double"
                                                                {{ ($timing->slot_type ?? '') == 'double' ? 'selected' : '' }}>
                                                                Morning/Evening Slot
                                                            </option>
                                                        </select>
                                                    </div>
                                
                                                    {{-- Slot Gap --}}
                                                    <div class="col-md-4">
                                                        <label>Slot Gap *</label>
                                                        <select name="timings[{{ $dayKey }}][slot_time_gap]" class="form-control day-slot-gap">
                                                            <option value="">Select Slot Gap</option>
                                                            <option value="30"
                                                                {{ ($timing->slot_time_gap ?? '') == 30 ? 'selected' : '' }}>
                                                                30 Minutes
                                                            </option>
                                                            <option value="60"
                                                                {{ ($timing->slot_time_gap ?? '') == 60 ? 'selected' : '' }}>
                                                                1 Hour
                                                            </option>
                                                            <option value="120" {{ ($timing->slot_time_gap ?? '') == 120 ? 'selected' : '' }}>2 Hours</option>
                                                            <option value="180" {{ ($timing->slot_time_gap ?? '') == 180 ? 'selected' : '' }}>3 Hours</option>
                                                            <option value="240" {{ ($timing->slot_time_gap ?? '') == 240 ? 'selected' : '' }}>4 Hours</option>
                                                        </select>
                                                    </div>
                                                </div>
                                
                                                {{-- SINGLE SLOT --}}
                                                <div class="row mt-3 single-slot-box 
                                                    {{ ($timing->slot_type ?? '') == 'single' ? '' : 'd-none' }}">
                                
                                                    <div class="col-md-6">
                                                        <label>Start Time *</label>
                                                        <select class="form-control single-start" name="timings[{{ $dayKey }}][start_time]">
                                                            <option value="">-- Select Start Time --</option>
                                                            @foreach(range(9,21) as $hour)
                                                                @php $value = sprintf('%02d:00:00', $hour); @endphp
                                                                <option value="{{ $value }}"
                                                                    {{ ($timing->start_time ?? '') == $value ? 'selected' : '' }}>
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
                                                                <option value="{{ $value }}"
                                                                    {{ ($timing->end_time ?? '') == $value ? 'selected' : '' }}>
                                                                    {{ date('h:i A', strtotime("$hour:00")) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                
                                                {{-- DOUBLE SLOT --}}
                                                <div class="row mt-3 double-slot-box {{ ($timing->slot_type ?? '') == 'double' ? '' : 'd-none' }}">
                                                    @foreach(['first_half_start','first_half_end','second_half_start','second_half_end'] as $field)
                                                        <div class="col-md-3">
                                                            <label>{{ ucwords(str_replace('_',' ',$field)) }} *</label>
                                                            <select class="form-control {{ $field }}" name="timings[{{ $dayKey }}][{{ $field }}]">
                                                                <option value="">-- Select Time --</option>
                                                                @foreach(range(9,21) as $hour)
                                                                    @php $value = sprintf('%02d:00:00', $hour); @endphp
                                                                    <option value="{{ $value }}"
                                                                        {{ ($timing->$field ?? '') == $value ? 'selected' : '' }}>
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
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection
