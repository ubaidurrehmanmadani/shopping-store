@extends('layouts.admin')

@php($currencies = config('currencies'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 50px;
            padding: 10px 14px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,0.03);
            color: var(--ink);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--ink);
            line-height: 28px;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 48px;
            right: 10px;
        }

        .select2-dropdown {
            border: 1px solid var(--line);
            background: #132430;
            color: var(--ink);
        }

        .select2-search--dropdown .select2-search__field {
            border-radius: 12px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,0.04);
            color: var(--ink);
        }

        .select2-results__option {
            color: var(--ink);
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: rgba(240, 138, 75, 0.2);
            color: #fff;
        }

        .currency-option {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .currency-flag {
            font-size: 1.1rem;
        }

        .currency-meta {
            color: var(--muted);
            font-size: 0.88rem;
        }
    </style>
@endpush

@section('content')
    <div class="panel">
        <h3>Brand settings</h3>
        <p>Change the company name, logo, public contact details, and the single storefront currency used across products and checkout.</p>

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
                <label>
                    Store currency
                    <select name="site_currency" class="js-currency-select" required>
                        @foreach ($currencies as $code => $currency)
                            <option
                                value="{{ $code }}"
                                data-flag="{{ $currency['flag'] }}"
                                data-name="{{ $currency['name'] }}"
                                @selected(old('site_currency', $settings->get('site_currency', 'USD')) === $code)
                            >
                                {{ $code }} - {{ $currency['name'] }}
                            </option>
                        @endforeach
                    </select>
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

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formatCurrency = function (option) {
                if (!option.id) {
                    return option.text;
                }

                const element = option.element;
                const flag = element.dataset.flag || '';
                const name = element.dataset.name || '';

                return $(
                    '<span class="currency-option">' +
                        '<span class="currency-flag">' + flag + '</span>' +
                        '<span>' +
                            '<strong>' + option.id + '</strong> ' +
                            '<span class="currency-meta">' + name + '</span>' +
                        '</span>' +
                    '</span>'
                );
            };

            $('.js-currency-select').select2({
                placeholder: 'Select a currency',
                templateResult: formatCurrency,
                templateSelection: formatCurrency,
                width: '100%'
            });
        });
    </script>
@endpush
