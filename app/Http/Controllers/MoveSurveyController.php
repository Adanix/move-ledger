<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CatalogCategory;
use App\Enums\MoveStatus;
use App\Models\CatalogItem;
use App\Models\Move;
use App\Services\MoveLifecycleService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MoveSurveyController extends Controller
{
    public function __construct(
        protected MoveLifecycleService $lifecycleService
    ) {}

    public function show(Request $request, Move $move): View
    {
        $this->authorize('view', $move);

        // Auto-start survey if currently draft
        if ($move->status === MoveStatus::Draft) {
            $this->lifecycleService->transitionStatus(
                $move,
                MoveStatus::InSurvey,
                'Survey started by field staff',
                $request->user()
            );
        }

        $move->load([
            'customer',
            'assignedSurveyor',
            'rooms.items.photos',
            'activeShare',
            'validOriginSignature',
        ]);

        $catalogItems = CatalogItem::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $selectedRoomId = (int) $request->query('room_id', $move->rooms->first()?->id ?? 0);

        return view('moves.survey', [
            'move' => $move,
            'catalogItems' => $catalogItems,
            'catalogCategories' => CatalogCategory::cases(),
            'selectedRoomId' => $selectedRoomId,
        ]);
    }
}
