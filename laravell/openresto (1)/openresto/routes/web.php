<?php

use App\Http\Controllers\RestoController;
use Illuminate\Support\Facades\Route;

// --- JALUR PELANGGAN (CUSTOMER ROUTES) ---
// Halaman depan (Input Meja & HP)
Route::get('/', [RestoController::class, 'index'])->name('index');
Route::post('/', [RestoController::class, 'storeSession'])->name('index.store');

// Halaman menu & proses checkout pesanan
Route::get('/menu', [RestoController::class, 'menu'])->name('menu');
Route::post('/order', [RestoController::class, 'order'])->name('order.process');
Route::get('/order/receipt/{id}', [RestoController::class, 'receipt'])->name('order.receipt');


// --- JALUR ADMIN DAPUR (ADMIN ROUTES) ---
// Halaman login & autentikasi
Route::get('/admin/login', [RestoController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [RestoController::class, 'loginProcess'])->name('admin.login.submit');
Route::get('/admin/logout', [RestoController::class, 'logout'])->name('admin.logout');

// Halaman utama dashboard statistik & antrean dapur
Route::get('/admin/dashboard', [RestoController::class, 'dashboard'])->name('admin.dashboard');

// Admin menu management
Route::get('/admin/menus', [RestoController::class, 'adminMenus'])->name('admin.menus');
Route::get('/admin/menus/create', [RestoController::class, 'adminMenuCreate'])->name('admin.menus.create');
Route::post('/admin/menus/store', [RestoController::class, 'adminMenuStore'])->name('admin.menus.store');
Route::get('/admin/menus/{id}/edit', [RestoController::class, 'adminMenuEdit'])->name('admin.menus.edit');
Route::post('/admin/menus/{id}/update', [RestoController::class, 'adminMenuUpdate'])->name('admin.menus.update');
Route::post('/admin/menus/{id}/delete', [RestoController::class, 'adminMenuDelete'])->name('admin.menus.delete');
