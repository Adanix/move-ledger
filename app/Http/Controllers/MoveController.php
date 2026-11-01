<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MoveStatus;
use App\Enums\PropertyType;
use App\Enums\UserRole;
use App\Http\Requests\Move\StoreMoveRequest;
use App\Http\Requests\Move\UpdateMoveRequest;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Move;
use App\Models\User;
use App\Services\MoveLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MoveController extends Controller
{
    public function __construct(
        protected MoveLifecycleService $lifecycleService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Move::class);

        $user = $request->user();
        $search = $request->query('search');
        $status = $request->query('status');
        $surveyorId = $request->query('surveyor_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = Move::with(['customer', 'assignedSurveyor']);

        if ($user->isSurveyor()) {
            $query->where('assigned_surveyor_id', $user->id);
        } elseif ($surveyorId) {
            $query->where('assigned_surveyor_id', $surveyorId);
        }

        if ($status && MoveStatus::tryFrom($status)) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('reference_code', 'like', "%{$search}%")
                    ->orWhere('origin_address', 'like', "%{$search}%")
                    ->orWhere('destination_address', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search): void {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($dateFrom) {
            $query->whereDate('scheduled_move_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('scheduled_move_date', '<=', $dateTo);
        }

        $moves = $query->orderBy('scheduled_move_date', 'desc')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $surveyors = User::where('role', UserRole::Surveyor)->where('is_active', true)->orderBy('name')->get();

        return view('moves.index', [
            'moves' => $moves,
            'surveyors' => $surveyors,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'surveyor_id' => $surveyorId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'statuses' => MoveStatus::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Move::class);

        $customers = Customer::orderBy('name')->get();
        $surveyors = User::where('role', UserRole::Surveyor)->where('is_active', true)->orderBy('name')->get();

        return view('moves.create', [
            'customers' => $customers,
            'surveyors' => $surveyors,
            'propertyTypes' => PropertyType::cases(),
        ]);
    }

    public function store(StoreMoveRequest $request): RedirectResponse
    {
        $this->authorize('create', Move::class);

        $data = $request->validated();

        $move = DB::transaction(function () use ($data, $request): Move {
            // Inline customer creation if needed
            if (empty($data['customer_id'])) {
                $customer = Customer::create([
                    'name' => $data['customer_name'],
                    'email' => $data['customer_email'],
                    'phone' => $data['customer_phone'],
                ]);
                $customerId = $customer->id;
            } else {
                $customerId = $data['customer_id'];
            }

            $refCode = $this->lifecycleService->generateReferenceCode();

            $move = Move::create([
                'reference_code' => $refCode,
                'customer_id' => $customerId,
                'assigned_surveyor_id' => $data['assigned_surveyor_id'],
                'created_by_id' => $request->user()->id,
                'status' => MoveStatus::Draft,
                'property_type' => $data['property_type'],
                'origin_address' => $data['origin_address'],
                'origin_floor_access' => $data['origin_floor_access'] ?? null,
                'destination_address' => $data['destination_address'],
                'destination_floor_access' => $data['destination_floor_access'] ?? null,
                'scheduled_move_date' => $data['scheduled_move_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Auto-seed default rooms
            $this->lifecycleService->seedDefaultRooms($move);

            AuditLog::log(
                eventType: 'move.created',
                description: "Move job {$move->reference_code} created.",
                move: $move,
                user: $request->user()
            );

            return $move;
        });

        return redirect()->route('moves.survey', $move)
            ->with('success', "Move {$move->reference_code} scheduled and default rooms seeded.");
    }

    public function show(Move $move): View
    {
        $this->authorize('view', $move);

        $move->load([
            'customer',
            'assignedSurveyor',
            'createdBy',
            'rooms.items.photos',
            'activeShare',
            'validOriginSignature',
            'validDestinationSignature',
            'auditLogs.user',
        ]);

        return view('moves.show', [
            'move' => $move,
        ]);
    }

    public function edit(Move $move): View
    {
        $this->authorize('update', $move);

        $customers = Customer::orderBy('name')->get();
        $surveyors = User::where('role', UserRole::Surveyor)->where('is_active', true)->orderBy('name')->get();

        return view('moves.edit', [
            'move' => $move,
            'customers' => $customers,
            'surveyors' => $surveyors,
            'propertyTypes' => PropertyType::cases(),
        ]);
    }

    public function update(UpdateMoveRequest $request, Move $move): RedirectResponse
    {
        $this->authorize('update', $move);

        $move->update($request->validated());

        AuditLog::log(
            eventType: 'move.updated',
            description: "Move details updated.",
            move: $move,
            user: $request->user()
        );

        return redirect()->route('moves.show', $move)
            ->with('success', 'Move details updated successfully.');
    }

    public function destroy(Move $move): RedirectResponse
    {
        $this->authorize('delete', $move);

        $refCode = $move->reference_code;
        $move->delete();

        return redirect()->route('moves.index')
            ->with('success', "Move {$refCode} has been deleted.");
    }
}
