@extends('layouts.admin', ['activePage' => 'chatbot', 'titlePage' => __('Chatbot')])

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

        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">{{ __('Auto-replies') }}</h4>
              <a href="{{ route('tenant.chatbot.create') }}" class="btn btn-primary">{{ __('New auto-reply') }}</a>
            </div>

            <p class="text-muted small">
              {{ __('When an inbound message matches a keyword below, the reply is sent automatically. Each auto-reply is a single trigger and a single reply — no multi-step conversations yet.') }}
            </p>

            @if ($flows->isEmpty())
              <p class="text-muted mb-0">{{ __('No auto-replies yet.') }}</p>
            @else
              <div class="table-responsive">
                <table class="table table-striped mb-0">
                  <thead>
                    <tr>
                      <th>{{ __('Name') }}</th>
                      <th>{{ __('Keyword') }}</th>
                      <th>{{ __('Replies with') }}</th>
                      <th>{{ __('Status') }}</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach ($flows as $flow)
                    @php
                      $trigger = $flow->triggers->first();
                      $step = $flow->steps->sortBy('order')->first();
                    @endphp
                    <tr>
                      <td>{{ $flow->name }}</td>
                      <td><code>{{ $trigger?->value ?? '—' }}</code></td>
                      <td class="small text-muted">
                        @if ($step?->action_type === \App\Models\FlowStep::ACTION_SEND_TEXT)
                          {{ \Illuminate\Support\Str::limit($step->payload['body'] ?? '', 60) }}
                        @elseif ($step)
                          {{ __('Template') }} #{{ $step->payload['template_id'] ?? '—' }}
                        @else
                          —
                        @endif
                      </td>
                      <td>
                        <span class="badge badge-{{ $flow->is_active ? 'success' : 'warning' }}">
                          {{ $flow->is_active ? __('Active') : __('Paused') }}
                        </span>
                      </td>
                      <td class="text-right">
                        <form action="{{ route('tenant.chatbot.toggle', $flow) }}" method="POST" class="d-inline">
                          @csrf
                          <button class="btn btn-sm">{{ $flow->is_active ? __('Pause') : __('Activate') }}</button>
                        </form>
                        <form action="{{ route('tenant.chatbot.destroy', $flow) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('{{ __('Delete this auto-reply?') }}');">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
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

      </div>
    </div>
  </div>
</div>
@endsection
