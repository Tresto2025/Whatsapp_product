@extends('layouts.admin', ['activePage' => 'doctor-broadcast-messages', 'titlePage' => __('Doctor Broadcast Messages')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <!-- Add Broadcast Message Form -->
            <div class="col-12 grid-margin">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Add Broadcast Message</h4>
                        <form action="{{ route('doctor.broadcast_messages.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Title -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Title *</label>
                                        <input type="text" class="form-control" name="title" value="{{ old('title') }}">
                                        @error('title')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label" style="top: 0rem;">Select Patients (optional)</label>
                                        <select name="send_to[]" class="form-control js-example-basic-multiple send_to" multiple="multiple" style="width:100%">
                                            <option value="all">All Patients</option>
                                            @foreach($patients as $patient)
                                                <option value="{{ $patient->phone }}">
                                                    {{ $patient->name ?? 'Unknown' }} ({{ $patient->phone }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('send_to')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Description *</label>
                                        <input type="text" class="form-control" name="description" value="{{ old('description') }}">
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Image -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Image</label>
                                        <input type="file" class="form-control" name="image" accept="image/*">
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- Submit -->
                            <div class="col-md-12">
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Broadcast Messages List -->
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="font-weight-normal">Broadcast Messages List</h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Paid Messages</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Sent Date</th>
                                        <!--<th>Actions</th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $key => $msg)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $msg->title }}</td>
                                            <td style="max-width: 120px;white-space: normal;line-height: 20px;">{{ $msg->description }}</td>
                                            <td>{{ $msg->total_send_messages }}</td>
                                            <td>
                                                @if($msg->image)
                                                    <img src="{{ asset($msg->image) }}" width="60" height="60" class="rounded">
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </td>
                                            <td class="active-inactive">
                                                @if($msg->status == 1)
                                                    <span class="badge badge-success">Sent</span>
                                                @elseif($msg->status == 2)
                                                    <span class="badge badge-danger">Not Sent</span>
                                                @else
                                                    <span class="badge badge-secondary">Unknown</span>
                                                @endif
                                            </td>
                                            <td>{{ date('d-M-Y', strtotime($msg->created_at)) }}</td>
                                            <!--<td>
                                                <div class="tools d-flex gap-2">
                                                    <a href="{{ route('broadcast_messages.resend', $msg->id) }}"
                                                       class="custom-tooltip text-primary"
                                                       data-tooltip="Resend Message" data-tooltip-pos="top">
                                                        <i class="fa fa-paper-plane"></i>
                                                    </a>
                                                </div>
                                            </td>-->
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No broadcast messages found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection
