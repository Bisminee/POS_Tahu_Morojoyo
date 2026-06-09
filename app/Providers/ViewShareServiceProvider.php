<?php

namespace App\Providers;

use App\Models\Identitas;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewShareServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('identitas', Identitas::first());
        });
    }
}