<?php

use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;

Route::prefix('files')->controller(TestController::class)->group(function () {
    Route::post('/', 'store');        // Upload file
    Route::get('/', 'index');         // List uploaded files
    Route::get('/{id}', 'show');      // View file content (name, city)
    Route::delete('/{id}', 'destroy'); // Delete file
});

// Simple GET route to test
Route::get('/test-api', [TestController::class, 'testMethod']);

// routes/api.php
Route::get('/users', function () {
    return response()->json([
        ['id' => 1, 'name' => 'Reni', 'email' => 'reni@example.com'],
        ['id' => 2, 'name' => 'Sam', 'email' => 'sam@example.com'],
    ]);
});


//--------------------Routes------




// Route::prefix('employees')->controller(EmployeeController::class)->group(function () {
//     Route::get('/get-empoloyee-details', 'getEmployeeInfo');
//     Route::post('/', 'store');
//     Route::get('/{id}', 'show');
//     Route::put('/{id}', 'update');
//     Route::delete('/{id}', 'deleteEmloyeeDecision');
// });


Route::prefix('employees')->controller(EmployeeController::class)->group(function () {
    Route::get('/pending', 'getEmpNotHandledInfo');
    Route::get('/all', 'getAllEmployeeInfo');

    Route::patch('/{id}/grade', 'updateGrade');
    Route::patch('/{id}/decision', 'updateDecision');

    Route::delete('/{id}/decision', 'deleteDecision');
});





