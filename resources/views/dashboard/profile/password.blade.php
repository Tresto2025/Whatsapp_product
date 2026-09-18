@extends('layouts.admin', ['activePage' => 'profile', 'titlePage' => __('Update Profile')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <!-- Change Password -->
            <div class="col-6 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Change Password</h4>
                        <form action="{{ route('admin-update-password') }}" method="POST">
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
