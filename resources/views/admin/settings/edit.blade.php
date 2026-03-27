@extends('layouts.admin')

@section('content')
    <div class="panel">
        <h3>Brand settings</h3>
        <p>Change the company name, logo, and public contact details shown across the storefront.</p>

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" style="margin-top: 18px;">
            @csrf
            <div class="form-grid">
                <label>
                    Company / brand name
                    <input type="text" name="site_name" value="{{ old('site_name', $settings->get('site_name', 'RushBite')) }}" required>
                </label>
                <label>
                    Tagline
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings->get('site_tagline')) }}">
                </label>
                <label>
                    Contact phone
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings->get('contact_phone')) }}">
                </label>
                <label>
                    Contact email
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings->get('contact_email')) }}">
                </label>
                <label class="span-2">
                    Contact address
                    <textarea name="contact_address">{{ old('contact_address', $settings->get('contact_address')) }}</textarea>
                </label>
                <label class="span-2">
                    Company logo
                    <input type="file" name="logo" accept="image/*">
                </label>
                @if ($settings->get('site_logo_path'))
                    <div class="span-2">
                        <div class="meta" style="margin-bottom: 8px;">Current logo</div>
                        <img src="{{ '/storage/'.$settings->get('site_logo_path') }}" alt="Current logo" style="width: 140px; height: 140px; object-fit: cover; border-radius: 18px; border: 1px solid var(--line);">
                    </div>
                @endif
            </div>

            <div class="toolbar" style="margin-top: 18px;">
                <button type="submit" class="button">Save settings</button>
            </div>
        </form>
    </div>
@endsection
