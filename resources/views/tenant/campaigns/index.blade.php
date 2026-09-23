@extends('layouts.admin', ['activePage' => 'campaigns', 'titlePage' => __('Campaigns')])

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
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">{{ __('Campaigns') }}</h4>
              <a href="{{ route('tenant.campaigns.create') }}" class="btn btn-primary">{{ __('New campaign') }}</a>
            </div>

            @if ($campaigns->isEmpty())
              <p class="text-muted mb-0">
                {{ __('No campaigns yet. A campaign sends one approved template to a segment of your contacts.') }}
              </p>
            @else
              <div class="table-responsive">
                <table class="table table-striped mb-0">
                  <thead>
                    <tr>
                      <th>{{ __('Name') }}</th>
                      <th>{{ __('Template') }}</th>
                      <th>{{ __('Status') }}</th>
                      <th>{{ __('Recipients') }}</th>
                      <th>{{ __('Sent / Failed / Skipped') }}</th>
                      <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach ($campaigns as $campaign)
                    @php($counts = $campaign->counts ?? [])
                    <tr>
                      <td>{{ $campaign->name }}</td>
                      <td><code>{{ $campaign->template?->name ?? '—' }}</code></td>
                      <td><span class="badge">{{ $campaign->status }}</span></td>
                      <td>{{ $campaign->recipients_count }}</td>
                      <td>
                        {{ $counts['sent'] ?? 0 }} / {{ $counts['failed'] ?? 0 }} / {{ $counts['skipped'] ?? 0 }}
                      </td>
                      <td class="text-right">
                        <a href="{{ route('tenant.campaigns.show', $campaign) }}" class="btn btn-sm btn-primary">{{ __('View') }}</a>
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
