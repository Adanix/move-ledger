<?php

declare(strict_types=1);

namespace App\Http\Controllers\Demo;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DemoLoginController extends Controller
{
    /**
     * Display the button-based demo persona selector.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (! config('app.demo_mode', true)) {
            abort(404, 'Demo mode is not enabled.');
        }

        $personas = [
            [
                'key' => 'admin',
                'name' => 'Administrator',
                'badge' => 'Executive & Ops Controller',
                'description' => 'Full administrative authority. Oversee global metrics, configure system defaults, manage staff team accounts, inspect audit trails, and authorize cryptographic manifest unlocks.',
                'highlights' => ['Staff Accounts & Roles', 'Manifest Revision Unlocks', 'Global Audit Trail Logs'],
                'button_text' => 'Continue as Administrator',
            ],
            [
                'key' => 'coordinator',
                'name' => 'Move Coordinator',
                'badge' => 'Relocation Dispatcher',
                'description' => 'Central relocation logistics. Register customer shippers, create and dispatch move jobs, configure property room templates, generate PIN-protected customer share links, and monitor move milestones.',
                'highlights' => ['Move Job Dispatch', 'Customer Directory', 'PIN-Guarded Share Links'],
                'button_text' => 'Continue as Move Coordinator',
            ],
            [
                'key' => 'surveyor',
                'name' => 'Field Surveyor & Driver',
                'badge' => 'Mobile Field Crew',
                'description' => 'Mobile-first on-site inventory capturing. Fast 1-tap room surveys, automated volume & weight calculation, photo documentation of pre-existing wear, on-device digital signatures, and destination delivery check-off.',
                'highlights' => ['1-Tap Room Inventory', 'Pre-Existing Damage Photos', 'Digital Signature Pad'],
                'button_text' => 'Continue as Field Surveyor',
            ],
        ];

        return view('demo.login', [
            'personas' => $personas,
        ]);
    }

    /**
     * Authenticate an allowlisted demo persona via 1-click button submission.
     */
    public function quickLogin(Request $request): RedirectResponse
    {
        if (! config('app.demo_mode', true)) {
            abort(404, 'Demo mode is not enabled.');
        }

        $validated = $request->validate([
            'persona' => ['required', 'string', 'in:admin,coordinator,surveyor'],
        ]);

        $role = match ($validated['persona']) {
            'admin' => UserRole::Admin,
            'coordinator' => UserRole::Coordinator,
            'surveyor' => UserRole::Surveyor,
        };

        $user = User::where('role', $role)
            ->where('is_active', true)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'persona' => 'No active demo user found for the requested role.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', "Signed in as demo persona: {$user->name} ({$user->role->label()}).");
    }
}
