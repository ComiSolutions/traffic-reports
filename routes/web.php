<?php

use App\Http\Controllers\Admin\ReportReviewController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\TrafficReportController;
use App\Http\Middleware\EnsureTeamMembership;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\User\Dashboard as UserDashboard;
use App\Livewire\User\ReportOffence;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::view('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('dashboard', UserDashboard::class)
    ->middleware(['auth', 'verified', 'role:user'])
    ->name('dashboard');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('dashboard', AdminDashboard::class)->name('dashboard');

        Route::get('reports', [ReportReviewController::class, 'index'])->name('reports.index');
        Route::get('reports/{report}', [ReportReviewController::class, 'show'])->name('reports.show');
        Route::patch('reports/{report}/approve', [ReportReviewController::class, 'approve'])->name('reports.approve');
        Route::patch('reports/{report}/reject', [ReportReviewController::class, 'reject'])->name('reports.reject');
    });

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('team.dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');

    Route::get('reports/create', ReportOffence::class)
        ->middleware('role:user,admin')
        ->name('reports.create');

    Route::resource('reports', TrafficReportController::class)
        ->middleware('role:user,admin')
        ->only(['index', 'show']);

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

require __DIR__.'/settings.php';
