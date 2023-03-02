<?php

namespace Corals\Modules\Aliexpress\Classes;

use Corals\Modules\Aliexpress\Models\AliexpressCategory;

class Aliexpress
{
    /**
     * Aliexpress constructor.
     */
    function __construct()
    {
    }

    /**
     * @return array|mixed
     */
    public function getAliexpressCategories()
    {
        return AliexpressCategory::active()->pluck('name', 'id')->toArray();
    }
}
