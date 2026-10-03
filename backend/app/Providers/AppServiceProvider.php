<?php

namespace App\Providers;

use App\Models\Solicitud;
use App\Policies\SolicitudPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Solicitud::class, SolicitudPolicy::class);

        if (config('mail.default') === 'smtp' && empty(config('mail.mailers.smtp.username'))) {
            config(['mail.default' => 'log']);
        }
    }
}
