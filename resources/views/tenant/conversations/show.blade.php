@extends('layouts.admin', ['activePage' => 'inbox', 'titlePage' => __('Conversation')])

@section('content')
<style>
  .thread { display: flex; flex-direction: column; gap: 8px; max-height: 60vh; overflow-y: auto;
            padding: 12px; background: #eceff1; border-radius: 10px; }
  .bubble { max-width: 72%; padding: 8px 12px; border-radius: 10px; background: #fff;
            border: 1px solid var(--line); white-space: pre-wrap; word-break: break-word; }
  .bubble.out { align-self: flex-end; background: #d9fdd3; border-color: #c5f0bd; }
  .bubble.failed { background: #fee2e2; border-color: #fecaca; }
  .bubble .meta { font-size: 11px; color: var(--muted); margin-top: 4px; }
</style>
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
              <div>
                <h4 class="card-title mb-0">{{ $conversation->contact?->displayName() ?? '—' }}</h4>
                <div class="text-muted small">
                  {{ $conversation->contact?->wa_id }}
                  &middot; {{ __('status') }}: {{ $conversation->status }}
                </div>
              </div>
              <a href="{{ route('conversations.index') }}" class="btn btn-sm">{{ __('Back to inbox') }}</a>
            </div>

            <div class="thread">
              @forelse ($messages as $message)
                <div class="bubble {{ $message->isInbound() ? 'in' : 'out' }} {{ $message->status === \App\Models\Message::STATUS_FAILED ? 'failed' : '' }}">
                  <div>{{ $message->body ?: '['.$message->type.']' }}</div>
                  <div class="meta">
                    {{ $message->sent_at?->format('d M Y, H:i') ?? '' }}
                    @unless ($message->isInbound())
                      &middot; {{ $message->status }}
                      @if ($message->error)
                        <span class="text-danger">— {{ $message->error }}</span>
                      @endif
                    @endunless
                  </div>
                </div>
              @empty
                <p class="text-muted mb-0">{{ __('No messages in this conversation yet.') }}</p>
              @endforelse
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            @if (! $canSend)
              <div class="alert alert-warning mb-0">
                {{ __('No connected WhatsApp number is available to send from. Connect a number to reply.') }}
              </div>
            @else
              @unless ($withinWindow)
                <div class="alert alert-warning">
                  {{ __('It has been more than 24 hours since the last inbound message. WhatsApp only allows a free-text reply inside that window — outside it, an approved template is required, and this reply may be rejected.') }}
                </div>
              @endunless
              <form action="{{ route('conversations.reply', $conversation) }}" method="POST">
                @csrf
                <div class="form-group">
                  <label for="body">{{ __('Reply') }}</label>
                  <textarea name="body" id="body" rows="3" class="form-control" required maxlength="4096">{{ old('body') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
              </form>
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
