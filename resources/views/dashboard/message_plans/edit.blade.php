@extends('layouts.admin', ['activePage' => 'message-plans', 'titlePage' => __('MessagePlans')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Message Plan</h3>
                    </div>
                    <div class="card-body">
                            <form method="POST" action="{{ route('message_plans.update', $plan->id) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Plan Name</label>
                                        <input type="text" name="plan_name" class="form-control" value="{{ old('plan_name', $plan->plan_name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>No. of Messages</label>
                                        <input type="number" name="no_of_messages" class="form-control" value="{{ old('no_of_messages', $plan->no_of_messages) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Price</label>
                                        <input type="number" name="price" class="form-control" value="{{ old('price', $plan->price) }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Description *</label>
                                        <input type="text" class="form-control" name="description" value="{{ old('description', $plan->description) }}" required>
                                        @if ($errors->has('description'))
                                            <span class="text-danger">{{ $errors->first('description') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('message_plans.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
