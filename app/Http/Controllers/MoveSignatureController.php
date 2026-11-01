<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Signature\StoreSignatureRequest;
use App\Models\Move;
use App\Services\SignatureVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MoveSignatureController extends Controller
{
    public function __construct(
        protected SignatureVerificationService $sigService
    ) {}

    public function create(Request $request, Move $move, string $stage): View
    {
        $this->authorize('captureSignature', $move);

        if (! in_array($stage, ['origin', 'destination'], true)) {
            abort(404);
        }

        $move->load(['customer', 'rooms.items']);

        return view('moves.sign', [
            'move' => $move,
            'stage' => $stage,
        ]);
    }

    public function store(StoreSignatureRequest $request, Move $move, string $stage): RedirectResponse
    {
        $this->authorize('captureSignature', $move);

        if (! in_array($stage, ['origin', 'destination'], true)) {
            abort(404);
        }

        $validated = $request->validated();

        $this->sigService->storeSignature(
            move: $move,
            stage: $stage,
            signerName: $validated['signer_name'],
            signerRole: $validated['signer_role'],
            signatureDataUri: $validated['signature_data_uri'],
            ip: $request->ip(),
            userAgent: $request->userAgent(),
            staffUser: $request->user()
        );

        return redirect()->route('moves.show', $move)
            ->with('success', ucfirst($stage) . ' signature captured successfully. Manifest is now locked.');
    }
}
