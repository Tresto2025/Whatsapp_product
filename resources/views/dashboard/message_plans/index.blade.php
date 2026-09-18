@extends('layouts.admin', ['activePage' => 'message-plans', 'titlePage' => __('MessagePlans')])

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-normal">Add Message Plans</h4>
                        <form action="{{ route('message_plans.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Plan Name</label>
                                        <input type="text" class="form-control" id="plan_name" name="plan_name" value="{{ old('plan_name') }}">
                                        @if ($errors->has('plan_name'))
                                            <span class="text-danger">{{ $errors->first('plan_name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">No. of Messages</label>
                                        <input type="number" class="form-control" name="no_of_messages" value="{{ old('no_of_messages') }}" min="1">
                                        @if ($errors->has('no_of_messages'))
                                            <span class="text-danger">{{ $errors->first('no_of_messages') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label">Price</label>
                                        <input type="text" class="form-control" id="price" name="price" value="{{ old('price') }}">
                                        @if ($errors->has('price'))
                                            <span class="text-danger">{{ $errors->first('price') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Description *</label>
                                        <input type="text" class="form-control" name="description" value="{{ old('description') }}">
                                        @if ($errors->has('description'))
                                            <span class="text-danger">{{ $errors->first('description') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="font-weight-normal mb-2 mb-sm-0">Message Plans List</h4>
                            <!--<div class="d-flex justtify-content-between align-items-center">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" name="packages_search" id="packages_search" class="form-control float-right" placeholder="Search">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Plan Name</th>
                                        <th>No. of Messages</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="plan-dynamic-data">
                                    @foreach($plans as $key => $plan)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ ucwords(strtolower($plan->plan_name)) }}</td>
                                        <td>{{ $plan->no_of_messages }}</td>
                                        <td>{{ $plan->price }}</td>
                                        <td class="active-inactive">
                                            @if($plan->status == 1)
                                                <span class="badge badge-success" data-new="badge-danger" data-old="badge-success" data-text="In-Active">Active</span>
                                            @else
                                                <span class="badge badge-danger" data-new="badge-success" data-old="badge-danger" data-text="Active">In-Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="tools">
                                            @if($plan->status == 1)
                                                <span class="custom-tooltip" data-tooltip="Change Status" data-tooltip-pos="top">
                                                    <i class="fa fa-toggle-on update-plan-status text-success" data-status="0" data-id="{{ $plan->id }}"></i>
                                                </span>
                                            @else
                                                <span class="custom-tooltip" data-tooltip="Change Status" data-tooltip-pos="top">
                                                    <i class="fa fa-toggle-off update-plan-status text-danger" data-status="1" data-id="{{ $plan->id }}"></i>
                                                </span>
                                            @endif
                                            <a href="{{ route('message_plans.edit', $plan->id) }}" class="custom-tooltip" data-tooltip="Edit Plan" data-tooltip-pos="top"><i class="fa fa-edit"></i></a>
                                            <a href="javascript:void(0);" class="custom-tooltip delete-plan-btn" data-tooltip="Delete Plan" data-tooltip-pos="top" data-id="{{ $plan->id }}"><i class="fa fa-trash-o"></i></a>
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
