<?php

namespace Corals\Modules\Aliexpress\Models;

use Corals\Foundation\Models\BaseModel;
use Spatie\Activitylog\Traits\LogsActivity;

class AliexpressCategory extends BaseModel
{
    use  LogsActivity;

    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'aliexpress.models.aliexpress_category';

    protected $table = 'aliexpress_categories';

    protected $guarded = ['id'];

    protected $casts = [];

    public function categories()
    {
        return $this->hasMany(Import::class, 'aliexpress_category_import');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
