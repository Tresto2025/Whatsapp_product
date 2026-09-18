<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <!--<li class="nav-item user-info">
            <p class="name">Clyde Miles</p>
            <p class="email">clydemiles@elenor.us</p>
        </li>-->
        <li class="nav-item">
            <a class="nav-link {{ $activePage == 'home' ? 'active' : '' }}" href="{{ route('dashboard') }}" >
                <i class="fa fa-home menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        @if(Auth::user()->role == 1)
        <li class="nav-item {{ $activePage == 'all-doctors' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'all-doctors' ? 'active' : '' }}" href="{{ route('doctor.index') }}">
                <i class="mdi mdi-account-multiple  menu-icon"></i>
                <span class="menu-title">Manage Doctors</span>
            </a>
        </li>
        <!--<li class="nav-item {{ $activePage == 'sms-balance' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'sms-balance' ? 'active' : '' }}" href="{{ route('sms.index') }}">
                <i class="mdi mdi-image-filter menu-icon"></i>
                <span class="menu-title">Manage SMS</span>
            </a>
        </li>-->
        <!--<li class="nav-item {{ $activePage == 'message-plans' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'message-plans' ? 'active' : '' }}" href="{{ route('message_plans.index') }}">
                <i class="mdi mdi-package-variant menu-icon"></i>
                <span class="menu-title">Manage Plans</span>
            </a>
        </li>-->
        <li class="nav-item {{ $activePage == 'message-price' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'message-price' ? 'active' : '' }}" href="{{ route('admin.message.price') }}">
                <i class="mdi mdi-package-variant menu-icon"></i>
                <span class="menu-title">Message Price</span>
            </a>
        </li>
        <li class="nav-item {{ $activePage == 'all-services' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'all-services' ? 'active' : '' }}" href="{{ route('doctor.services.index') }}">
                <i class="mdi mdi-package-variant menu-icon"></i>
                <span class="menu-title">Manage Services</span>
            </a>
        </li>
        <li class="nav-item {{ $activePage == 'broadcast-messages' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'broadcast-messages' ? 'active' : '' }}" href="{{ route('broadcast_messages.index') }}">
                <i class="mdi mdi-checkerboard menu-icon"></i>
                <span class="menu-title">Broadcast message</span>
            </a>
        </li>
             <li class="nav-item {{ in_array($activePage, ['blog-posts', 'blog-categories']) ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#blog-menu" aria-expanded="{{ in_array($activePage, ['blog-posts', 'blog-categories']) ? 'true' : 'false' }}" aria-controls="blog-menu">
                <i class="mdi mdi-post menu-icon"></i>
                <span class="menu-title">Blog Management</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ in_array($activePage, ['blog-posts', 'blog-categories']) ? 'show' : '' }}" id="blog-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link {{ $activePage == 'blog-posts' ? 'active' : '' }}" href="{{ route('blog-post.index') }}">All Posts</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('blog-post.create') }}">Add New Post</a></li>
                    <!--<li class="nav-item"> <a class="nav-link" href="{{ route('blog-post.draft') }}">Drafts</a></li>-->
                    <!--<li class="nav-item"> <a class="nav-link" href="{{ route('blog-post.recycle-bin') }}">Recycle Bin</a></li>-->
                    <li class="nav-item"> <a class="nav-link {{ $activePage == 'blog-categories' ? 'active' : '' }}" href="{{ route('blog-category.index') }}">Categories</a></li>
                </ul>
            </div>
        </li>
        @else
        <li class="nav-item {{ $activePage == 'all-doctors' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'all-doctors' ? 'active' : '' }}" href="{{ route('doctor.appointments') }}">
                <i class="mdi mdi-account-multiple  menu-icon"></i>
                <span class="menu-title">Manage Appointments</span>
            </a>
        </li>
        <li class="nav-item {{ $activePage == 'doctor-my-balance' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'doctor-my-balance' ? 'active' : '' }}" href="{{ route('doctor.my.balance') }}">
                <i class="mdi mdi-whatsapp menu-icon"></i>
                <span class="menu-title">Message Balance</span>
            </a>
        </li>
        <li class="nav-item {{ $activePage == 'doctor-broadcast-messages' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'doctor-broadcast-messages' ? 'active' : '' }}" href="{{ route('doctor.broadcast_messages.index') }}">
                <i class="mdi mdi-checkerboard menu-icon"></i>
                <span class="menu-title">Broadcast message</span>
            </a>
        </li>
        <li class="nav-item {{ $activePage == 'doctor-profile' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'doctor-profile' ? 'active' : '' }}" href="{{ route('doctor-my-profile') }}">
                <i class="mdi mdi-account-settings  menu-icon"></i>
                <span class="menu-title">Profile</span>
            </a>
        </li>
        <!--<li class="nav-item {{ $activePage == 'doctor-sms-balance' ? 'active' : '' }}">
            <a class="nav-link {{ $activePage == 'doctor-sms-balance' ? 'active' : '' }}" href="{{ route('doctor.sms-balance') }}">
                <i class="mdi mdi-package-variant menu-icon"></i>
                <span class="menu-title">SMS Plans</span>
            </a>
        </li>-->
        @endif
        <li class="nav-item">
            <a class="nav-link" href="{{ route('user-logout') }}">
                <i class="mdi mdi-logout menu-icon"></i>
                <span class="menu-title">Log Out</span>
            </a>
        </li>
    </ul>
</nav>