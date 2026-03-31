<?php

use Illuminate\Support\Facades\Route;

// ── Auth ────────────────────────────────────────────────────────
use App\Http\Controllers\Auth\AuthController;

// ── API Publique ─────────────────────────────────────────────────
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SolutionController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ClientController;

// ── Admin ────────────────────────────────────────────────────────
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminSolutionController;
use App\Http\Controllers\Admin\AdminPortfolioController;
use App\Http\Controllers\Admin\AdminNewsController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\Admin\AdminClientController;
use App\Http\Controllers\Admin\AdminSocialController;


// ════════════════════════════════════════════════════════════════
//  AUTHENTIFICATION
// ════════════════════════════════════════════════════════════════
Route::post('/auth/login',  [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);
});


// ════════════════════════════════════════════════════════════════
//  API PUBLIQUE  — pas d'authentification requise
// ════════════════════════════════════════════════════════════════
Route::prefix('v1')->group(function () {

    // Services
    Route::get('/services',        [ServiceController::class, 'index']);
    Route::get('/services/{slug}', [ServiceController::class, 'show']);

    // Solutions
    Route::get('/solutions',        [SolutionController::class, 'index']);
    Route::get('/solutions/{slug}', [SolutionController::class, 'show']);

    // Portfolio / Réalisations
    Route::get('/portfolio',             [PortfolioController::class, 'index']);
    Route::get('/portfolio/{slug}',      [PortfolioController::class, 'show']);
    Route::get('/portfolio-categories',  [PortfolioController::class, 'categories']);

    // News / Articles
    Route::get('/news',        [NewsController::class, 'index']);
    Route::get('/news/{slug}', [NewsController::class, 'show']);

    // Clients bandeau défilant
    Route::get('/clients', [ClientController::class, 'index']);

    // Formulaire de contact
    Route::post('/contact', [ContactController::class, 'store']);

    // Réseaux sociaux (Footer)
    Route::get('/socials', [AdminSocialController::class, 'index']);
});


// ════════════════════════════════════════════════════════════════
//  ADMIN  — authentification Sanctum obligatoire
// ════════════════════════════════════════════════════════════════
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {

    // ── Services ─────────────────────────────────────────────
    Route::get('/services',                     [AdminServiceController::class, 'index']);
    Route::post('/services',                    [AdminServiceController::class, 'store']);
    Route::get('/services/{service}',           [AdminServiceController::class, 'show']);
    Route::put('/services/{service}',           [AdminServiceController::class, 'update']);
    Route::delete('/services/{service}',        [AdminServiceController::class, 'destroy']);
    Route::post('/services/reorder',            [AdminServiceController::class, 'reorder']);

    // ── Solutions ────────────────────────────────────────────
    Route::get('/solutions',                    [AdminSolutionController::class, 'index']);
    Route::post('/solutions',                   [AdminSolutionController::class, 'store']);
    Route::get('/solutions/{solution}',         [AdminSolutionController::class, 'show']);
    Route::put('/solutions/{solution}',         [AdminSolutionController::class, 'update']);
    Route::delete('/solutions/{solution}',      [AdminSolutionController::class, 'destroy']);

    // ── Portfolio / Réalisations ──────────────────────────────
    Route::get('/portfolio',                    [AdminPortfolioController::class, 'index']);
    Route::post('/portfolio',                   [AdminPortfolioController::class, 'store']);
    Route::get('/portfolio/{portfolio}',        [AdminPortfolioController::class, 'show']);
    Route::put('/portfolio/{portfolio}',        [AdminPortfolioController::class, 'update']);
    Route::delete('/portfolio/{portfolio}',     [AdminPortfolioController::class, 'destroy']);

    // Catégories portfolio
    Route::get('/portfolio-categories',         [AdminPortfolioController::class, 'categories']);
    Route::post('/portfolio-categories',        [AdminPortfolioController::class, 'storeCategory']);
    Route::delete('/portfolio-categories/{id}', [AdminPortfolioController::class, 'destroyCategory']);

    // Galerie d'images
    Route::post('/portfolio/{portfolio}/images', [AdminPortfolioController::class, 'addImage']);
    Route::delete('/portfolio/images/{image}',   [AdminPortfolioController::class, 'deleteImage']);

    // ── News ──────────────────────────────────────────────────
    Route::get('/news',                         [AdminNewsController::class, 'index']);
    Route::post('/news',                        [AdminNewsController::class, 'store']);
    Route::get('/news/{news}',                  [AdminNewsController::class, 'show']);
    Route::put('/news/{news}',                  [AdminNewsController::class, 'update']);
    Route::delete('/news/{news}',               [AdminNewsController::class, 'destroy']);

    // ── Contacts ──────────────────────────────────────────────
    Route::get('/contacts/stats',               [AdminContactController::class, 'stats']);
    Route::get('/contacts',                     [AdminContactController::class, 'index']);
    Route::get('/contacts/{contact}',           [AdminContactController::class, 'show']);
    Route::put('/contacts/{contact}',           [AdminContactController::class, 'update']);
    Route::delete('/contacts/{contact}',        [AdminContactController::class, 'destroy']);

    // ── Clients ───────────────────────────────────────────────
    Route::get('/clients',                      [AdminClientController::class, 'index']);
    Route::post('/clients',                     [AdminClientController::class, 'store']);
    Route::get('/clients/{client}',             [AdminClientController::class, 'show']);
    Route::put('/clients/{client}',             [AdminClientController::class, 'update']);
    Route::delete('/clients/{client}',          [AdminClientController::class, 'destroy']);
    Route::post('/clients/reorder',             [AdminClientController::class, 'reorder']);

    // ── Réseaux sociaux ───────────────────────────────────────
    Route::get('/socials',          [AdminSocialController::class, 'adminIndex']);
    Route::post('/socials',         [AdminSocialController::class, 'store']);
    Route::put('/socials/{social}', [AdminSocialController::class, 'update']);
    Route::delete('/socials/{social}', [AdminSocialController::class, 'destroy']);
});