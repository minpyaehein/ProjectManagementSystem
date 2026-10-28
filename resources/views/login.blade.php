    @extends('layouts.login_master')

    @section('content')
        <main class="login-page">
            <section class="login-art">
                <a class="brand" href="login.html"><img class="logo" src={{ asset('image/logo.png') }}  alt=""></a>
                <div class="login-copy">
                    <div class="eyebrow" style="color: #b8d8c3">
                        Projects management, simplified
                    </div>
                    <h1>Better projects begin with better decisions.</h1>
                    <p>
                        Plan projects, coordinate teams, manage clients and suppliers, and monitor organizational activities from one centralized workspace.
                    </p>
                </div>
                <div class="login-foot">
                    PROJECTS MANAGEMENT SYSTEM
                </div>
            </section>
            <section class="login-side">
                <form class="login-box" action="{{route('login-form')}}" method="POST">
                    @csrf
                    <div class="eyebrow">Welcome back</div>
                    <h2>Sign in to Projects Management System</h2>
                    <p>
                        Enter your account details to access the management
                        workspace.
                    </p>
                    <div class="field">
                        <label for="name">Username</label><input id="name" name="name" type="text"
                            placeholder="Enter your username" required />
                    </div>
                    <div class="field">
                        <label for="password">Password</label><input id="password" name="password" type="password"
                            placeholder="Enter your password" required />
                    </div>
                    <button class="btn btn-primary" type="submit">
                        Sign in to dashboard →
                    </button>
                </form>
            </section>
        </main>
    @endsection
