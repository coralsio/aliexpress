<?php

namespace Corals\Modules\Aliexpress\Providers;

use Corals\Foundation\Providers\BaseUninstallModuleServiceProvider;
use Corals\Modules\Aliexpress\database\migrations\AliexpressTables;
use Corals\Modules\Aliexpress\database\seeds\AliexpressDatabaseSeeder;

class UninstallModuleServiceProvider extends BaseUninstallModuleServiceProvider
{
    protected $migrations = [
        AliexpressTables::class,
    ];

    protected function providerBooted()
    {
        $this->dropSchema();

        $aliexpressDatabaseSeeder = new AliexpressDatabaseSeeder();

        $aliexpressDatabaseSeeder->rollback();
    }
}
