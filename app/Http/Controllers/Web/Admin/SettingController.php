<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Support\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => AppSetting::pairs(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:2000'],
            'site_currency' => ['required', 'string', 'size:3', \Illuminate\Validation\Rule::in(array_keys(config('currencies')))],
            'logo' => ['nullable', 'image', 'max:5120'],
        ]);

        $settings = AppSetting::pairs();
        $logoPath = $settings->get('site_logo_path');

        if ($request->hasFile('logo')) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            $logoPath = $request->file('logo')->store('settings', 'public');
        }

        AppSetting::storeMany([
            'site_name' => $validated['site_name'],
            'site_tagline' => $validated['site_tagline'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'contact_address' => $validated['contact_address'] ?? null,
            'site_currency' => strtoupper($validated['site_currency']),
            'site_logo_path' => $logoPath,
        ]);

        Currency::flush();

        return redirect()->route('admin.settings.edit')->with('success', 'Brand settings updated.');
    }
}
