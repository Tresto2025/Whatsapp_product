@extends('layouts.admin', ['activePage' => 'all-appointment', 'titlePage' => __('Add Appointment')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4>Add Appointment</h4>

                        <form class="form-sample" id="appointmentForm" action="{{ route('doctor.appointment.store') }}" method="POST">
                            @csrf
                            <div class="row">

                                {{-- Patient Name --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Full Name *</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                </div>

                                {{-- Phone --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Phone *</label>
                                        <input type="text" class="form-control" name="phone" id="phone" maxlength="10" required>
                                    </div>
                                </div>
                                {{-- Service --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Service Type *</label>
                                        <select class="form-control" name="service_type" required>
                                            <option value="">Select Service</option>
                                            @foreach ($services as $id => $service)
                                                <option value="{{ $service }}">{{ $service }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Date --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Appointment Date *</label>
                                        <input type="text" id="date" class="form-control" name="date" required>
                                    </div>
                                </div>

                                {{-- Slot Type --}}
                                @if($timing && $timing->slot_type === 'double')
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Preferred Slot *</label>
                                        <select class="form-control" id="slot_half" name="slot_half" required>
                                            <option value="">Select Slot</option>
                                            <option value="first">First Half</option>
                                            <option value="second">Second Half</option>
                                        </select>
                                    </div>
                                </div>
                                @endif

                                {{-- Time Slot --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Time Slot *</label>
                                        <select class="form-control" id="time_slot" name="time_slot" required>
                                            <option value="">Select Time</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Purpose --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Purpose of Visit *</label>
                                        <textarea class="form-control" name="purpose" rows="3" required></textarea>
                                    </div>
                                </div>
                                
                                {{-- Send WhatsApp Confirmation --}}
                                <div class="col-md-12 mt-3" style="margin-left: 20px;">
                                    <div class="form-group">
                                        <input class="form-check-input" type="checkbox" id="send_whatsapp" name="send_whatsapp" value="1">
                                        <label class="form-check-label" for="send_whatsapp" style="margin-top: 5px;">
                                            Send WhatsApp confirmation message to patient
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Submit Appointment</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');

    phoneInput.addEventListener('input', function(e) {
        // Remove all non-digit characters
        this.value = this.value.replace(/\D/g, '');
        
        // Limit to 10 digits
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10);
        }
    });

    // Optional: Prevent typing non-numeric keys like e, +, -, etc.
    phoneInput.addEventListener('keypress', function(e) {
        if (!/[0-9]/.test(e.key)) {
            e.preventDefault();
        }
    });
});
</script>

<script>
$(document).ready(function () {
    // === Initialize daterangepicker with min/max date ===
    const today = moment();
    const maxDate = moment().add(5, 'days');

    $('#date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minDate: today,
        maxDate: maxDate,
        locale: { format: 'YYYY-MM-DD' }
    });

    // === Fetch available time slots ===
    function fetchSlots() {
        const slotHalf = $('#slot_half').length ? $('#slot_half').val() : '';
        const selectedDate = $('#date').val();

        if (!selectedDate) return; // only run if date selected

        $.ajax({
            url: "{{ route('doctor.get.slots') }}",
            type: "POST",
            data: {
                slot_half: slotHalf,
                date: selectedDate,
                _token: "{{ csrf_token() }}"
            },
            beforeSend: function () {
                $('#time_slot').html('<option>Select Time</option>');
            },
            success: function (response) {
                $('#time_slot').empty().append('<option value="">Select Time</option>');
                if (response.slots && response.slots.length > 0) {
                    $.each(response.slots, function (i, slot) {
                        $('#time_slot').append('<option value="' + slot + '">' + slot + '</option>');
                    });
                } else {
                    $('#time_slot').append('<option value="">No Slots Available</option>');
                }
            },
            error: function () {
                $('#time_slot').html('<option>Error loading slots</option>');
            }
        });
    }

    // Event listeners
    $('#date').on('apply.daterangepicker', fetchSlots); // runs when user selects a date
    $('#slot_half').on('change', fetchSlots);
});
</script>
<script>
$(document).ready(function () {
    $('#appointmentForm').on('submit', function (e) {
        e.preventDefault(); // Stop form submit initially

        let date = $('#date').val();
        let time = $('#time_slot').val();
        let phone = $('#phone').val();

        // === Basic validation ===
        if (!date || !time || !phone) {
            Swal.fire({
                title: 'Missing Fields',
                text: 'Please fill all required fields before submitting.',
            });
            return;
        }

        // === Check phone length ===
        if (phone.length !== 10) {
            Swal.fire({
                title: 'Invalid Phone Number',
                text: 'Please enter a valid 10-digit phone number.',
            });
            return;
        }

        // === Check for past date/time ===
        const slotStartTime = time.split('-')[0].trim(); // start time
        const slotEndTime = time.split('-')[1].trim();   // end time

        // Combine with selected date, parse in 12-hour format
        const selectedStartDateTime = moment(date + ' ' + slotStartTime, 'YYYY-MM-DD h:mm A');
        const selectedEndDateTime = moment(date + ' ' + slotEndTime, 'YYYY-MM-DD h:mm A');
        const currentTime = moment();

        // Booking is allowed if **current time is before slot end**
        if (currentTime.isAfter(selectedEndDateTime)) {
            Swal.fire({
                title: 'Invalid Appointment Time',
                text: 'You cannot book an appointment in the past.',
            });
            return;
        }

        // === If all good, submit form ===
        this.submit();
    });
});

</script>
@endsection
