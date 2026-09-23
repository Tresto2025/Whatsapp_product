@extends('layouts.admin', ['activePage' => 'campaigns', 'titlePage' => __('Campaign')])

@section('content')
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12">

        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @php($counts = $campaign->counts ?? [])
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h4 class="card-title mb-0">{{ $campaign->name }}</h4>
                <div class="text-muted small">
                  {{ __('Template') }}: <code>{{ $campaign->template?->name }}</code>
                  &middot; {{ __('status') }}: <span class="badge">{{ $campaign->status }}</span>
                </div>
              </div>
              <a href="{{ route('tenant.campaigns.index') }}" class="btn btn-sm">{{ __('Back to campaigns') }}</a>
            </div>

            <div class="row">
              @foreach ([
                ['label' => __('Recipients'), 'value' => $counts['total'] ?? $recipients->total()],
                ['label' => __('Sent'), 'value' => $counts['sent'] ?? 0],
                ['label' => __('Failed'), 'value' => $counts['failed'] ?? 0],
                ['label' => __('Skipped'), 'value' => $counts['skipped'] ?? 0],
              ] as $stat)
                <div class="col-md-3">
                  <div class="card">
                    <div class="card-body">
                      <p class="mb-1 text-muted">{{ $stat['label'] }}</p>
                      <h3 class="mb-0">{{ number_format($stat['value']) }}</h3>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h4 class="card-title">{{ __('Recipients') }}</h4>
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>{{ __('Contact') }}</th>
                    <th>{{ __('Delivery') }}</th>
                    <th>{{ __('Reason') }}</th>
                  </tr>
                </thead>
                <tbody>
                @foreach ($recipients as $recipient)
                  <tr>
                    <td>{{ $recipient->contact?->displayName() ?? '—' }}</td>
                    <td><span class="badge">{{ $recipient->message?->status ?? $recipient->status }}</span></td>
                    <td class="text-danger small">{{ $recipient->failed_reason }}</td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>

            @if ($recipients->hasPages())
              <div class="mt-3 d-flex" style="gap: 8px;">
                @if ($recipients->onFirstPage())
                  <span class="btn btn-sm" style="opacity: .5;">{{ __('Previous') }}</span>
                @else
                  <a class="btn btn-sm" href="{{ $recipients->previousPageUrl() }}">{{ __('Previous') }}</a>
                @endif
                @if ($recipients->hasMorePages())
                  <a class="btn btn-sm" href="{{ $recipients->nextPageUrl() }}">{{ __('Next') }}</a>
                @else
                  <span class="btn btn-sm" style="opacity: .5;">{{ __('Next') }}</span>
                @endif
              </div>
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
