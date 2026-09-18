@extends('layouts.admin', ['activePage' => 'all-doctors', 'titlePage' => __('All Doctors')])

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <a href="{{route('doctor.create')}}">
                    <button type="button" class="btn btn-secondary">Add New Doctor</button>
                </a>
                <a href="{{route('doctors.import.form')}}">
                    <button type="button" class="btn btn-secondary">Import Doctors</button>
                </a>
                
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="font-weight-normal mb-2 mb-sm-0">All Doctors</h4>
                            <div class="d-flex">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="doctors_search" id="doctors_search" class="form-control float-right" placeholder="Search">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <!--<th>Experience</th>
                                        <th>Profession Type</th>
                                        <th>Gender</th>-->
                                        <th>City</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="doctors-dynamic-data">
                                    @foreach($doctors_list as $key => $doctor)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            @if($doctor->profile_image)
                                                <img src="{{ asset($doctor->profile_image) }}" alt="img" width="100" height="40">
                                            @else
                                                No Image
                                            @endif
                                        </td>
                                        <td><a href="{{ route('doctor.details', ['id' => $doctor->id]) }}" class="custom-tooltip">{{ ucwords(strtolower($doctor->first_name)) }} {{ ucwords(strtolower($doctor->last_name)) }}</a></td>
                                        <td><a href="javascript:void(0);" 
                                                   class="custom-tooltip copy-credentials-btn" 
                                                   data-tooltip="Copy Credentials" 
                                                   data-tooltip-pos="top" 
                                                   data-username="{{ $doctor->email }}" 
                                                   data-password="{{ $doctor->show_password ?? 'N/A' }}">
                                                    <i class="fa fa-copy"></i>
                                                </a> {{ $doctor->email }}</td>
                                        <td>{{ $doctor->country_code ?? '' }}{{ $doctor->phone }}</td>
                                        <!--<td>{{ $doctor->experience ?? '-' }}</td>
                                        <td>{{ $doctor->profession_type ?? '-' }}</td>
                                        <td>{{ $doctor->gender ?? '-' }}</td>-->
                                        <td>{{ $doctor->city ? ucwords(strtolower($doctor->city)) : '-' }}</td>
                                        <td class="active-inactive">
                                            @if($doctor->status == 1)
                                                <span class="badge badge-success" data-new="badge-danger" data-old="badge-success" data-text="In-Active">Active</span>
                                            @else
                                                <span class="badge badge-danger" data-new="badge-success" data-old="badge-danger" data-text="Active">In-Active</span>
                                            @endif
                                        </td>
                                        <td>{{ date('d-M-Y', strtotime($doctor->created_at)) }}</td>
                                        <td>
                                            <div class="tools">
                                        
                                                <a href="{{ route('doctor.edit', $doctor->id) }}" class="custom-tooltip" data-tooltip="Edit Doctor" data-tooltip-pos="top"><i class="fa fa-edit"></i></a>
                                                <a href="javascript:void(0);" class="custom-tooltip delete-doctor-btn" data-tooltip="Delete Doctor" data-tooltip-pos="top" data-id="{{ $doctor->id }}"><i class="fa fa-trash-o"></i></a>
                                                
                                                <a href="{{ route('doctor.qr-pdf', $doctor->id) }}" class="custom-tooltip" data-tooltip="Download QR" data-tooltip-pos="top"><i class="fa fa-qrcode"></i></a>
                                        
                                                {{-- Status toggle --}}
                                                @if($doctor->status == 1)
                                                    <span class="custom-tooltip" data-tooltip="Change Status" data-tooltip-pos="top">
                                                        <i class="fa fa-toggle-on update-doctor-status text-success" data-status="0" data-id="{{ $doctor->id }}"></i>
                                                    </span>
                                                @else
                                                    <span class="custom-tooltip" data-tooltip="Change Status" data-tooltip-pos="top">
                                                        <i class="fa fa-toggle-off update-doctor-status text-danger" data-status="1" data-id="{{ $doctor->id }}"></i>
                                                    </span>
                                                @endif
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
