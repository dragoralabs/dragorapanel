<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\ServerDetailController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebServerController;
use Illuminate\Support\Facades\Route;

// ── Setup (no auth) ──
Route::get('/setup', [SetupController::class, 'seed']);

// ── Auth ──
Route::match(['get', 'post'], '/auth', [AuthController::class, 'handle']);

// ── User Profile & Settings ──
Route::get('/user/profile', [UserController::class, 'profile']);
Route::put('/user/profile', [UserController::class, 'profileUpdate']);
Route::post('/user/password', [UserController::class, 'changePassword']);
Route::get('/user/activity', [UserController::class, 'myActivity']);

// ── API Tokens ──
Route::get('/user/tokens', [UserController::class, 'tokensIndex']);
Route::post('/user/tokens', [UserController::class, 'tokensStore']);
Route::delete('/user/tokens/{apiToken}', [UserController::class, 'tokensDestroy']);

// ── Notifications ──
Route::get('/user/notifications', [UserController::class, 'notificationsIndex']);
Route::post('/user/notifications/{notification}/read', [UserController::class, 'notificationsRead']);
Route::post('/user/notifications/read-all', [UserController::class, 'notificationsReadAll']);

// ── Servers (general CRUD) ──
Route::get('/servers', [ServerController::class, 'index']);
Route::get('/servers/{id}', [ServerController::class, 'show']);
Route::post('/servers', [ServerController::class, 'store']);
Route::put('/servers/{id}', [ServerController::class, 'update']);
Route::delete('/servers/{id}', [ServerController::class, 'destroy']);

// ── Server Detail (nested under /servers/{id}) ──
Route::post('/servers/{id}/console', [ServerDetailController::class, 'consoleSend']);
Route::get('/servers/{id}/console', [ServerDetailController::class, 'consoleLogs']);
Route::post('/servers/{id}/power', [ServerDetailController::class, 'powerAction']);

Route::get('/servers/{id}/files', [ServerDetailController::class, 'filesList']);
Route::post('/servers/{id}/files/dir', [ServerDetailController::class, 'filesCreateDir']);
Route::post('/servers/{id}/files/file', [ServerDetailController::class, 'filesCreateFile']);
Route::get('/servers/{id}/files/read', [ServerDetailController::class, 'filesRead']);
Route::put('/servers/{id}/files/write', [ServerDetailController::class, 'filesWrite']);
Route::post('/servers/{id}/files/rename', [ServerDetailController::class, 'filesRename']);
Route::post('/servers/{id}/files/delete', [ServerDetailController::class, 'filesDelete']);
Route::post('/servers/{id}/files/upload', [ServerDetailController::class, 'filesUpload']);

Route::get('/servers/{id}/backups', [ServerDetailController::class, 'backupsIndex']);
Route::post('/servers/{id}/backups', [ServerDetailController::class, 'backupsStore']);
Route::delete('/servers/{id}/backups/{backup}', [ServerDetailController::class, 'backupsDestroy']);
Route::post('/servers/{id}/backups/{backup}/lock', [ServerDetailController::class, 'backupsLock']);

Route::get('/servers/{id}/databases', [ServerDetailController::class, 'databasesIndex']);
Route::post('/servers/{id}/databases', [ServerDetailController::class, 'databasesStore']);
Route::delete('/servers/{id}/databases/{database}', [ServerDetailController::class, 'databasesDestroy']);
Route::post('/servers/{id}/databases/{database}/reset-password', [ServerDetailController::class, 'databasesResetPassword']);

Route::get('/servers/{id}/schedules', [ServerDetailController::class, 'schedulesIndex']);
Route::post('/servers/{id}/schedules', [ServerDetailController::class, 'schedulesStore']);
Route::put('/servers/{id}/schedules/{schedule}', [ServerDetailController::class, 'schedulesUpdate']);
Route::delete('/servers/{id}/schedules/{schedule}', [ServerDetailController::class, 'schedulesDestroy']);

