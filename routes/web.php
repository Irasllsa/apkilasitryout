<?php
/**
 * Definisi Route Aplikasi
 * @var \App\Core\Router $router
 */
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\Admin\PemetaanController;

// Root -> arahkan ke dashboard / login
$router->get('/', [DashboardController::class, 'index']);

// ---- Autentikasi ----
$router->get('/login', [AuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [AuthController::class, 'login'], ['guest']);
$router->post('/logout', [AuthController::class, 'logout'], ['auth']);

// ---- Dashboard (semua role) ----
$router->get('/dashboard', [DashboardController::class, 'index'], ['auth']);

// =====================================================================
//  AREA ADMIN
// =====================================================================
$router->group(['prefix' => '/admin', 'middleware' => ['auth', 'role:admin']], function ($router) {

    // Master Pemetaan
    $router->get('/pemetaan', [PemetaanController::class, 'index']);

    // Kelas
    $router->post('/pemetaan/kelas', [PemetaanController::class, 'storeKelas']);
    $router->post('/pemetaan/kelas/{id}/update', [PemetaanController::class, 'updateKelas']);
    $router->post('/pemetaan/kelas/{id}/delete', [PemetaanController::class, 'deleteKelas']);

    // Mata Pelajaran
    $router->get('/pemetaan/kelas/{id}', [PemetaanController::class, 'showKelas']);
    $router->post('/pemetaan/mapel', [PemetaanController::class, 'storeMapel']);
    $router->post('/pemetaan/mapel/{id}/update', [PemetaanController::class, 'updateMapel']);
    $router->post('/pemetaan/mapel/{id}/delete', [PemetaanController::class, 'deleteMapel']);

    // Bab
    $router->get('/pemetaan/mapel/{id}', [PemetaanController::class, 'showMapel']);
    $router->post('/pemetaan/bab', [PemetaanController::class, 'storeBab']);
    $router->post('/pemetaan/bab/{id}/update', [PemetaanController::class, 'updateBab']);
    $router->post('/pemetaan/bab/{id}/delete', [PemetaanController::class, 'deleteBab']);

    // Sub Kemampuan
    $router->get('/pemetaan/bab/{id}', [PemetaanController::class, 'showBab']);
    $router->post('/pemetaan/sub', [PemetaanController::class, 'storeSub']);
    $router->post('/pemetaan/sub/{id}/update', [PemetaanController::class, 'updateSub']);
    $router->post('/pemetaan/sub/{id}/delete', [PemetaanController::class, 'deleteSub']);
});
