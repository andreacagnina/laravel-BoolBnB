<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            $unreadCount = Message::where('is_read', false)
                ->whereHas('property', function ($query) {
                    $query->where('user_id', Auth::id()); // Filtra per proprietà dell'utente loggato
                })
                ->count();

            $view->with('unreadCount', $unreadCount);
        });
    }
}
