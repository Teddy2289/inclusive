<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\PartenaireController;
use App\Enums\ContactStatut;
use App\Enums\PartenaireStatut;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\RolePermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// ─── Public ───────────────────────────────────────────────
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Enums disponibles pour le front
Route::get('/enums', fn() => response()->json([
    'partenaire_statuts' => PartenaireStatut::options(),
    'contact_statuts'    => ContactStatut::options(),
]));

// ─── Authentifié ──────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Partenaires
    Route::apiResource('partenaires', PartenaireController::class);
    Route::patch(
        'partenaires/{partenaire}/statut',
        [PartenaireController::class, 'changerStatut']
    );
    Route::get(
        'partenaires/{partenaire}/transitions',
        [PartenaireController::class, 'transitionsDisponibles']
    );

    // Contacts
    Route::apiResource('contacts', ContactController::class);
    Route::patch(
        'contacts/{contact}/statut',
        [ContactController::class, 'changerStatut']
    );
    Route::get(
        'contacts/{contact}/transitions',
        [ContactController::class, 'transitionsDisponibles']
    );

    // Import Excel
    Route::post('/import', [ImportController::class, 'import']);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/imports', [DashboardController::class, 'imports']);
    Route::get('/dashboard/sync-status', [DashboardController::class, 'syncStatus']);

    // Users management
    Route::apiResource('users', UserController::class);
    Route::get('roles', [UserController::class, 'getRoles']);
    Route::get('permissions', [UserController::class, 'getPermissions']);

     // Rôles
    Route::get('/roles',                [RolePermissionController::class, 'indexRoles']);
    Route::get('/roles/{role}',         [RolePermissionController::class, 'showRole']);
    Route::post('/roles',               [RolePermissionController::class, 'storeRole']);
    Route::put('/roles/{role}',         [RolePermissionController::class, 'updateRole']);
    Route::delete('/roles/{role}',      [RolePermissionController::class, 'destroyRole']);

    // Permissions
    Route::get('/permissions',                      [RolePermissionController::class, 'indexPermissions']);
    Route::get('/permissions/{permission}',         [RolePermissionController::class, 'showPermission']);
    Route::post('/permissions',                     [RolePermissionController::class, 'storePermission']);
    Route::put('/permissions/{permission}',         [RolePermissionController::class, 'updatePermission']);
    Route::delete('/permissions/{permission}',      [RolePermissionController::class, 'destroyPermission']);

    Route::get('/crm/accounts', [App\Http\Controllers\Api\CrmController::class, 'accounts']);
});
