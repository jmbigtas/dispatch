<?php
use App\Http\Controllers\{AdminDivisionController, AdminDriverController, AdminUserController, AdminVehicleController, AdminVehicleModelController, AuthController, DashboardController, TripTicketController};
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Every role: request and view trips
    Route::get('/trips', [TripTicketController::class, 'index'])->name('trips.index');
    Route::get('/trips/create', [TripTicketController::class, 'create'])->name('trips.create');
    Route::post('/trips', [TripTicketController::class, 'store'])->name('trips.store');

    // Approving authority + admin: review, assign, approve or reject
    Route::middleware('role:approving_authority,admin')->group(function () {
        Route::get('/trips/{trip}/review', [TripTicketController::class, 'review'])->name('trips.review');
        Route::post('/trips/{trip}/approve', [TripTicketController::class, 'approve'])->name('trips.approve');
        Route::post('/trips/{trip}/reject', [TripTicketController::class, 'reject'])->name('trips.reject');
        Route::patch('/trips/{trip}/status', [TripTicketController::class, 'updateStatus'])->name('trips.status');
    });

    // Admin only
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');

        foreach (['divisions' => AdminDivisionController::class, 'vehicles' => AdminVehicleController::class,
                  'vehicle-models' => AdminVehicleModelController::class, 'drivers' => AdminDriverController::class] as $slug => $ctl) {
            Route::resource($slug, $ctl)->except('show')->names("admin.$slug");
        }
    });
});
