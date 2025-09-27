<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('apiSuccess', function ($data, $message = null, $code = 200) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => $message,
            ], $code);
        });

        Response::macro('apiError', function ($message, $errors = null, $code = 400) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $message,
                'errors' => $errors,
            ], $code);
        });
    }
}
