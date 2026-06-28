<?php

namespace App\Providers;

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
        \Illuminate\Support\Str::macro('utf8Slug', function ($title, $separator = '-') {
            // Remove all characters that are not letters, numbers, marks, spaces or hyphens
            $title = preg_replace('/[^\p{L}\p{N}\p{M}\s-]+/u', '', $title);
            // Replace spaces and repeated separators with single separator
            $title = preg_replace('/[\s-]+/u', $separator, $title);
            // Trim separator from start and end
            return trim($title, $separator);
        });

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\LogSuccessfulLogin::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Failed::class,
            \App\Listeners\LogFailedLogin::class
        );

        \Illuminate\Support\Facades\View::composer(['layouts.navigation', 'layouts.public-navigation', 'layouts.public'], function ($view) {
            $categories = \App\Models\Category::getActiveCategoriesForNavigation();
            $view->with('categories', $categories);
        });
    }
}
