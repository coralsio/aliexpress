<?php

namespace Corals\Modules\Aliexpress\Providers;

use Corals\Modules\Aliexpress\Models\Import;
use Corals\Modules\Aliexpress\Observers\ImportObserver;
use Illuminate\Support\ServiceProvider;

class AliexpressObserverServiceProvider extends ServiceProvider
{
    /**
     * Register Observers
     */
    public function boot()
    {
        Import::observe(ImportObserver::class);
    }
}
