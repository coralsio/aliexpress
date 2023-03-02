<?php

namespace Corals\Modules\Aliexpress\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class Import extends BaseModel
{
    use PresentableTrait;
    use LogsActivity;

    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'aliexpress.models.import';

    protected $table = 'aliexpress_imports';

    protected $guarded = ['id'];

    protected $casts = ['keywords' => 'array'];

    public function categories()
    {
        return $this->belongsToMany(AliexpressCategory::class, 'aliexpress_category_import');
    }

    public function products()
    {
        return $this->belongsToMany(\ImportProduct::class, 'aliexpress_import_product');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
