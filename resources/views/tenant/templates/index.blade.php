@extends('layouts.admin', ['activePage' => 'templates', 'titlePage' => __('Templates')])

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
              <h4 class="card-title mb-0">{{ __('Message templates') }}</h4>
              @if ($canSync)
                <form action="{{ route('tenant.templates.sync') }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-primary">{{ __('Sync from Meta') }}</button>
                </form>
              @endif
            </div>

            <p class="text-muted small">
              {{ __('Templates are created and approved inside your Meta WhatsApp Manager. This list mirrors them so campaigns and flows can be built against approved templates — sync to pull the latest.') }}
            </p>

            @if ($templates->isEmpty())
              <p class="text-muted mb-0">
                {{ __('No templates yet. Approve templates in Meta, then sync them here.') }}
              </p>
            @else
              <div class="table-responsive">
                <table class="table table-striped mb-0">
                  <thead>
                    <tr>
                      <th>{{ __('Name') }}</th>
                      <th>{{ __('Language') }}</th>
                      <th>{{ __('Category') }}</th>
                      <th>{{ __('Status') }}</th>
                      <th>{{ __('Variables') }}</th>
                      <th>{{ __('Body') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach ($templates as $template)
                    <tr>
                      <td><code>{{ $template->name }}</code></td>
                      <td>{{ $template->language }}</td>
                      <td>{{ $template->category ?? '—' }}</td>
                      <td>
                        <span class="badge badge-{{ $template->isApproved() ? 'success' : 'warning' }}">
                          {{ $template->status }}
                        </span>
                        @if ($template->rejection_reason)
                          <div class="text-danger small">{{ $template->rejection_reason }}</div>
                        @endif
                      </td>
                      <td>{{ $template->variableCount() }}</td>
                      <td class="small text-muted">{{ \Illuminate\Support\Str::limit($template->body, 80) }}</td>
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
