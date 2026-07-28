<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Import Facade View
use App\Models\Category;             // Import Model Category

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
        // Bagikan variabel $categories secara otomatis ke view layouts.app
        View::composer('layouts.app', function ($view) {
            $view->with('categories', Category::all());
        });
    }
}