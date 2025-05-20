<nav class="sidebar">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-home menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.customers.index') }}">
                <i class="fas fa-users menu-icon"></i>
                <span class="menu-title">Customers</span>
            </a>
        </li>
        @if(Auth::guard('admin')->user()->role->role_name === 'admin' || Auth::guard('admin')->user()->role->permissions->can_add_agent)
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.agents.index') }}">
                <i class="fas fa-user-tie menu-icon"></i>
                <span class="menu-title">Collection Agents</span>
            </a>
        </li>
        @endif
        @if(Auth::guard('admin')->user()->role->role_name === 'admin')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.permissions.index') }}">
                <i class="fas fa-lock menu-icon"></i>
                <span class="menu-title">Permissions</span>
            </a>
        </li>
        @endif
    </ul>

    <!-- Logout Button -->
    <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
        @csrf
        <button type="submit" class="btn btn-danger d-flex align-items-center">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
    </form>
</nav>