@extends('layouts.admin', ['activePage' => 'chatbot', 'titlePage' => __('New auto-reply')])

@section('content')
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12">

        @if ($errors->any())
          <div class="alert alert-danger">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
          </div>
        @endif

        <div class="card">
          <div class="card-body">
            <h4 class="card-title">{{ __('New auto-reply') }}</h4>

            <form action="{{ route('tenant.chatbot.store') }}" method="POST">
              @csrf

              <div class="form-group">
                <label for="name">{{ __('Name') }}</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
              </div>

              <div class="form-group">
                <label for="keyword">{{ __('Trigger keyword') }}</label>
                <input type="text" name="keyword" id="keyword" class="form-control" value="{{ old('keyword') }}"
                       placeholder="{{ __('e.g. hi, menu, book') }}" required>
                <p class="form-text text-muted">
                  {{ __('Fires when an inbound message contains this word (case-insensitive).') }}
                </p>
                <label class="d-inline" style="font-weight: 400;">
                  <input type="checkbox" name="exact_match" value="1" {{ old('exact_match') ? 'checked' : '' }}>
                  {{ __('Match the whole message exactly, not just contains') }}
                </label>
              </div>

              <div class="form-group">
                <label>{{ __('Reply with') }}</label>
                <div>
                  <label class="d-inline" style="font-weight: 400;">
                    <input type="radio" name="reply_type" value="text" id="reply_text"
                           {{ old('reply_type', 'text') === 'text' ? 'checked' : '' }}>
                    {{ __('Plain text') }}
                  </label>
                </div>
                <div>
                  <label class="d-inline" style="font-weight: 400;">
                    <input type="radio" name="reply_type" value="template" id="reply_template"
                           {{ old('reply_type') === 'template' ? 'checked' : '' }}>
                    {{ __('Approved template') }}
                  </label>
                </div>
              </div>

              <div class="form-group" id="text-fields">
                <label for="reply_body">{{ __('Message') }}</label>
                <textarea name="reply_body" id="reply_body" class="form-control" rows="3">{{ old('reply_body') }}</textarea>
              </div>

              <div class="form-group" id="template-fields" style="display: none;">
                <label for="template_id">{{ __('Template') }}</label>
                @if ($templates->isEmpty())
                  <p class="text-muted small">
                    {{ __('No approved templates without placeholders yet.') }}
                    <a href="{{ route('tenant.templates.index') }}">{{ __('Sync from Meta') }}</a>.
                  </p>
                @else
                  <select name="template_id" id="template_id" class="form-control">
                    <option value="">{{ __('Choose an approved template…') }}</option>
                    @foreach ($templates as $template)
                      <option value="{{ $template->id }}" {{ (string) old('template_id') === (string) $template->id ? 'selected' : '' }}>
                        {{ $template->name }} ({{ $template->language }})
                      </option>
                    @endforeach
                  </select>
                @endif
                <p class="form-text text-muted">
                  {{ __('Only templates with no placeholders are listed — auto-replies can\'t fill template variables yet.') }}
                </p>
              </div>

              <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
              <a href="{{ route('tenant.chatbot.index') }}" class="btn">{{ __('Cancel') }}</a>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    var textRadio = document.getElementById('reply_text');
    var templateRadio = document.getElementById('reply_template');
    var textFields = document.getElementById('text-fields');
    var templateFields = document.getElementById('template-fields');
    if (!textRadio || !templateRadio) return;

    function render() {
      textFields.style.display = textRadio.checked ? 'block' : 'none';
      templateFields.style.display = templateRadio.checked ? 'block' : 'none';
    }

    textRadio.addEventListener('change', render);
    templateRadio.addEventListener('change', render);
    render();
  })();
</script>
@endsection
