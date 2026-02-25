<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/demo');

Route::get('/demo', function () {
    $pusherConfig = config('broadcasting.connections.pusher.options');

    return view('demo', [
        'pusherKey' => config('broadcasting.connections.pusher.key'),
        'pusherCluster' => $pusherConfig['cluster'] ?? null,
        'pusherHost' => env('PUSHER_PUBLIC_HOST', $pusherConfig['host'] ?? null),
        'pusherPort' => env('PUSHER_PUBLIC_PORT', $pusherConfig['port'] ?? null),
        'pusherScheme' => env('PUSHER_PUBLIC_SCHEME', $pusherConfig['scheme'] ?? null),
    ]);
});
