<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CatalogItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InventoryItemPhotoController;
use App\Http\Controllers\MoveController;
use App\Http\Controllers\MoveDeliveryController;
use App\Http\Controllers\MoveRoomController;
use App\Http\Controllers\MoveShareController;
use App\Http\Controllers\MoveSignatureController;
use App\Http\Controllers\MoveStatusController;
use App\Http\Controllers\MoveSurveyController;
use App\Http\Controllers\Demo\DemoLoginController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicShareController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

// Canonical Public P0 Routes
Route::get('/', [PublicPageController::class, 'home'])->name('public.home');
Route::get('/docs', [PublicPageController::class, 'docs'])->name('public.docs');

// Demo Persona Quick Login Routes
Route::get('/demo/login', [DemoLoginController::class, 'showLogin'])->name('demo.login');
Route::post('/demo/quick-login', [DemoLoginController::class, 'quickLogin'])
    ->middleware('throttle:30,1')
    ->name('demo.quick-login');

// Staff Authentication (Guest)
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

// Authenticated Staff Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Public Customer Share Portal (Token-based)
Route::prefix('share')->group(function (): void {
    Route::get('/{token}', [PublicShareController::class, 'show'])
        ->middleware('throttle:60,1')
        ->name('share.show');

    Route::get('/{token}/pin', [PublicShareController::class, 'showPinForm'])
        ->middleware('throttle:60,1')
        ->name('share.pin');

    Route::post('/{token}/pin', [PublicShareController::class, 'verifyPin'])
        ->middleware('throttle:5,10')
        ->name('share.pin.verify');

    Route::post('/{token}/sign/{stage}', [PublicShareController::class, 'storeSignature'])
        ->middleware('throttle:10,1')
        ->name('share.sign.store');
});

// Authenticated Staff Application Routes
Route::middleware(['auth', 'active_user'])->group(function (): void {
    // Operations & Field Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customers Management
    Route::resource('customers', CustomerController::class)->except(['create', 'edit', 'destroy']);

    // Standard Item Catalog
    Route::resource('catalog', CatalogItemController::class)->except(['create', 'show', 'edit']);

    // Move Jobs Management & Scoped Sub-resources
    Route::resource('moves', MoveController::class);

    Route::scopeBindings()->group(function (): void {
        // Status Transition / Revision Unlock
        Route::patch('/moves/{move}/status', [MoveStatusController::class, 'update'])->name('moves.status.update');

        // Field Survey Workspace (Mobile-First Room & Inventory Builder)
        Route::get('/moves/{move}/survey', [MoveSurveyController::class, 'show'])->name('moves.survey');

        // Room Management within Move
        Route::post('/moves/{move}/rooms', [MoveRoomController::class, 'store'])->name('moves.rooms.store');
        Route::put('/moves/{move}/rooms/{room}', [MoveRoomController::class, 'update'])->name('moves.rooms.update');
        Route::delete('/moves/{move}/rooms/{room}', [MoveRoomController::class, 'destroy'])->name('moves.rooms.destroy');

        // Inventory Items within Move
        Route::post('/moves/{move}/items', [InventoryItemController::class, 'store'])->name('moves.items.store');
        Route::put('/moves/{move}/items/{item}', [InventoryItemController::class, 'update'])->name('moves.items.update');
        Route::patch('/moves/{move}/items/{item}/quantity', [InventoryItemController::class, 'updateQuantity'])->name('moves.items.quantity');
        Route::delete('/moves/{move}/items/{item}', [InventoryItemController::class, 'destroy'])->name('moves.items.destroy');

        // Condition Photos per Item
        Route::post('/moves/{move}/items/{item}/photos', [InventoryItemPhotoController::class, 'store'])->name('moves.items.photos.store');
        Route::delete('/moves/{move}/items/{item}/photos/{photo}', [InventoryItemPhotoController::class, 'destroy'])->name('moves.items.photos.destroy');

        // Destination Delivery Check-Off
        Route::get('/moves/{move}/delivery', [MoveDeliveryController::class, 'show'])->name('moves.delivery');
        Route::patch('/moves/{move}/items/{item}/delivery', [MoveDeliveryController::class, 'updateItem'])->name('moves.items.delivery');
        Route::post('/moves/{move}/delivery/mark-room', [MoveDeliveryController::class, 'markRoomDelivered'])->name('moves.delivery.mark-room');

        // On-Device Tablet/Phone Signature Capture
        Route::get('/moves/{move}/sign/{stage}', [MoveSignatureController::class, 'create'])->name('moves.sign');
        Route::post('/moves/{move}/sign/{stage}', [MoveSignatureController::class, 'store'])->name('moves.sign.store');

        // Customer Share Link Generation & Revocation
        Route::post('/moves/{move}/shares', [MoveShareController::class, 'store'])->name('moves.shares.store');
        Route::delete('/moves/{move}/shares/{share}', [MoveShareController::class, 'destroy'])->name('moves.shares.destroy');
    });

    // Admin Only Staff Management
    Route::middleware('role:admin')->prefix('admin')->group(function (): void {
        Route::resource('users', AdminUserController::class)->except(['create', 'show', 'edit', 'destroy']);
    });
});
