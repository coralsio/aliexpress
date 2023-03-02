<?php

namespace Corals\Modules\Aliexpress\Providers;

use Corals\Foundation\Providers\BaseInstallModuleServiceProvider;
use Corals\Modules\Aliexpress\database\migrations\AliexpressTables;
use Corals\Modules\Aliexpress\database\seeds\AliexpressDatabaseSeeder;

class InstallModuleServiceProvider extends BaseInstallModuleServiceProvider
{
    protected $module_public_path = __DIR__ . '/../public';

    protected $migrations = [
        AliexpressTables::class,
    ];

    protected function providerBooted()
    {
        $this->createSchema();

        $aliexpressDatabaseSeeder = new AliexpressDatabaseSeeder();

        $aliexpressDatabaseSeeder->run();
    }
}
