@extends('layouts.admin', ['activePage' => 'doctor-my-balance', 'titlePage' => __('My Balance')])

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Recharge Wallet</h4>
                    <form id="rechargeForm">
                        <div class="form-group">
                            <label>Enter Amount</label>
                            <input type="number" class="form-control" id="recharge_amount" placeholder="Minimum ₹50" min="50" required>
                            <small class="text-muted">
                                Minimum recharge amount is ₹50
                            </small>
                        </div>
                        <button type="button" id="rechargeBtn" class="btn btn-primary btn-block mt-3">
                            <i class="mdi mdi-wallet"></i> Recharge Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Wallet Info -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Wallet Information</h4>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Balance</span>
                        <strong>₹{{ $balance->wallet_balance ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Lifetime Spending</span>
                        <strong>₹{{ $balance->total_spent ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Overall paid messages sent</span>
                        <strong>{{ $sentSms ?? 0 }}</strong>
                    </div>
                    <hr>
                    <p class="text-muted mb-0">
                        Recharge your wallet to send WhatsApp notifications and broadcast messages.
                    </p>
                </div>
            </div>
        </div>
    </div>


    <!-- Stats Cards -->
    <div class="row">
        <!--<div class="col-md-4 grid-margin stretch-card">
            <div class="card aligner-wrapper">
                <div class="card-body">
                    <div class="absolute left top bottom h-100 v-strock-2 bg-primary"></div>
                    <p class="mb-2 text-dark">Total Message Balance</p>
                    <div class="d-flex align-items-center">
                        <h1 class="font-weight-medium mb-2 text-dark">{{ $balance->total_sms ?? 0 }}</h1>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary dot-indicator"></div>
                        <p class="text-muted mb-0 ml-2">Overall purchased</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card aligner-wrapper">
                <div class="card-body">
                    <div class="absolute left top bottom h-100 v-strock-2 bg-success"></div>
                    <p class="mb-2 text-dark">Remaining Messages</p>
                    <div class="d-flex align-items-center">
                        <h1 class="font-weight-medium mb-2 text-dark">{{ $balance->pending_sms ?? 0 }}</h1>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-success dot-indicator"></div>
                        <p class="text-muted mb-0 ml-2">Available balance</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card aligner-wrapper">
                <div class="card-body">
                    <div class="absolute left top bottom h-100 v-strock-2 bg-danger"></div>
                    <p class="mb-2 text-dark">Spent Messages</p>
                    <div class="d-flex align-items-center">
                        <h1 class="font-weight-medium mb-2 text-dark">{{ $balance->spent_sms ?? 0 }}</h1>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-danger dot-indicator"></div>
                        <p class="text-muted mb-0 ml-2">Already used</p>
                    </div>
                </div>
            </div>
        </div>-->
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card aligner-wrapper">
                <div class="card-body">
                    <div class="absolute left top bottom h-100 v-strock-2 bg-info"></div>
                    <p class="mb-2 text-dark">Today’s Usage</p>
                    <div class="d-flex align-items-center">
                        <h1 class="font-weight-medium mb-2 text-dark">{{ $todaySms ?? 0 }}</h1>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-info dot-indicator"></div>
                        <p class="text-muted mb-0 ml-2">Messages sent today</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card aligner-wrapper">
                <div class="card-body">
                    <div class="absolute left top bottom h-100 v-strock-2 bg-warning"></div>
                    <p class="mb-2 text-dark">This Month’s Usage</p>
                    <div class="d-flex align-items-center">
                        <h1 class="font-weight-medium mb-2 text-dark">{{ $monthSms ?? 0 }}</h1>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-warning dot-indicator"></div>
                        <p class="text-muted mb-0 ml-2">Messages sent this month</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card aligner-wrapper">
                <div class="card-body">
                    <div class="absolute left top bottom h-100 v-strock-2 bg-secondary"></div>
                    <p class="mb-2 text-dark">Last Recharge</p>
                    <div class="d-flex align-items-center">
                        <h1 class="font-weight-medium mb-2 text-dark">
                            ₹{{ $lastRecharge->amount ?? 0 }}
                        </h1>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary dot-indicator"></div>
                        <p class="text-muted mb-0 ml-2">
                           {{ $lastRecharge?->created_at?->format('d-M-Y h:i A') ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Recharge History Table -->
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="mdi mdi-history"></i> Recharge History
                </h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Transaction ID</th>
                                <th>Amount</th>
                                <th>Payment Gateway</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rechargeHistory as $index => $recharge)
                            <tr>
                                <td>{{ $rechargeHistory->firstItem() + $index }}</td>
                                <td>
                                    <code>{{ $recharge->transaction_id ?? 'N/A' }}</code>
                                </td>
                                <td>
                                    <strong class="text-success">₹{{ number_format($recharge->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ ucfirst($recharge->payment_gateway ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>
                                    @if($recharge->status == 'success' || $recharge->status == 'paid')
                                        <span class="badge badge-success">Success</span>
                                    @elseif($recharge->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-danger">{{ ucfirst($recharge->status ?? 'N/A') }}</span>
                                    @endif
                                </td>
                                <td>{{ $recharge->created_at->format('d-M-Y h:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="mdi mdi-wallet-outline" style="font-size:2rem;"></i>
                                    <p class="mt-2">No recharge history found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($rechargeHistory->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $rechargeHistory->links() }}
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('rechargeBtn').onclick = function(){
        var amount = document.getElementById('recharge_amount').value;
        if(amount < 50)
        {
            Swal.fire({
                title: 'Minimum Recharge',
                text: 'Minimum recharge amount is ₹50'
            });
            return;
        }
    
        var options = {
            "key": "{{ env('RAZORPAY_KEY') }}",
            "amount": amount * 100,
            "currency": "INR",
            "name": "Tatkal Doctor",
            "description": "Wallet Recharge",
            "handler": function (response){
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "/doctor/wallet-payment-success";
    
                var token = document.createElement("input");
                token.name="_token";
                token.value="{{ csrf_token() }}";
    
                var payment = document.createElement("input");
                payment.name="razorpay_payment_id";
                payment.value=response.razorpay_payment_id;
    
                var amt = document.createElement("input");
                amt.name="amount";
                amt.value=amount;
    
                form.appendChild(token);
                form.appendChild(payment);
                form.appendChild(amt);
                document.body.appendChild(form);
                form.submit();
            }
        };
        var rzp = new Razorpay(options);
        rzp.open();
    };
</script>
@endsection

