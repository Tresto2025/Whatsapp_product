@extends('layouts.admin', ['activePage' => 'doctor-services', 'titlePage' => __('Doctor Services')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            @if(session('success'))
                <div class="alert alert-success" style="width:max-content; margin:10px; float:right; padding:5px 10px;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" style="width:max-content; margin:10px; float:right; padding:5px 10px;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Select Your Services</h4>
                        <form id="serviceForm" action="{{ route('doctor.services.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                @foreach($services as $service)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" 
                                                       class="form-check-input service-checkbox" 
                                                       name="services[]" 
                                                       value="{{ $service->id }}"
                                                       {{ in_array($service->id, $selectedServices) ? 'checked' : '' }}>
                                                {{ $service->service_name }}
                                                <i class="input-helper"></i>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Save Services</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
