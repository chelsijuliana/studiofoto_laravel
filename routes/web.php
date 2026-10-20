<?php


use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Login;
use App\Livewire\Admin\ChatIndex;
use App\Livewire\Admin\BookingIndex;
use App\Livewire\Admin\BookingShow;
use App\Livewire\Admin\ServiceManage;
use App\Livewire\Admin\ScheduleIndex;
use App\Livewire\Admin\ReportIndex;
use App\Livewire\Admin\PortfolioManage;
use App\Livewire\Admin\UserIndex;
use App\Livewire\Admin\OperatingHourManage;
use App\Livewire\Admin\ProfileEdit;
use App\Livewire\Admin\ChatShow;
use App\Livewire\Admin\NotificationIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PasswordResetController;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', Login::class)->name('login');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('logout');

    Route::middleware('admin.web')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        Route::get('/bookings', BookingIndex::class)->name('bookings.index');
        Route::get('/bookings/{booking}', BookingShow::class)->name('bookings.show');
        Route::get('/chat', ChatIndex::class)->name('chat.index');
        Route::get('/services', ServiceManage::class)->name('services.index');
        Route::get('/schedule', ScheduleIndex::class)->name('schedule.index');
        Route::get('/reports', ReportIndex::class)->name('reports.index');
        Route::get('/users', UserIndex::class)->name('users.index');
        Route::get('/portfolio', PortfolioManage::class)->name('portfolio.index');
        Route::get('/operating-hours', OperatingHourManage::class)->name('operating-hours.index');
        Route::get('/profile', ProfileEdit::class)->name('profile.edit');
        Route::get('/chat/{booking}', ChatShow::class)->name('chat.show');
        Route::get('/notifications', NotificationIndex::class)->name('notifications.index');


    });

});

Route::get('/reset-password', [PasswordResetController::class, 'showResetForm']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);