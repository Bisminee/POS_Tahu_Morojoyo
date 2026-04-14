<?php

namespace App\Providers;

use App\Models\Karyawan;
use App\Policies\KaryawanPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Daftarkan semua policy model di sini.
     */
    protected $policies = [
        Karyawan::class => KaryawanPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
