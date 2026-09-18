@extends('layouts.admin', ['activePage' => 'all-services', 'titlePage' => __('All Services')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Add Service</h4>
                        <form action="{{ route('doctor.services.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Service Name</label>
                                        <input type="text" class="form-control" id="service_name" name="service_name" value="{{ old('service_name') }}">
                                        @if ($errors->has('service_name'))
                                            <span class="text-danger">{{ $errors->first('service_name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group pt-4">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
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
                            <h4 class="font-weight-normal mb-2 mb-sm-0">Services List</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Service Name</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="service-dynamic-data">
                                    @foreach($services as $key => $service)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $service->service_name }}</td>
                                        <td>{{ date('d-M-Y', strtotime($service->created_at)) }}</td>
                                        <td class="active-inactive">
                                            @if($service->status == 1)
                                                <span class="badge badge-success" data-new="badge-danger" data-old="badge-success" data-text="In-Active">Active</span>
                                            @else
                                                <span class="badge badge-danger" data-new="badge-success" data-old="badge-danger" data-text="Active">In-Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="tools">
                                            @if($service->status == 1)
                                                <span class="custom-tooltip" data-tooltip="Change Status" data-tooltip-pos="top">
                                                    <i class="fa fa-toggle-on update-service-status text-success" data-status="0" data-id="{{ $service->id }}"></i>
                                                </span>
                                            @else
                                                <span class="custom-tooltip" data-tooltip="Change Status" data-tooltip-pos="top">
                                                    <i class="fa fa-toggle-off update-service-status text-danger" data-status="1" data-id="{{ $service->id }}"></i>
                                                </span>
                                            @endif
                                            <a href="{{ route('doctor.services.edit', $service->id) }}" class="custom-tooltip" data-tooltip="Edit Service" data-tooltip-pos="top"><i class="fa fa-edit"></i></a>
                                            <a href="javascript:void(0);" class="custom-tooltip delete-service-btn" data-tooltip="Delete Service" data-tooltip-pos="top" data-id="{{ $service->id }}"><i class="fa fa-trash-o"></i></a>
                                            </div>
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
