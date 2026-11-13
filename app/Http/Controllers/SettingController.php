<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Tampilkan form pengaturan profil toko dan struk
     */
    public function index(): View
    {
        $settings = Setting::allKeyed();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Simpan pembaruan pengaturan toko
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:100'],
            'store_tagline' => ['nullable', 'string', 'max:150'],
            'store_address' => ['required', 'string', 'max:255'],
            'store_phone' => ['required', 'string', 'max:50'],
            'receipt_footer' => ['nullable', 'string', 'max:500'],
            'receipt_paper_size' => ['required', 'in:58mm,80mm'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, str_starts_with($key, 'receipt_') ? 'receipt' : 'store');
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan toko dan struk berhasil diperbarui.');
    }
}
