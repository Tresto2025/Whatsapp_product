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
                @endphp

               @if(in_array($appt->status, [1, 2]) && $appt->date >= $today)
                    <a href="javascript:void(0);" 
                       class="custom-tooltip cancel-btn cancel-appointment" 
                       data-id="{{ $appt->id }}" 
                       data-tooltip="Cancel" 
                       data-tooltip-pos="top">Cancel</a>

                    <a href="javascript:void(0);" 
                       class="custom-tooltip reschedule-btn" 
                       data-id="{{ $appt->id }}"
                       data-date="{{ date('Y-m-d', strtotime($appt->date)) }}"
                       data-time="{{ $appt->time }}">
                       Reschedule
                    </a>
                @endif
                 @if(in_array($appt->status, [1, 2]) && $appt->date == $today)
                    <a href="javascript:void(0);" 
                       class="custom-tooltip check_in_btn checkin-appointment" 
                       data-id="{{ $appt->id }}"
                       data-date="{{ date('Y-m-d', strtotime($appt->date)) }}"
                       data-time="{{ $appt->time }}">
                       Check in
                    </a>
                @endif
            <!--<a href="{{ route('doctor.patient-details', ['id' => $appt->id]) }}" 
               class="custom-tooltip view-btn" 
               data-tooltip="View Details" 
               data-tooltip-pos="top">View</a>-->
            </div>
        </div>
    </td>
</tr>
@endforeach