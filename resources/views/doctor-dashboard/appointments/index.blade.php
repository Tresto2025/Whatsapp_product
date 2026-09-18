@extends('layouts.admin', ['activePage' => 'all-appointment', 'titlePage' => __('All Appointment')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <a href="{{route('doctor.appointment.create')}}">
                    <button type="button" class="btn btn-secondary">Add New Appointment</button>
                </a>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="font-weight-normal mb-2 mb-sm-0">All Appointments</h4>
                            <div class="d-flex" style="gap: 30px;">
                                <div class="input-group input-group-sm" style="width: 220px;">
                                    <input type="text" name="appointments_date" id="appointments_date" class="form-control" placeholder="Filter by date">
                                </div>
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="appointments_search" id="appointments_search" class="form-control float-right" placeholder="Search">
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
                                        <th>User Name</th>
                                        <th>Service Type</th>
                                        <th>Booking Date</th>
                                        <th>Booking Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="appointment-dynamic-data">
                                    @foreach($appointments as $appt)
                                    <tr>
                                        <td><a href="{{ route('doctor.patient-details', ['id' => $appt->id]) }}" class="custom-tooltip">{{ ucwords(strtolower($appt->name)) }}</a></td>
                                        <td>{{ ucwords(strtolower($appt->service_type)) }}</td>
                                        <td>{{ date('d-M-Y', strtotime($appt->date)) }}</td>
                                        <td>{{ $appt->time }}</td>
                                        <td class="active-inactive">
                                            @if($appt->status == 1)
                                                <span class="badge badge-success">Confirmed</span>
                                            @elseif($appt->status == 2)
                                                <span class="badge badge-success">Reschedule Request</span>
                                            @elseif($appt->status == 3)
                                                <span class="badge badge-success">Checked In</span>
                                            @elseif($appt->status == 4)
                                                <span class="badge badge-danger">Missed</span>
                                            @else
                                                <span class="badge badge-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="tools">
                                                <div class="custom-apointment-btn">
                                                    @php
                                                        $today = date('Y-m-d');
                                                        $updatedToday = \Carbon\Carbon::parse($appt->updated_at)->isToday();
                                                        $appt->is_paid = !\Carbon\Carbon::parse($appt->updated_at)->isToday();
                                                    @endphp
                                                    
                                                    @if(in_array($appt->status, [1, 2]) && $appt->date >= $today)
                                                    <a href="javascript:void(0);" class="custom-tooltip cancel-btn cancel-appointment" data-id="{{ $appt->id }}" data-paid="{{ $appt->is_paid ? 1 : 0 }}" data-tooltip="Cancel" data-tooltip-pos="top">Cancel</a>
                                                    <a href="javascript:void(0);" class="custom-tooltip reschedule-btn" data-id="{{ $appt->id }}" data-paid="{{ $appt->is_paid ? 1 : 0 }}" data-date="{{ date('Y-m-d', strtotime($appt->date)) }}" data-time="{{ $appt->time }}">Reschedule</a>
                                                    @endif
                                                    @if(in_array($appt->status, [1, 2]) && $appt->date == $today)
                                                        <a href="javascript:void(0);" class="custom-tooltip check_in_btn checkin-appointment" data-id="{{ $appt->id }}" data-date="{{ date('Y-m-d', strtotime($appt->date)) }}" data-time="{{ $appt->time }}">Check in </a>
                                                    @endif
                                                <!--<a href="{{ route('doctor.patient-details', ['id' => $appt->id]) }}" class="custom-tooltip view-btn" data-tooltip="View Details" data-tooltip-pos="top">View</a>-->
                                                </div>
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
    
    <!-- Reschedule Modal -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="rescheduleForm">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Reschedule Appointment</h5>
            </div>
            <div class="modal-body">
              <input type="hidden" name="appointment_id" id="appointment_id">
              <div class="mb-3">
                <label class="form-label">Select Date</label>
                <select class="form-control" id="reschedule_date" name="date" required>
                  <option value="" selected>-- Select Date --</option>
                  @foreach($dates as $index => $d)
                  <option value="{{ $d['id'] }}">
                      {{ $d['label'] }}
                </option>
                @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Select Time Slot</label>
                <select class="form-control" id="reschedule_time" name="time" required>
                    <option value="">-- Select Time --</option>
                </select>
            </div>

            </div>
            <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal" style="margin-right: 10px;">Cancel</button>
              <button type="submit" class="btn btn-primary">Reschedule</button>
            </div>
          </form>
        </div>
      </div>
    </div>
@endsection