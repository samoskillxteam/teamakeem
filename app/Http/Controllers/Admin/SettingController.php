<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function profile(): Response
    {
        $user = auth()->user();
        return Inertia::render('Admin/Profile', [
            'adminEmail' => $user?->email,
            'adminName'  => $user?->name,
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password'          => ['required', 'current_password'],
            'new_password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'new_password_confirmation' => ['required'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateDetails(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
        ]);

        $user->name  = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function index(): Response
    {
        $settings = Setting::orderBy('group')->orderBy('id')->get();
        $admin    = auth()->user();

        return Inertia::render('Admin/Settings/Index', [
            'settings'   => $settings,
            'adminEmail' => $admin?->email,
            'adminName'  => $admin?->name,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'new_password'              => ['nullable', 'confirmed', Password::min(8)],
            'new_password_confirmation' => ['nullable'],
            'logo'                      => ['nullable', 'image', 'max:4096'],
            'logo_dark'                 => ['nullable', 'image', 'max:4096'],
            'hero_desktop_light'        => ['nullable', 'image', 'max:8192'],
            'hero_desktop_dark'         => ['nullable', 'image', 'max:8192'],
            'hero_mobile_light'         => ['nullable', 'image', 'max:4096'],
            'hero_mobile_dark'          => ['nullable', 'image', 'max:4096'],
            'spa_flyer'                 => ['nullable', 'image', 'max:8192'],
        ]);

        // --- Text / textarea settings ---
        $data = $request->input('settings', []);
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : (string) $value]
            );
        }

        // --- Image uploads: logo ---
        $this->storeImage($request, 'logo',       'branding/logo.png',            'logo_path',            'images/logo.png');
        $this->storeImage($request, 'logo_dark',  'branding/logo_dark.png',       'logo_dark_path',       'images/logo_dark.png');

        // --- Hero images (stored in public disk → public/storage/branding/) ---
        $this->storePublicHero($request, 'hero_desktop_light', 'branding/hero_desktop_light.png', 'hero_desktop_light_path');
        $this->storePublicHero($request, 'hero_desktop_dark',  'branding/hero_desktop_dark.png',  'hero_desktop_dark_path');
        $this->storePublicHero($request, 'hero_mobile_light',  'branding/hero_mobile_light.png',  'hero_mobile_light_path');
        $this->storePublicHero($request, 'hero_mobile_dark',   'branding/hero_mobile_dark.png',   'hero_mobile_dark_path');
        $this->storePublicHero($request, 'spa_flyer',          'branding/spa_flyer.jpg',           'spa_flyer_path');

        // --- Admin account ---
        $user = auth()->user();
        if ($user) {
            $newEmail = trim($request->input('admin_email', ''));
            $newName  = trim($request->input('admin_name', ''));
            if ($newEmail && $newEmail !== $user->email) {
                $user->email = $newEmail;
            }
            if ($newName && $newName !== $user->name) {
                $user->name = $newName;
            }
            if ($request->filled('new_password')) {
                $user->password = Hash::make($request->input('new_password'));
            }
            $user->save();
        }

        Cache::forget('app_settings');

        return back()->with('success', 'Settings saved successfully.');
    }

    /** Store an uploaded image to the public disk and update the given setting key. */
    private function storePublicHero(Request $request, string $field, string $storagePath, string $settingKey): void
    {
        if (!$request->hasFile($field)) {
            return;
        }
        Storage::disk('public')->putFileAs(
            dirname($storagePath),
            $request->file($field),
            basename($storagePath)
        );
        Setting::updateOrCreate(
            ['key' => $settingKey],
            ['value' => 'storage/' . $storagePath]
        );
    }

    /** Store logo to the public/images directory (not in storage, served directly). */
    private function storeImage(Request $request, string $field, string $publicRelPath, string $settingKey, string $fallback): void
    {
        if (!$request->hasFile($field)) {
            return;
        }
        $dest = public_path($publicRelPath);
        if (!is_dir(dirname($dest))) {
            mkdir(dirname($dest), 0755, true);
        }
        $request->file($field)->move(dirname($dest), basename($dest));
        Setting::updateOrCreate(
            ['key' => $settingKey],
            ['value' => $publicRelPath]
        );
    }
}
