@extends('layouts.store')

@section('content')
    <div class="panel auth-card">
        <div class="eyebrow">Welcome back</div>
        <h1 style="font-size: 2.6rem;">Sign in to RushBite</h1>
        <div class="stack" style="margin-top: 10px;">
            <p>Temporary seeded test accounts are available below. Remove these details later when you no longer need demo access.</p>
            <div class="panel" style="padding: 16px; background: rgba(255,255,255,0.45); box-shadow: none;">
                <div><strong>Admin:</strong> admin@example.com / password</div>
                <div><strong>User:</strong> test@example.com / password</div>
                <div><strong>User:</strong> customer@example.com / password</div>
            </div>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="stack" style="margin-top: 24px;">
            @csrf
            <label>
                Email
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>
            <label>
                Password
                <input type="password" name="password" required>
            </label>
            <label style="grid-auto-flow: column; justify-content: start; align-items: center;">
                <input type="checkbox" name="remember" value="1" style="width: auto;">
                Keep me signed in
            </label>
            <button type="submit" class="button">Login</button>
        </form>

        <p style="margin-top: 18px;">
            New here?
            <a href="{{ route('register') }}" style="color: var(--brand-dark); font-weight: 700;">Create an account</a>
            to continue to checkout.
        </p>
    </div>
@endsection
