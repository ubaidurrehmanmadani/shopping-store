@extends('layouts.store')

@section('content')
    <div class="panel auth-card">
        <div class="eyebrow">Get started</div>
        <h1 style="font-size: 2.6rem;">Create your RushBite account</h1>
        <p>This gives you access to menu ordering, checkout, and order history on the web screens.</p>

        <form method="POST" action="{{ route('register.store') }}" class="stack" style="margin-top: 24px;">
            @csrf
            <label>
                Full name
                <input type="text" name="name" value="{{ old('name') }}" required>
            </label>
            <label>
                Email
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>
            <label>
                Password
                <input type="password" name="password" required>
            </label>
            <label>
                Confirm password
                <input type="password" name="password_confirmation" required>
            </label>
            <button type="submit" class="button">Create account</button>
        </form>
    </div>
@endsection
