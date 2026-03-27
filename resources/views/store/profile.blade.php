@extends('layouts.store')

@section('content')
    <section class="section">
        <div class="section-head">
            <div class="stack">
                <div class="eyebrow">Profile</div>
                <h2>Your saved contact and address details</h2>
            </div>
        </div>

        <div class="panel">
            <form method="POST" action="{{ route('store.profile.update') }}" class="stack">
                @csrf
                @method('PUT')
                <label>
                    Full name
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </label>
                <label>
                    Phone
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                </label>
                <label>
                    Street / address line
                    <input type="text" name="address_line" value="{{ old('address_line', $user->address_line) }}">
                </label>
                <label>
                    City
                    <input type="text" name="city" value="{{ old('city', $user->city) }}">
                </label>
                <label>
                    Area / state
                    <input type="text" name="area" value="{{ old('area', $user->area) }}">
                </label>
                <label>
                    Postal code
                    <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
                </label>
                <button type="submit" class="button">Save profile</button>
            </form>
        </div>
    </section>
@endsection
