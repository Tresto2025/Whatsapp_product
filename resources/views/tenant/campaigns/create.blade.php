@extends('layouts.admin', ['activePage' => 'campaigns', 'titlePage' => __('New campaign')])

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

        @if ($templates->isEmpty())
          <div class="alert alert-warning">
            {{ __('No approved templates yet. Sync your templates from Meta before creating a campaign.') }}
            <a href="{{ route('tenant.templates.index') }}" class="alert-link">{{ __('Go to templates') }}</a>.
          </div>
        @else
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">{{ __('New campaign') }}</h4>

            <form action="{{ route('tenant.campaigns.store') }}" method="POST">
              @csrf

              <div class="form-group">
                <label for="name">{{ __('Campaign name') }}</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
              </div>

              <div class="form-group">
                <label for="template">{{ __('Template') }}</label>
                <select name="template_id" id="template" class="form-control" required>
                  <option value="">{{ __('Choose an approved template…') }}</option>
                  @foreach ($templates as $template)
                    <option value="{{ $template->id }}" {{ (string) old('template_id') === (string) $template->id ? 'selected' : '' }}>
                      {{ $template->name }} ({{ $template->language }}) — {{ $template->category }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div id="variables"></div>

              <div class="form-group">
                <label>{{ __('Send to') }}</label>
                <div>
                  <label class="d-inline" style="font-weight: 400;">
                    <input type="radio" name="segment_type" value="all" {{ old('segment_type', 'all') === 'all' ? 'checked' : '' }}>
                    {{ __('All contacts') }}
                  </label>
                </div>
                <div>
                  <label class="d-inline" style="font-weight: 400;">
                    <input type="radio" name="segment_type" value="tags" {{ old('segment_type') === 'tags' ? 'checked' : '' }}>
                    {{ __('Contacts with any of these tags') }}
                  </label>
                </div>
                @if ($tags->isNotEmpty())
                  <div class="mt-2" style="padding-left: 18px;">
                    @foreach ($tags as $tag)
                      <label class="d-inline mb-2" style="font-weight: 400; margin-right: 12px;">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                          {{ collect(old('tags', []))->contains($tag->id) ? 'checked' : '' }}>
                        {{ $tag->name }}
                      </label>
                    @endforeach
                  </div>
                @else
                  <p class="text-muted small mt-1">{{ __('No tags defined yet — tag-based segments will be empty.') }}</p>
                @endif
              </div>

              <div class="alert alert-info small">
                {{ __('Marketing templates skip contacts who have opted out. Meta bills conversations to your own WhatsApp account.') }}
              </div>

              <button type="submit" class="btn btn-primary">{{ __('Create & send') }}</button>
              <a href="{{ route('tenant.campaigns.index') }}" class="btn">{{ __('Cancel') }}</a>
            </form>
          </div>
        </div>
        @endif

      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    var templateVars = @json($templates->mapWithKeys(fn ($t) => [$t->id => $t->variables ?? []]));
    var fieldOptions = [
      ['name', 'Contact name'],
      ['profile_name', 'WhatsApp profile name'],
      ['wa_id', 'Phone number'],
      ['email', 'Email'],
      ['static', 'Custom text…'],
    ];

    var select = document.getElementById('template');
    var container = document.getElementById('variables');
    if (!select || !container) return;

    function render() {
      container.innerHTML = '';
      var vars = templateVars[select.value] || [];
      if (!vars.length) return;

      var heading = document.createElement('p');
      heading.className = 'text-muted small mb-2';
      heading.textContent = 'Fill each template placeholder:';
      container.appendChild(heading);

      vars.forEach(function (position) {
        var group = document.createElement('div');
        group.className = 'form-group';

        var label = document.createElement('label');
        label.textContent = 'Placeholder {{' + position + '}}';
        group.appendChild(label);

        var sel = document.createElement('select');
        sel.className = 'form-control';
        sel.name = 'variable_map[' + position + '][mode]';
        fieldOptions.forEach(function (opt) {
          var o = document.createElement('option');
          o.value = opt[0];
          o.textContent = opt[1];
          sel.appendChild(o);
        });
        group.appendChild(sel);

        var text = document.createElement('input');
        text.type = 'text';
        text.className = 'form-control mt-2';
        text.name = 'variable_map[' + position + '][value]';
        text.placeholder = 'Custom text';
        text.style.display = 'none';
        group.appendChild(text);

        sel.addEventListener('change', function () {
          text.style.display = sel.value === 'static' ? 'block' : 'none';
        });

        container.appendChild(group);
      });
    }

    select.addEventListener('change', render);
    render();
  })();
</script>
@endsection