Route::get('/servers/{id}/subusers', [ServerDetailController::class, 'subusersIndex']);
Route::post('/servers/{id}/subusers', [ServerDetailController::class, 'subusersStore']);
Route::put('/servers/{id}/subusers/{subuser}', [ServerDetailController::class, 'subusersUpdate']);
Route::delete('/servers/{id}/subusers/{subuser}', [ServerDetailController::class, 'subusersDestroy']);

Route::get('/servers/{id}/activity', [ServerDetailController::class, 'activityIndex']);

// ── Agent-Facing Node Routes (no panel auth — authenticated by node token) ──
Route::prefix('nodes')->group(function () {
    Route::post('/ping', [NodeController::class, 'agentPing']);
    Route::post('/report', [NodeController::class, 'agentReport']);
    Route::get('/servers', [NodeController::class, 'agentServers']);
    Route::get('/servers/{serverId}/commands', [NodeController::class, 'agentServerCommands']);
    Route::post('/servers/{serverId}/status', [NodeController::class, 'agentServerStatus']);
    Route::post('/servers/{serverId}/log', [NodeController::class, 'agentServerLog']);
    Route::post('/allocate-port', [NodeController::class, 'agentAllocatePort']);
});

// ── Panel Admin ──
Route::prefix('panel/admin')->group(function () {
    Route::get('/stats', [AdminController::class, 'stats']);

    Route::get('/users', [AdminController::class, 'usersIndex']);
    Route::post('/users', [AdminController::class, 'usersStore']);
    Route::put('/users/{user}', [AdminController::class, 'usersUpdate']);
    Route::delete('/users/{user}', [AdminController::class, 'usersDestroy']);

    Route::get('/locations', [AdminController::class, 'locationsIndex']);
    Route::post('/locations', [AdminController::class, 'locationsStore']);
    Route::put('/locations/{location}', [AdminController::class, 'locationsUpdate']);
    Route::delete('/locations/{location}', [AdminController::class, 'locationsDestroy']);

    Route::get('/allocations', [AdminController::class, 'allocationsIndex']);
    Route::post('/allocations', [AdminController::class, 'allocationsStore']);
    Route::delete('/allocations/{allocation}', [AdminController::class, 'allocationsDestroy']);

    Route::get('/settings', [AdminController::class, 'settingsIndex']);
    Route::put('/settings', [AdminController::class, 'settingsUpdate']);
    Route::post('/logo', [AdminController::class, 'logoUpload']);

    Route::get('/activity', [AdminController::class, 'activityIndex']);
});

// ── Panel Nodes CRUD (admin only) ──
Route::prefix('panel/nodes')->group(function () {
    Route::get('/', [NodeController::class, 'index']);
    Route::post('/', [NodeController::class, 'store']);
    Route::get('/{id}', [NodeController::class, 'show']);
    Route::put('/{id}', [NodeController::class, 'update']);
    Route::delete('/{id}', [NodeController::class, 'destroy']);
    Route::post('/{id}/regenerate', [NodeController::class, 'regenerate']);
});

// ── Web Server Config Generator (admin only) ──
Route::prefix('panel/webserver')->group(function () {
    Route::get('/configs', [WebServerController::class, 'configs']);
    Route::post('/generate', [WebServerController::class, 'generate']);
    Route::post('/install', [WebServerController::class, 'install']);
    Route::get('/status', [WebServerController::class, 'status']);
});

// ── Plugin System (admin only) ──
Route::prefix('panel/plugins')->group(function () {
    Route::get('/', [PluginController::class, 'index']);
    Route::post('/upload', [PluginController::class, 'upload']);
    Route::post('/{uniqueId}/toggle', [PluginController::class, 'toggle']);
    Route::delete('/{uniqueId}', [PluginController::class, 'destroy']);
    Route::post('/{uniqueId}/config', [PluginController::class, 'storeConfig']);
    Route::get('/assets/{uniqueId}/{path?}', [PluginController::class, 'serveAsset'])->where('path', '.*');
    Route::get('/hooks/active', [PluginController::class, 'activeHooks']);
});

