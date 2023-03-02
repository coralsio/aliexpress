<?php

namespace Corals\Modules\Aliexpress;

use Corals\Foundation\Providers\BasePackageServiceProvider;
use Corals\Modules\Aliexpress\Console\Commands\RunImports;
use Corals\Modules\Aliexpress\Facades\Aliexpress;
use Corals\Modules\Aliexpress\Providers\AliexpressAuthServiceProvider;
use Corals\Modules\Aliexpress\Providers\AliexpressObserverServiceProvider;
use Corals\Modules\Aliexpress\Providers\AliexpressRouteServiceProvider;
use Corals\Settings\Facades\Modules;
use Illuminate\Foundation\AliasLoader;

class AliexpressServiceProvider extends BasePackageServiceProvider
{
    protected $defer = true;
    /**
     * @var
     */
    protected $packageCode = 'corals-aliexpress';

    /**
     * Bootstrap the application events.
     *
     * @return void
     */

    public function bootPackage()
    {
        // Load view
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Aliexpress');

        // Load translation
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'Aliexpress');

        $this->registerCommand();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function registerPackage()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/aliexpress.php', 'aliexpress');

        $this->app->register(AliexpressRouteServiceProvider::class);
        $this->app->register(AliexpressAuthServiceProvider::class);
        $this->app->register(AliexpressObserverServiceProvider::class);

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Aliexpress', Aliexpress::class);
            if (\Modules::isModuleActive('corals-marketplace')) {
                $namespace = "Marketplace";
            } else {
                $namespace = "Ecommerce";
            }

            $loader->alias('ImportBrand', 'Corals\\Modules\\' . $namespace . '\\Models\\Brand');
            $loader->alias('ImportCategory', 'Corals\\Modules\\' . $namespace . '\\Models\\Category');
            $loader->alias('ImportProduct', 'Corals\\Modules\\' . $namespace . '\\Models\\Product');
            $loader->alias('ImportSKU', 'Corals\\Modules\\' . $namespace . '\\Models\\SKU');
            $loader->alias('ImportTag', 'Corals\\Modules\\' . $namespace . '\\Models\\Tag');
            $loader->alias('ImportProductService', 'Corals\\Modules\\' . $namespace . '\\Services\\ProductService');
            $loader->alias('ImportProductRequest',
                'Corals\\Modules\\' . $namespace . '\\Http\\Requests\\ProductRequest');
        });
    }

    protected function registerCommand()
    {
        $this->commands(RunImports::class);
    }

    public function registerModulesPackages()
    {
        Modules::addModulesPackages('corals/aliexpress');
    }
}
