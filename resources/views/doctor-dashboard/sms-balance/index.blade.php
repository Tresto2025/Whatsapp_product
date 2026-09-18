@extends('layouts.admin', ['activePage' => 'doctor-sms-balance', 'titlePage' => __('doctor sms balance')])

@section('content')
<style>
    .pricing-table .pricing-card .pricing-card-body {
        padding: 50px 20px 43px 20px;
    }
    .pricing-table .pricing-card .pricing-card-body .plan-features li {
        font-size: 13px;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="container text-center">
                            <h2 class="font-weight-normal mb-4 mt-3">Choose a plan that suits you the best</h2>

                            <div class="row pricing-table">
                                @foreach($all_plans as $plan)
                                    <div class="col-md-4 grid-margin stretch-card pricing-card">
                                        <div class="card border border-primary pricing-card-body">
                                            <div class="text-center pricing-card-head">
                                                <h3>{{ $plan->plan_name }}</h3>
                                                <p>{{ $plan->no_of_messages }} SMS</p>
                                                <h1 class="font-weight-normal mb-4">₹{{ number_format($plan->price, 2) }}</h1>
                                            </div>

                                            <ul class="list-unstyled plan-features">
                                                <li>{{ $plan->description }}</li>
                                            </ul>

                                            <form id="paymentForm{{ $plan->id }}" action="{{ route('doctor.payment-success-data') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id{{ $plan->id }}">
                                            </form>

                                            <button class="btn btn-primary" onclick="payWithRazorpay({{ $plan->id }}, {{ $plan->price }}, '{{ $plan->plan_name }}', {{ $plan->no_of_messages }})">
                                                Buy Now
                                            </button>

                                            <p class="mt-3 mb-0 plan-cost text-gray">₹{{ number_format($plan->price, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function payWithRazorpay(planId, amount, planName, noOfMessages) {
    const key = "{{ config('razorpay.key') ?? env('RAZORPAY_KEY') }}";

    if (!key) {
        alert("Razorpay key missing! Please set RAZORPAY_KEY in .env and run php artisan config:clear");
        return;
    }

    var options = {
        "key": key,
        "amount": amount * 100,
        "currency": "INR",
        "name": "Doctor SMS Plan",
        "description": planName + " - " + noOfMessages + " SMS",
        "prefill": {
            "name": "{{ Auth::user()->name }}",
            "email": "{{ Auth::user()->email }}",
            "contact": "{{ Auth::user()->phone }}"
        },
        "theme": {
            "color": "#0d6efd"
        },
        "handler": function (response) {
            console.log("✅ Payment Success", response);
            // Set payment ID
            document.getElementById('razorpay_payment_id' + planId).value = response.razorpay_payment_id;
            // Submit the form
            document.getElementById('paymentForm' + planId).submit();
        },
        "modal": {
            "ondismiss": function(){
                console.log("❌ Payment popup closed by user");
            }
        }
    };

    var rzp = new Razorpay(options);
    rzp.open();
}
</script>
@endsection
