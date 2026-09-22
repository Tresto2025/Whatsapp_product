@extends('layouts.admin', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

@section('content')
<div class="main-panel">
  <div class="content-wrapper">

    @if (Auth::user()->isSuperAdmin())
      <div class="alert alert-info">
        {{ __('You are signed in as the platform super admin, so these figures cover every workspace.') }}
      </div>
    @endif

    @if (!Auth::user()->isSuperAdmin() && $connectedNumbers === 0)
      <div class="alert alert-warning">
        {{ __('No WhatsApp number is connected yet, so this workspace cannot send or receive messages.') }}
        @if (Auth::user()->administersWorkspace())
          <a href="{{ route('tenant.whatsapp.create') }}" class="alert-link">{{ __('Connect one now') }}</a>.
        @endif
      </div>
    @endif

    <div class="row">
      @foreach ([
        ['label' => __('Connected numbers'), 'value' => $connectedNumbers, 'icon' => 'mdi-whatsapp'],
        ['label' => __('Contacts'),          'value' => $contacts,         'icon' => 'mdi-account-multiple'],
        ['label' => __('Open conversations'),'value' => $openConversations,'icon' => 'mdi-forum'],
        ['label' => __('Responses'),         'value' => $responses,        'icon' => 'mdi-clipboard-check'],
      ] as $card)
        <div class="col-md-3 stretch-card grid-margin">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <p class="mb-1 text-muted">{{ $card['label'] }}</p>
                  <h3 class="mb-0">{{ number_format($card['value']) }}</h3>
                </div>
                <i class="mdi {{ $card['icon'] }} icon-lg text-primary"></i>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="row">
      <div class="col-md-4 stretch-card grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">{{ __('Messages') }}</h4>
            <p class="mb-1">{{ __('Received') }}: <strong>{{ number_format($messagesIn) }}</strong></p>
            <p class="mb-0">{{ __('Sent') }}: <strong>{{ number_format($messagesOut) }}</strong></p>
          </div>
        </div>
      </div>

      <div class="col-md-8 stretch-card grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">{{ __('Recent conversations') }}</h4>
            @if ($recentConversations->isEmpty())
              <p class="text-muted mb-0">
                {{ __('Nothing yet. Conversations appear here as soon as someone messages your connected number.') }}
              </p>
            @else
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead>
                    <tr>
                      <th>{{ __('Contact') }}</th>
                      <th>{{ __('Status') }}</th>
                      <th>{{ __('Unread') }}</th>
                      <th>{{ __('Last message') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($recentConversations as $conversation)
                      <tr>
                        <td>{{ $conversation->contact?->displayName() ?? '—' }}</td>
                        <td><span class="badge badge-light">{{ $conversation->status }}</span></td>
                        <td>{{ $conversation->unread_count }}</td>
                        <td>{{ $conversation->last_message_at?->diffForHumans() ?? '—' }}</td>
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
