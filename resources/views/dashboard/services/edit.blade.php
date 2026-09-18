@extends('layouts.admin', ['activePage' => 'all-services', 'titlePage' => __('All Services')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Edit Service</h4>
                        <form action="{{ route('doctor.services.update', $service->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Service Name</label>
                                        <input type="text" name="service_name" class="form-control" value="{{ old('service_name', $service->service_name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group pt-4">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <a href="{{ route('doctor.services.index') }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
