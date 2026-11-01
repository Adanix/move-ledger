<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Move;
use App\Models\MoveShare;
use App\Services\MoveLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MoveShareController extends Controller
{
    public function __construct(
        protected MoveLifecycleService $lifecycleService
    ) {}

    public function store(Request $request, Move $move): RedirectResponse
    {
        $this->authorize('share', $move);

        $validated = $request->validate([
            'pin' => 'nullable|string|min:4|max:6',
            'expires_days' => 'nullable|integer|min:1|max:90',
            'allow_sign' => 'nullable|boolean',
        ]);

        $share = $this->lifecycleService->createShareLink(
            move: $move,
            pin: ! empty($validated['pin']) ? $validated['pin'] : null,
            expireDays: (int) ($validated['expires_days'] ?? 14),
            allowSign: $request->boolean('allow_sign', true),
            actor: $request->user()
        );

        return redirect()->route('moves.show', $move)
            ->with('success', 'Customer share link generated successfully.');
    }

    public function destroy(Move $move, MoveShare $share): RedirectResponse
    {
        $this->authorize('share', $move);

        $share->update(['revoked_at' => now()]);

        AuditLog::log(
            eventType: 'share.revoked',
            description: 'Customer share link revoked immediately.',
            move: $move,
            user: auth()->user()
        );

        return redirect()->route('moves.show', $move)
            ->with('success', 'Customer share link has been revoked.');
    }
}
