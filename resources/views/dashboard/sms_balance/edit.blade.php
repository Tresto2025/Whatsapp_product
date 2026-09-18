@extends('layouts.admin', ['activePage' => 'sms-balance', 'titlePage' => __('Edit SMS Balance')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit SMS Balance for {{ $smsBalance->doctor->name }} ({{ $smsBalance->doctor->email }})</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('sms-balance.update', $smsBalance->id) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total SMS</label>
                                        <input type="number" name="total_sms" class="form-control" value="{{ old('total_sms', $smsBalance->total_sms) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Pending SMS</label>
                                        <input type="number" name="pending_sms" class="form-control" value="{{ old('pending_sms', $smsBalance->pending_sms) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Spent SMS</label>
                                        <input type="number" name="spent_sms" class="form-control" value="{{ old('spent_sms', $smsBalance->spent_sms) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('sms.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
