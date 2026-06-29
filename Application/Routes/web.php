<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DeliveryOrderReviewController;
use App\Http\Controllers\InvoiceReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module 4: Internal Review & Approval Workflow
|--------------------------------------------------------------------------
| Paste this block into your project's routes/web.php (skip the <?php
| line above and the "use" statements if they already exist up top).
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('staff')->group(function () {
    Route::get('/', fn () => redirect()->route('do.index'));
    // PDF
    Route::get('/delivery-orders/{id}/export', [DeliveryOrderReviewController::class, 'exportPdf'])->name('do.export');
    Route::get('/invoices/{id}/export', [InvoiceReviewController::class, 'exportPdf'])->name('invoice.export');
    Route::get('/audit-log/export', [AuditLogController::class, 'exportPdf'])->name('auditlog.export');


    // Delivery Order review
    Route::get('/delivery-orders', [DeliveryOrderReviewController::class, 'index'])->name('do.index');
    Route::get('/delivery-orders/{id}', [DeliveryOrderReviewController::class, 'show'])->name('do.show');
    Route::post('/delivery-orders/{id}/assign', [DeliveryOrderReviewController::class, 'assignReviewer'])->name('do.assign');
    Route::post('/delivery-orders/{id}/approve', [DeliveryOrderReviewController::class, 'approve'])->name('do.approve');
    Route::post('/delivery-orders/{id}/reject', [DeliveryOrderReviewController::class, 'reject'])->name('do.reject');

    // Invoice review (Finance-only actions are gated inside the Blade view + can
    // be hardened further with ->middleware('staff:Finance Officer') if you want
    // a route-level check too)
    Route::get('/invoices', [InvoiceReviewController::class, 'index'])->name('invoice.index');
    Route::get('/invoices/{id}', [InvoiceReviewController::class, 'show'])->name('invoice.show');
    Route::post('/invoices/{id}/forward', [InvoiceReviewController::class, 'forwardToFinance'])->name('invoice.forward');
    Route::post('/invoices/{id}/status', [InvoiceReviewController::class, 'updateStatus'])->name('invoice.updateStatus');
    Route::post('/invoices/{id}/reject', [InvoiceReviewController::class, 'reject'])->name('invoice.reject');

    // Audit trail
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('auditlog.index');
});
