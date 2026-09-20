@extends('layouts.admin', ['activePage' => 'whatsapp-connection', 'titlePage' => __('WhatsApp Connection')])

@section('content')
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12">

        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
          </div>
        @endif

        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">{{ __('Connected WhatsApp Numbers') }}</h4>
              <a href="{{ route('tenant.whatsapp.create') }}" class="btn btn-primary">{{ __('Connect a number') }}</a>
            </div>

            @if ($accounts->isEmpty())
              <p class="text-muted">
                {{ __('No number connected yet. Connect your own Meta WhatsApp number to start receiving messages in this workspace — each tenant uses their own number and their own credentials.') }}
              </p>
              <a href="{{ route('tenant.whatsapp.create') }}" class="btn btn-primary">{{ __('Get started') }}</a>
            @else
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>{{ __('Number') }}</th>
                      <th>{{ __('Phone number ID') }}</th>
                      <th>{{ __('Status') }}</th>
                      <th>{{ __('Webhook') }}</th>
                      <th>{{ __('Verify token') }}</th>
                      <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach ($accounts as $account)
                    <tr>
                      <td>
                        {{ $account->display_phone_number ?: '—' }}
                        @if ($account->is_default)
                          <span class="badge badge-info">{{ __('default') }}</span>
                        @endif
                        @if ($account->label)
                          <div class="text-muted small">{{ $account->label }}</div>
                        @endif
                      </td>
                      <td><code>{{ $account->phone_number_id }}</code></td>
                      <td>
                        <span class="badge badge-{{ $account->isConnected() ? 'success' : 'warning' }}">
                          {{ $account->connection_status }}
                        </span>
                        @if ($account->last_error)
                          <div class="text-danger small">{{ $account->last_error }}</div>
                        @endif
                      </td>
                      <td>{{ $account->webhook_status }}</td>
                      <td><code class="small">{{ $account->verify_token }}</code></td>
                      <td class="text-right">
                        <form action="{{ route('tenant.whatsapp.recheck', $account) }}" method="POST" class="d-inline">
                          @csrf
                          <button class="btn btn-sm btn-outline-secondary">{{ __('Re-check') }}</button>
                        </form>
                        @unless ($account->is_default)
                          <form action="{{ route('tenant.whatsapp.default', $account) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-info">{{ __('Make default') }}</button>
                          </form>
                        @endunless
                        <form action="{{ route('tenant.whatsapp.destroy', $account) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('{{ __('Disconnect this number?') }}');">
                          @csrf @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger">{{ __('Disconnect') }}</button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h4 class="card-title">{{ __('Webhook settings for Meta') }}</h4>
            <p class="text-muted">
              {{ __('In your Meta app under WhatsApp → Configuration, set the callback URL below and paste the verify token shown for your number.') }}
            </p>
            <div class="form-group">
              <label>{{ __('Callback URL') }}</label>
              <input type="text" class="form-control" readonly value="{{ $webhookUrl }}">
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
