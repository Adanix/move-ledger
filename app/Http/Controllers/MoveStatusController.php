<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MoveStatus;
use App\Models\Move;
use App\Services\MoveLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MoveStatusController extends Controller
{
    public function __construct(
        protected MoveLifecycleService $lifecycleService
    ) {}

    public function update(Request $request, Move $move): RedirectResponse
    {
        $this->authorize('updateStatus', $move);

        $action = $request->input('action');

        if ($action === 'unlock_for_revision') {
            if (! ($request->user()->isAdmin() || $request->user()->isCoordinator())) {
                abort(403, 'Only Admins and Coordinators can unlock signed manifests.');
            }

            $validated = $request->validate([
                'reason' => 'required|string|min:5|max:500',
            ]);

            $this->lifecycleService->unlockForRevision($move, $validated['reason'], $request->user());

            return redirect()->route('moves.survey', $move)
                ->with('success', 'Manifest unlocked for revision. Previous origin signature invalidated.');
        }

        $validated = $request->validate([
            'status' => 'required|in:in_survey,pending_signoff,in_transit,delivered,cancelled',
            'reason' => 'required_if:status,cancelled|nullable|string|min:5|max:500',
        ]);

        $target = MoveStatus::from($validated['status']);

        $this->lifecycleService->transitionStatus(
            $move,
            $target,
            $validated['reason'] ?? null,
            $request->user()
        );

        if ($target === MoveStatus::InSurvey) {
            return redirect()->route('moves.survey', $move)
                ->with('success', 'Survey started.');
        }

        return redirect()->back()
            ->with('success', "Move status updated to {$target->label()}.");
    }
}
