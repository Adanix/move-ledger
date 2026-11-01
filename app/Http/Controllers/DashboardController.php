<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MoveStatus;
use App\Models\Move;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Move::with(['customer', 'assignedSurveyor']);

        if ($user->isSurveyor()) {
            $query->where('assigned_surveyor_id', $user->id);
        }

        $allMovesCount = (clone $query)->count();
        $inSurveyCount = (clone $query)->where('status', MoveStatus::InSurvey)->count();
        $pendingSignoffCount = (clone $query)->where('status', MoveStatus::PendingSignoff)->count();
        $inTransitCount = (clone $query)->where('status', MoveStatus::InTransit)->count();
        $deliveredCount = (clone $query)->where('status', MoveStatus::Delivered)->count();

        $todayDate = now()->toDateString();
        $todayMoves = (clone $query)
            ->whereDate('scheduled_move_date', $todayDate)
            ->orderBy('scheduled_move_date')
            ->get();

        $recentMoves = (clone $query)
            ->latest('updated_at')
            ->take(8)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'kpis' => [
                'total' => $allMovesCount,
                'in_survey' => $inSurveyCount,
                'pending_signoff' => $pendingSignoffCount,
                'in_transit' => $inTransitCount,
                'delivered' => $deliveredCount,
            ],
            'todayMoves' => $todayMoves,
            'recentMoves' => $recentMoves,
        ]);
    }
}
