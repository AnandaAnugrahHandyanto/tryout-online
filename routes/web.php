<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Legacy /dashboard -> redirect by role
Route::get('/dashboard', function () {
    $role = auth()->user()->role ?? 'siswa';
    $map = [
        'admin' => 'admin.dashboard',
        'guru' => 'guru.dashboard',
        'siswa' => 'siswa.dashboard',
        'orang_tua' => 'orang-tua.dashboard',
    ];
    return redirect()->route($map[$role] ?? 'siswa.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Role dashboards
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::resource('kelas', \App\Http\Controllers\Admin\KelasController::class)->except(['show']);
    Route::resource('mapel', \App\Http\Controllers\Admin\MataPelajaranController::class)->parameters(['mapel'=>'mapel'])->except(['show']);
    Route::resource('guru', \App\Http\Controllers\Admin\GuruController::class)->except(['show']);
    Route::resource('siswa', \App\Http\Controllers\Admin\SiswaController::class)->except(['show']);
    Route::resource('ortu', \App\Http\Controllers\Admin\OrangTuaController::class)->except(['show']);
});

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', fn() => view('guru.dashboard'))->name('dashboard');
    Route::resource('soal', \App\Http\Controllers\Guru\SoalController::class)->except(['show']);
    Route::resource('tryout', \App\Http\Controllers\Guru\TryoutController::class);
});

Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', fn() => view('siswa.dashboard'))->name('dashboard');
});

Route::middleware(['auth', 'role:orang_tua'])->prefix('orang-tua')->name('orang-tua.')->group(function () {
    Route::get('/dashboard', fn() => view('orang-tua.dashboard'))->name('dashboard');
});

require __DIR__.'/auth.php';
