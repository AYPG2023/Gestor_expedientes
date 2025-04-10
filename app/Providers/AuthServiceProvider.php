<?php

namespace App\Providers;

use App\Models\Archivo;
use App\Policies\ArchivoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * El arreglo de políticas del modelo.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Archivo::class => ArchivoPolicy::class,
    ];

    /**
     * Registra cualquier servicio de autenticación / autorización.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Aquí puedes registrar más gates si deseas
    }
}
