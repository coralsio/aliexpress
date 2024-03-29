<?php

namespace Corals\Modules\Aliexpress\Providers;

use Corals\Foundation\Providers\BaseUpdateModuleServiceProvider;

class UpdateModuleServiceProvider extends BaseUpdateModuleServiceProvider
{
    protected $module_code = 'corals-aliexpress';
    protected $batches_path = __DIR__ . '/../update-batches/*.php';
    protected $module_public_path = __DIR__ . '/../public';
}
