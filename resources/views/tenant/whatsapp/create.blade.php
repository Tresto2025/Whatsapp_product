@extends('layouts.admin', ['activePage' => 'whatsapp-connection', 'titlePage' => __('Connect WhatsApp')])

@section('content')
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-8">

        @if ($errors->any())
          <div class="alert alert-danger">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
          </div>
        @endif

        <div class="card">
          <div class="card-body">
            <h4 class="card-title">{{ __('Connect your Meta WhatsApp number') }}</h4>
            <p class="text-muted">
              {{ __('Copy these from your Meta app under WhatsApp → API Setup. We check them with Meta before saving, so a wrong value is rejected here rather than failing silently on the first message.') }}
            </p>

            <form action="{{ route('tenant.whatsapp.store') }}" method="POST">
              @csrf

              <div class="form-group">
                <label for="label">{{ __('Label (optional)') }}</label>
                <input type="text" id="label" name="label" class="form-control"
                       value="{{ old('label') }}" placeholder="{{ __('e.g. Main clinic line') }}">
              </div>

              <div class="form-group">
                <label for="phone_number_id">{{ __('Phone number ID') }} <span class="text-danger">*</span></label>
                <input type="text" id="phone_number_id" name="phone_number_id" class="form-control"
                       value="{{ old('phone_number_id') }}" required>
                <small class="form-text text-muted">
                  {{ __('This is how inbound messages are routed to your workspace.') }}
                </small>
              </div>

              <div class="form-group">
                <label for="access_token">{{ __('Permanent access token') }} <span class="text-danger">*</span></label>
                <textarea id="access_token" name="access_token" class="form-control" rows="3" required>{{ old('access_token') }}</textarea>
                <small class="form-text text-muted">{{ __('Stored encrypted. It is never shown again after saving.') }}</small>
              </div>

              <div class="form-group">
                <label for="app_secret">{{ __('App secret') }}</label>
                <input type="text" id="app_secret" name="app_secret" class="form-control" value="{{ old('app_secret') }}">
                <small class="form-text text-muted">
                  {{ __('Used to verify the X-Hub-Signature-256 header on incoming webhooks. Strongly recommended.') }}
                </small>
              </div>

              <div class="form-group">
                <label for="waba_id">{{ __('WhatsApp Business Account ID') }}</label>
                <input type="text" id="waba_id" name="waba_id" class="form-control" value="{{ old('waba_id') }}">
              </div>

              <div class="form-group">
                <label for="meta_business_id">{{ __('Meta Business ID') }}</label>
                <input type="text" id="meta_business_id" name="meta_business_id" class="form-control" value="{{ old('meta_business_id') }}">
              </div>

              <div class="form-group">
                <label>{{ __('Callback URL to paste into Meta') }}</label>
                <input type="text" class="form-control" readonly value="{{ $webhookUrl }}">
                <small class="form-text text-muted">
                  {{ __('Your verify token is generated on save and shown on the connection list.') }}
                </small>
              </div>

              <button type="submit" class="btn btn-primary">{{ __('Verify and connect') }}</button>
              <a href="{{ route('tenant.whatsapp.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
