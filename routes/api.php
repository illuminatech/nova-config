<?php
/**
 * API routes for this Nova tool.
 * These routes are loaded by the ServiceProvider of this tool.
 * They are protected by this tool's "Authorize" middleware.
 *
 * @see \Illuminatech\NovaConfig\NovaConfigServiceProvider::routes()
 */

namespace Illuminatech\NovaConfig\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::get('config', ConfigController::class.'@index')->name('config.index');
Route::put('config', ConfigController::class.'@update')->name('config.update');
Route::delete('config', ConfigController::class.'@reset')->name('config.reset');
