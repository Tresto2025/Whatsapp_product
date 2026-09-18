@extends('layouts.admin', ['activePage' => 'sms-balance', 'titlePage' => __('SMS Balance')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Add SMS</h4>
                        <form action="{{ route('sms.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Doctor</label>
                                        <input type="text" class="form-control" id="doctor_search" placeholder="Search doctor by name or email">
                                        <input type="hidden" name="doctor_id" id="doctor_id">
                                        @if ($errors->has('doctor_id'))
                                            <span class="text-danger">{{ $errors->first('doctor_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Total SMS</label>
                                        <input type="number" class="form-control" name="total_sms" value="{{ old('total_sms') }}" min="1">
                                        @if ($errors->has('total_sms'))
                                            <span class="text-danger">{{ $errors->first('total_sms') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Add SMS</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="font-weight-normal mb-2 mb-sm-0">SMS Balance List</h4>
                            <!--<div class="d-flex justtify-content-between align-items-center">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="packages_search" id="packages_search" class="form-control float-right" placeholder="Search">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Doctor Name</th>
                                        <th>Email</th>
                                        <th>Total SMS</th>
                                        <th>Pending SMS</th>
                                        <th>Spent SMS</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sms-dynamic-data">
                                    @foreach($smsList as $sms)
                                    <tr>
                                        <td>{{ $sms->doctor->name ?? '-' }}</td>
                                        <td>{{ $sms->doctor->email ?? '-' }}</td>
                                        <td>{{ $sms->total_sms }}</td>
                                        <td>{{ $sms->pending_sms }}</td>
                                        <td>{{ $sms->spent_sms }}</td>
                                        <td class="active-inactive">
                                            @if($sms->status == 1)
                                                <span class="badge badge-success" data-new="badge-danger" data-old="badge-success" data-text="In-Active">Active</span>
                                            @else
                                                <span class="badge badge-danger" data-new="badge-success" data-old="badge-danger" data-text="Active">In-Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sms->status == 1)
                                                <span class="custom-tooltip" data-tooltip="Change Status" data-tooltip-pos="top">
                                                    <i class="fa fa-toggle-on update-sms-status text-success" data-status="0" data-id="{{ $sms->id }}"></i>
                                                </span>
                                            @else
                                                <span class="custom-tooltip" data-tooltip="Change Status" data-tooltip-pos="top">
                                                    <i class="fa fa-toggle-off update-sms-status text-danger" data-status="1" data-id="{{ $sms->id }}"></i>
                                                </span>
                                            @endif
                                            <a href="{{ route('sms.edit', $sms->id) }}" class="custom-tooltip" data-tooltip="Edit SMS" data-tooltip-pos="top"><i class="fa fa-edit"></i></a>
                                            <!--<a href="{{ route('sms.destroy', $sms->id) }}" class="btn btn-sm btn-danger">Delete</a>-->
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
