<?php
use App\Http\Controllers\HobbyController;
use Illuminate\Support\Facades\Route;

Route::post('/hobby', [HobbyController::class, 'createHobby'])->name('hobby');
