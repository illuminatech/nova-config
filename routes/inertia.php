<?php
/**
 * Inertia routes for this Nova tool.
 * These routes are loaded by the ServiceProvider of this tool.
 * They are protected by this tool's "Authorize" middleware.
 *
 * @see \Illuminatech\NovaConfig\NovaConfigServiceProvider::routes()
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return inertia('IlluminatechNovaConfig');
});