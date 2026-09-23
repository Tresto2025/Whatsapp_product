@extends('layouts.admin', ['activePage' => 'inbox', 'titlePage' => __('Inbox')])

@section('content')
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12">

        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
          <div class="card-body">
            <h4 class="card-title">{{ __('Conversations') }}</h4>

            @if ($conversations->isEmpty())
              <p class="text-muted mb-0">
                {{ __('No conversations yet. They appear here as soon as someone messages a connected number.') }}
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
                      <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach ($conversations as $conversation)
                    <tr>
                      <td>
                        <a href="{{ route('conversations.show', $conversation) }}">
                          {{ $conversation->contact?->displayName() ?? '—' }}
                        </a>
                      </td>
                      <td><span class="badge">{{ $conversation->status }}</span></td>
                      <td>
                        @if ($conversation->unread_count > 0)
                          <span class="badge badge-success">{{ $conversation->unread_count }}</span>
                        @else
                          <span class="text-muted">0</span>
                        @endif
                      </td>
                      <td>{{ $conversation->last_message_at?->diffForHumans() ?? '—' }}</td>
                      <td class="text-right">
                        <a href="{{ route('conversations.show', $conversation) }}" class="btn btn-sm btn-primary">{{ __('Open') }}</a>
                      </td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>

              @if ($conversations->hasPages())
                <div class="mt-3 d-flex" style="gap: 8px;">
                  @if ($conversations->onFirstPage())
                    <span class="btn btn-sm" style="opacity: .5;">{{ __('Previous') }}</span>
                  @else
                    <a class="btn btn-sm" href="{{ $conversations->previousPageUrl() }}">{{ __('Previous') }}</a>
                  @endif
                  @if ($conversations->hasMorePages())
                    <a class="btn btn-sm" href="{{ $conversations->nextPageUrl() }}">{{ __('Next') }}</a>
                  @else
                    <span class="btn btn-sm" style="opacity: .5;">{{ __('Next') }}</span>
                  @endif
                </div>
              @endif
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
