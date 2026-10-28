<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        @yield('title', 'Golden Pride')
    </title>

    {{ asset('css/style.css') }}
</head>

<body>
    <div class="app">
        <aside class="sidebar">
             }}">
                <span>
                     }}"
                        alt="Golden Pride"
                    >
                </span>
            </a>

            @auth
                <div class="nav-label">
                    Workspace
                </div>

                <nav class="nav">
                     }}"
                    >
                        <span class="nav-icon">▦</span>
                        <span>Dashboard</span>
                    </a>

                    @if (auth()->user()->hasPermission('projects.manage'))
                         }}"
                        >
                            <span class="nav-icon">▣</span>
                            <span>Projects</span>
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('customers.manage'))
                         }}"
                        >
                            <span class="nav-icon">♧</span>
                            <span>Customers</span>
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('suppliers.manage'))
                         }}"
                        >
                            <span class="nav-icon">◇</span>
                            <span>Suppliers</span>
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('users.manage'))
                         }}"
                        >
                            <span class="nav-icon">♙</span>
                            <span>User Management</span>
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('roles.manage'))
                         }}"
                        >
                            <span class="nav-icon">⚙</span>
                            <span>Roles &amp; Permissions</span>
                        </a>
                    @endif

                    @if (auth()->user()->hasPermission('logs.view'))
                         }}"
                        >
                            <span class="nav-icon">◷</span>
                            <span>Activity Logs</span>
                        </a>
                    @endif
                </nav>

                <div class="sidebar-bottom">
                    <nav class="nav">
                         }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                class="logout-btn"
                                type="submit"
                            >
                                <span class="nav-icon">↪</span>
                                <span>Logout</span>
                            </button>
                        </form>
                    </nav>
                </div>
            @endauth
        </aside>

        <main class="main">
            <header class="topbar">
                <div>
                    <div class="crumb">
                        PROJECTS MANAGEMENT SYSTEM
                    </div>
                </div>

                @auth
                    <div class="top-right">
                        <span class="userline">
                            <strong>
                                {{ auth()->user()->name }}
                            </strong>

                            <span style="color: var(--muted)">
                                {{ auth()->user()->role?->name
                                    ?? 'No Role' }}
                            </span>
                        </span>

                        <span
                            class="avatar"
                            title="{{ auth()->user()->name }}"
                        >
                            {{ strtoupper(
                                substr(
                                    auth()->user()->name,
                                    0,
                                    1
                                )
                            ) }}
                        </span>
                    </div>
                @endauth
            </header>

            @yield('content')
        </main>
    </div>
</body>

</html>
