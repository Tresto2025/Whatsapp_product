@extends('layouts.admin', ['activePage' => 'message-price', 'titlePage' => __('Message Price Settings')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Message Price Settings</h4>
                        <form action="{{ route('admin.message.price.update') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Price Per Message (₹)</label>
                                        <input type="number" class="form-control" name="price_per_message" value="{{ $price->price_per_message ?? 1 }}" min="1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Update Price</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection