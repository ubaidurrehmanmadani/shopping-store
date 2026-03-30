@extends('layouts.store')

@section('content')
    <div class="panel auth-card">
        <div class="eyebrow">Verify email</div>
        <h1 style="font-size: 2.6rem;">Check your inbox</h1>
        <p>Your account is created, but you need to verify your email address before checkout. We have sent a verification link to <strong>{{ auth()->user()->email }}</strong>.</p>

        <div class="stack" style="margin-top: 24px;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="button">Resend verification email</button>
            </form>

            <a href="{{ route('store.cart.index') }}" class="button secondary">Back to cart</a>
        </div>
    </div>
@endsection
