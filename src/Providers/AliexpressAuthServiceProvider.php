<?php

namespace Corals\Modules\Aliexpress\Providers;

use Corals\Modules\Aliexpress\Models\Import;
use Corals\Modules\Aliexpress\Policies\ImportPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AliexpressAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Import::class => ImportPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
