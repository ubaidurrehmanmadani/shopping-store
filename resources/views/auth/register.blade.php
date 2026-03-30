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
            <div class="actions-row" style="align-items: end;">
                <label style="flex: 0 0 220px;">
                    Country code
                    <select name="phone_country_code" required>
                        @foreach ($phoneCountryCodes as $code => $label)
                            <option value="{{ $code }}" @selected(old('phone_country_code', '+44') === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label style="flex: 1 1 260px;">
                    Mobile number
                    <input type="tel" name="phone_number" value="{{ old('phone_number') }}" placeholder="7123 456789" required>
                </label>
            </div>
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

        <p style="margin-top: 18px;">
            Already have an account?
            <a href="{{ route('login') }}" style="color: var(--brand-dark); font-weight: 700;">Login here</a>
            and continue to checkout.
        </p>
    </div>
@endsection
