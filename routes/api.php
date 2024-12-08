<?php
use App\Http\Controllers\HobbyController;
use App\Http\Controllers\RolesController;
use App\Models\Roles;
use Illuminate\Support\Facades\Route;

Route::post('/hobby', [HobbyController::class, 'createHobby'])->name('hobby');

Route::post('/roles', [RolesController::class, "createRole"])->name("roles");

Route::get("/hobby", [HobbyController::class,"fetchAllHobbies"])->name("fetchAllHobbies");
