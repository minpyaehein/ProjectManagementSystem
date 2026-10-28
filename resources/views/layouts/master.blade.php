<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Projects Management System | Golden Pride')</title>

    <link rel="icon" href="{{ asset('image/logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-navbar.css') }}">

    @stack('styles')
</head>
<body>
    <div class="app">
        <aside
            class="sidebar"
            id="sidebar"
            aria-label="Primary navigation"
        >
            <a class="brand" href="{{ route('homePage') }}">
                <img
                    class="nav_logo"
                    src="{{ asset('image/logo.png') }}"
                    alt="Golden Pride"
                >

                <span class="title">PROJECTS MANAGEMENT SYSTEM</span>
            </a>

            <nav class="nav" aria-label="Main navigation">
                <div class="nav-label">Workspace</div>

                <a
                    class="{{ request()->routeIs('homePage') ? 'active' : '' }}"
                    href="{{ route('homePage') }}"
                >
                    <span class="nav-icon" aria-hidden="true">⌂</span>
                    <span>Dashboard</span>
                </a>

                @if (auth()->user()->hasPermission('projects.view'))
                    <a
                        class="{{ request()->routeIs('projectsPage', 'projects.*', 'project-create') ? 'active' : '' }}"
                        href="{{ route('projectsPage') }}"
                    >
                        <span class="nav-icon" aria-hidden="true">▣</span>
                        <span>Projects</span>
                    </a>
                @endif

                @if (auth()->user()->hasPermission('customers.view'))
                    <a
                        class="{{ request()->routeIs('customersPage', 'customers.*') ? 'active' : '' }}"
                        href="{{ route('customersPage') }}"
                    >
                        <span class="nav-icon" aria-hidden="true">♧</span>
                        <span>Customers</span>
                    </a>
                @endif

                @if (auth()->user()->hasPermission('suppliers.view'))
                    <a
                        class="{{ request()->routeIs('suppliersPage', 'suppliers.*') ? 'active' : '' }}"
                        href="{{ route('suppliersPage') }}"
                    >
                        <span class="nav-icon" aria-hidden="true">◇</span>
                        <span>Suppliers</span>
                    </a>
                @endif

                <div class="nav-label">Administration</div>

                @if (auth()->user()->hasPermission('users.view'))
                    <a
                        class="{{ request()->routeIs('usersPage', 'users.*') ? 'active' : '' }}"
                        href="{{ route('usersPage') }}"
                    >
                        <span class="nav-icon" aria-hidden="true">♙</span>
                        <span>Users</span>
                    </a>
                @endif

                @if (auth()->user()->hasPermission('roles.view'))
                    <a
                        class="{{ request()->routeIs('rolesPage', 'roles.*') ? 'active' : '' }}"
                        href="{{ route('rolesPage') }}"
                    >
                        <span class="nav-icon" aria-hidden="true">◆</span>
                        <span>Roles &amp; Permissions</span>
                    </a>
                @endif
            </nav>

            <div class="sidebar-bottom">
                <nav class="nav" aria-label="Account navigation">
                    <a
                        class="{{ request()->routeIs('profile.*') ? 'active' : '' }}"
                        href="{{ route('profile.edit') }}"
                    >
                        <span class="nav-icon" aria-hidden="true">●</span>
                        <span>My Profile</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf

                        <button class="logout-btn" type="submit">
                            <span class="nav-icon" aria-hidden="true">↪</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </nav>
            </div>
        </aside>

        <button
            class="sidebar-overlay"
            id="sidebarOverlay"
            type="button"
            aria-label="Close navigation"
            aria-hidden="true"
            tabindex="-1"
        ></button>

        <main class="main">
            <header class="topbar">
                <div class="topbar-left">
                    <button
                        class="mobile-menu-button"
                        id="mobileMenuButton"
                        type="button"
                        aria-controls="sidebar"
                        aria-expanded="false"
                        aria-label="Open navigation"
                    >
                        <span aria-hidden="true">☰</span>
                    </button>

                    <div class="crumb">
                        @yield('page-name', 'Dashboard')
                    </div>
                </div>

                @auth
                    <div class="top-right">
                        <a
                            class="top-profile-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}"
                            href="{{ route('profile.edit') }}"
                            aria-label="Open my profile"
                        >
                            <span class="userline">
                                <strong>{{ auth()->user()->name }}</strong>

                                <span class="top-profile-role">
                                    {{ auth()->user()->role?->name ?? 'No Role' }}
                                </span>
                            </span>

                            <span class="avatar" aria-hidden="true">
                                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </a>
                    </div>
                @endauth
            </header>

            @yield('content')
        </main>
    </div>

    @stack('scripts')

    <script src="{{ asset('js/responsive-navbar.js') }}" defer></script>
</body>
</html>
