@extends('layouts.admin', ['activePage' => 'profile', 'titlePage' => __('Update Profile')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <!-- Profile Update -->
            <div class="col-6 grid-margin">
                <div class="card">
                    <div class="card-body">
                        @if(session()->has('success'))
                        <div class="alert alert-success" style="width: max-content; margin: 10px; float: right; padding: 5px 10px;">
                            {{ session()->get('success') }}
                        </div>
                        @endif
                        <h4 class="font-weight-normal">Profile Update</h4>
                        <form action="{{ route('profile-update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $admin->id }}">

                            <div class="row">
                                <!-- Title -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Title *</label>
                                        <select class="form-control" name="title">
                                            <option value="">Select Title</option>
                                            <option value="Mr." {{ $admin->title == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                            <option value="Mrs." {{ $admin->title == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                            <option value="Miss" {{ $admin->title == 'Miss' ? 'selected' : '' }}>Miss</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- First Name -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>First Name *</label>
                                        <input type="text" class="form-control" name="first_name" value="{{ $admin->first_name }}">
                                    </div>
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Last Name *</label>
                                        <input type="text" class="form-control" name="last_name" value="{{ $admin->last_name }}">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Email *</label>
                                        <input type="text" class="form-control" name="email" value="{{ $admin->email }}">
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Phone *</label>
                                        <input type="text" class="form-control" name="phone" value="{{ $admin->phone }}">
                                    </div>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="col-6 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Change Password</h4>
                        <form action="{{ route('profile-update-password') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $admin->id }}">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Current Password *</label>
                                        <input type="password" class="form-control" name="current_password">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>New Password * <span class="hint_pass">(Minimum 8 characters)</span></label>
                                        <input type="password" class="form-control" name="password">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Confirm Password *</label>
                                        <input type="password" class="form-control" name="confirm_password">
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
