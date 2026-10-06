<?php

namespace App\Providers;

use App\Models\Solicitud;
use App\Policies\SolicitudPolicy;
use Illuminate\Pagination\Paginator;
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
        // Monorepo: Laravel public = frontend/public; DomPDF defaults to base_path('public') which no existe.
        config(['dompdf.public_path' => public_path()]);

        Paginator::useBootstrapFive();

        Gate::policy(Solicitud::class, SolicitudPolicy::class);

        if (config('mail.default') === 'smtp' && empty(config('mail.mailers.smtp.username'))) {
            config(['mail.default' => 'log']);
        }
    }
}
