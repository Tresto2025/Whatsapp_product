{{-- Step-by-step for a tenant who has never set up a Meta app before. --}}
<div class="card mb-4">
  <div class="card-body">
    <h4 class="card-title">{{ __('Where to find your WhatsApp credentials') }}</h4>
    <p class="text-muted">
      {{ __('You need a Meta developer app with the WhatsApp product added. Everything below comes from') }}
      <a href="https://developers.facebook.com/apps" target="_blank" rel="noopener">developers.facebook.com/apps</a>.
    </p>

    <ol class="mb-0">
      <li class="mb-2">
        <strong>{{ __('Create the app') }}</strong> —
        {{ __('My Apps → Create App → choose "Business". Then Add products → WhatsApp → Set up.') }}
      </li>
      <li class="mb-2">
        <strong>{{ __('Phone number ID and WhatsApp Business Account ID') }}</strong> —
        {{ __('WhatsApp → API Setup. Both are shown at the top of the page. The phone number ID is what routes incoming messages to your workspace, so copy it exactly.') }}
      </li>
      <li class="mb-2">
        <strong>{{ __('Permanent access token') }}</strong> —
        {{ __('the token on the API Setup page expires in 24 hours, so do not use it here. Instead go to Business Settings → Users → System Users → Add, create a system user with Admin access, then Add Assets and give it your app and your WhatsApp Business Account. Click Generate new token, select the app, tick') }}
        <code>whatsapp_business_messaging</code> {{ __('and') }} <code>whatsapp_business_management</code>,
        {{ __('and set expiry to Never. Copy the token — Meta shows it only once.') }}
      </li>
      <li class="mb-2">
        <strong>{{ __('App secret') }}</strong> —
        {{ __('App settings → Basic → App secret → Show. This lets us verify that incoming webhooks genuinely came from Meta.') }}
      </li>
      <li class="mb-2">
        <strong>{{ __('Save the form below.') }}</strong>
        {{ __('We call Meta with your credentials to confirm they work before storing them, so a wrong value is caught here rather than failing on your first message.') }}
      </li>
      <li>
        <strong>{{ __('Point Meta at us') }}</strong> —
        {{ __('back in WhatsApp → Configuration → Callback URL, paste the callback URL and the verify token shown on your connection page, click Verify and save, then Manage the webhook fields and subscribe to') }}
        <code>messages</code>.
      </li>
    </ol>
  </div>
</div>
