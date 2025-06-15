<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Task routes
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/toggle-status', [TaskController::class, 'toggleStatus'])
        ->name('tasks.toggle-status');
    Route::patch('/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])
        ->name('tasks.update-status');
    Route::post('/tasks/{task}/documentation', [TaskController::class, 'submitDocumentation'])
        ->name('tasks.submit-documentation');
});

Route::middleware(['auth','role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');

    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
});

Route::middleware(['auth', 'role:admin', 'can:view activities'])->group(function () {
    Route::get('/admin/activities', [ActivityController::class, 'index'])
        ->name('admin.activities.index');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('/projects', ProjectController::class);
    Route::patch('/projects/{project}/status', [ProjectController::class, 'updateStatus'])
        ->name('projects.update-status');
});

require __DIR__.'/auth.php';
