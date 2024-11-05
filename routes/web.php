<?php

use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return view('welcome');
});

// Route::get('/home', function () {
//     return view('home');
// })->middleware(['auth', 'verified'])->name('home');

Route::get('home/',[BookmarkController::class, 'index'])->name('home')->middleware('auth');
Route::post('home/',[BookmarkController::class, 'store'])->name('bookmark.store')->middleware('auth');
Route::delete('home/{bookmark}',[BookmarkController::class, 'destroy'])->name('bookmark.delete')->middleware('auth');
Route::get('search',[BookmarkController::class, 'search'])->name('bookmark.search')->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
