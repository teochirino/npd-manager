<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\SkuController;
use App\Http\Controllers\API\GateApprovalController;
use App\Http\Controllers\API\UserController;

// Ruta de prueba
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Autenticación
Route::post('/login', [AuthController::class, 'login']);

// Endpoint de prueba para verificar credenciales
Route::post('/test-login', function(\Illuminate\Http\Request $request) {
    $user = \App\Models\User::where('email', $request->email)->first();
    
    if (!$user) {
        return response()->json(['error' => 'Usuario no encontrado', 'email' => $request->email], 404);
    }
    
    $passwordCheck = \Illuminate\Support\Facades\Hash::check($request->password, $user->password);
    
    return response()->json([
        'user_found' => true,
        'email' => $user->email,
        'name' => $user->name,
        'password_correct' => $passwordCheck,
        'password_hash_preview' => substr($user->password, 0, 30) . '...',
    ]);
});

// Rutas protegidas (requieren token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Proyectos
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::post('/projects/{id}/update-progress', [ProjectController::class, 'updateProgress']);
    Route::post('/projects/{id}/change-phase', [ProjectController::class, 'changePhase']);
    
    // Tareas
    Route::get('/projects/{projectId}/tasks', [TaskController::class, 'index']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::post('/projects/{projectId}/tasks/bulk-update', [TaskController::class, 'bulkUpdate']);
    
    // SKUs
    Route::get('/projects/{projectId}/skus', [SkuController::class, 'index']);
    Route::post('/skus', [SkuController::class, 'store']);
    Route::put('/skus/{id}', [SkuController::class, 'update']);
    Route::delete('/skus/{id}', [SkuController::class, 'destroy']);
    
    // Gate Approvals
    Route::get('/gate-approvals', [GateApprovalController::class, 'index']);
    Route::post('/gate-approvals', [GateApprovalController::class, 'store']);
    Route::post('/gate-approvals/{id}/approve', [GateApprovalController::class, 'approve']);
    Route::post('/gate-approvals/{id}/reject', [GateApprovalController::class, 'reject']);
    
    // Users
    Route::get('/users/engineers', [UserController::class, 'engineers']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});