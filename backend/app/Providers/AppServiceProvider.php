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
        // Monorepo: assets en frontend/public; chroot debe incluir esa carpeta para imágenes en PDF.
        $chroot = array_values(array_filter([
            realpath(base_path()),
            realpath(public_path()),
            realpath(base_path('..')),
        ]));
        config([
            'dompdf.public_path' => public_path(),
            'dompdf.options.chroot' => $chroot,
        ]);

        Paginator::useBootstrapFive();

        Gate::policy(Solicitud::class, SolicitudPolicy::class);

        if (config('mail.default') === 'smtp' && empty(config('mail.mailers.smtp.username'))) {
            config(['mail.default' => 'log']);
        }
    }
}
