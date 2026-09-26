<nav class="sidebar">
    <div class="brand">{{ config('app.name') }}</div>
    <ul class="nav">
        <li class="nav-item {{ ($activePage ?? '') == 'dashboard' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
        </li>

        <li class="nav-item {{ ($activePage ?? '') == 'inbox' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('conversations.index') }}">Inbox</a>
        </li>

        @if(Auth::user()->administersWorkspace())
        <li class="nav-item {{ ($activePage ?? '') == 'campaigns' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('tenant.campaigns.index') }}">Campaigns</a>
        </li>
        <li class="nav-item {{ ($activePage ?? '') == 'templates' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('tenant.templates.index') }}">Templates</a>
        </li>
        <li class="nav-item {{ ($activePage ?? '') == 'chatbot' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('tenant.chatbot.index') }}">Chatbot</a>
        </li>
        <li class="nav-item {{ ($activePage ?? '') == 'whatsapp-connection' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('tenant.whatsapp.index') }}">WhatsApp Connection</a>
        </li>
        @endif

        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a class="nav-link" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); this.closest('form').submit();">Log Out</a>
            </form>
        </li>
    </ul>
</nav>
