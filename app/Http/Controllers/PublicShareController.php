<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Signature\StoreSignatureRequest;
use App\Models\MoveShare;
use App\Services\SignatureVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicShareController extends Controller
{
    public function __construct(
        protected SignatureVerificationService $sigService
    ) {}

    public function show(Request $request, string $token): View|RedirectResponse
    {
        $share = MoveShare::where('token', $token)->first();

        if (! $share || ! $share->isValid()) {
            abort(410, 'This customer inventory link has expired or been revoked.');
        }

        // Check PIN gate
        if ($share->has_pin) {
            $isVerified = $request->session()->get('verified_share_' . $share->id, false);
            if (! $isVerified) {
                return redirect()->route('share.pin', ['token' => $token]);
            }
        }

        // Track access
        $share->increment('access_count');
        $share->update(['last_accessed_at' => now()]);

        $move = $share->move;
        $move->load([
            'customer',
            'rooms.items.photos',
            'validOriginSignature',
            'validDestinationSignature',
        ]);

        return view('share.show', [
            'move' => $move,
            'share' => $share,
        ]);
    }

    public function showPinForm(Request $request, string $token): View
    {
        $share = MoveShare::where('token', $token)->first();

        if (! $share || ! $share->isValid()) {
            abort(410, 'This customer inventory link has expired or been revoked.');
        }

        return view('share.pin', [
            'share' => $share,
            'move' => $share->move,
        ]);
    }

    public function verifyPin(Request $request, string $token): RedirectResponse
    {
        $share = MoveShare::where('token', $token)->first();

        if (! $share || ! $share->isValid()) {
            abort(410, 'This customer inventory link has expired or been revoked.');
        }

        $validated = $request->validate([
            'pin' => 'required|string|min:4|max:6',
        ]);

        if (! $share->verifyPin($validated['pin'])) {
            return redirect()->back()
                ->withErrors(['pin' => 'Incorrect PIN code. Please verify and try again.']);
        }

        $request->session()->put('verified_share_' . $share->id, true);

        return redirect()->route('share.show', ['token' => $token]);
    }

    public function storeSignature(StoreSignatureRequest $request, string $token, string $stage): RedirectResponse
    {
        $share = MoveShare::where('token', $token)->first();

        if (! $share || ! $share->isValid()) {
            abort(410, 'This customer inventory link has expired or been revoked.');
        }

        if (! $share->allow_customer_signoff) {
            abort(403, 'Customer sign-off is disabled on this link.');
        }

        if ($share->has_pin && ! $request->session()->get('verified_share_' . $share->id, false)) {
            abort(403, 'Please unlock the link with your PIN first.');
        }

        if (! in_array($stage, ['origin', 'destination'], true)) {
            abort(404);
        }

        $validated = $request->validated();
        $move = $share->move;

        $this->sigService->storeSignature(
            move: $move,
            stage: $stage,
            signerName: $validated['signer_name'],
            signerRole: $validated['signer_role'],
            signatureDataUri: $validated['signature_data_uri'],
            ip: $request->ip(),
            userAgent: $request->userAgent()
        );

        return redirect()->route('share.show', ['token' => $token])
            ->with('success', 'Thank you! Your signature has been recorded and the inventory manifest is confirmed.');
    }
}
