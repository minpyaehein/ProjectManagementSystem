<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Golden Pride')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>

<body>
    <div class="app">
        <aside class="sidebar">
            <a class="brand" href="{{ route('homePage') }}">
                <img class="nav_logo" src="{{ asset('image/logo.png') }}" alt="Golden Pride">
            </a>

            @auth
                @php($currentUser = auth()->user())
                <div class="nav-label">Projects Management</div>

                <nav class="nav" aria-label="Main navigation">
                    <a class="{{ request()->routeIs('homePage') ? 'active' : '' }}" href="{{ route('homePage') }}">
                        <span class="nav-icon"></span><span>Dashboard</span>
                    </a>

                    @if ($currentUser->hasAnyPermission(['projects.view', 'projects.create', 'projects.edit', 'projects.deactivate']))
                        <a class="{{ request()->routeIs('projectsPage', 'projects.*', 'project-create') ? 'active' : '' }}"
                            href="{{ route('projectsPage') }}">
                            <span class="nav-icon"></span><span>Projects</span>
                        </a>
                    @endif

                    @if ($currentUser->hasAnyPermission(['customers.view', 'customers.create', 'customers.edit', 'customers.deactivate']))
                        <a class="{{ request()->routeIs('customersPage', 'customers.*') ? 'active' : '' }}"
                            href="{{ route('customersPage') }}">
                            <span class="nav-icon"></span><span>Customers</span>
                        </a>
                    @endif

                    @if ($currentUser->hasAnyPermission(['suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.deactivate']))
                        <a class="{{ request()->routeIs('suppliersPage', 'suppliers.*') ? 'active' : '' }}"
                            href="{{ route('suppliersPage') }}">
                            <span class="nav-icon"></span><span>Suppliers</span>
                        </a>
                    @endif

                    @if ($currentUser->hasAnyPermission(['users.view', 'users.create', 'users.edit', 'users.deactivate']))
                        <a class="{{ request()->routeIs('usersPage', 'users.*') ? 'active' : '' }}"
                            href="{{ route('usersPage') }}">
                            <span class="nav-icon"></span><span>User Management</span>
                        </a>
                    @endif

                    @if ($currentUser->hasAnyPermission(['roles.view', 'roles.create', 'roles.edit', 'roles.deactivate']))
                        <a class="{{ request()->routeIs('rolesPage', 'roles.*') ? 'active' : '' }}"
                            href="{{ route('rolesPage') }}">
                            <span class="nav-icon"></span><span>Roles &amp; Permissions</span>
                        </a>
                    @endif
                </nav>

                <div class="sidebar-bottom">
                    <nav class="nav" aria-label="Account navigation">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="logout-btn" type="submit">
                                <span class="nav-icon">↪</span><span>Logout</span>
                            </button>
                        </form>
                    </nav>
                </div>
            @endauth
        </aside>

        <main class="main">
            <header class="topbar">
                <div class="crumb">@yield('page-name', 'PROJECTS MANAGEMENT SYSTEM')</div>
                @auth
                    <div class="top-right">
                        <a class="top-profile-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}"
                            href="{{ route('profile.edit') }}" aria-label="Open my profile" title="Open my profile">
                            <span class="userline">
                                <strong>
                                    {{ auth()->user()->name }}
                                </strong>

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
</body>

</html>
