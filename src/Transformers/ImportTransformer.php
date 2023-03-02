<?php

namespace Corals\Modules\Aliexpress\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Aliexpress\Models\Import;

class ImportTransformer extends BaseTransformer
{
    public function __construct($extras = [])
    {
        $this->resource_url = config('aliexpress.models.import.resource_url');

        parent::__construct($extras);
    }

    /**
     * @param Import $import
     * @return array
     * @throws \Throwable
     */
    public function transform(Import $import)
    {
        $levels = [
            'pending' => 'info',
            'in_progress' => 'success',
            'completed' => 'primary',
            'failed' => 'danger',
            'canceled' => 'warning'
        ];

        $transformedArray = [
            'id' => $import->id,
            'title' => \Str::limit($import->title, 50),
            'status' => formatStatusAsLabels($import->status, [
                'level' => $levels[$import->status],
                'text' => trans('Aliexpress::attributes.import.status_options.' . $import->status)
            ]),
            'categories' => formatArrayAsLabels($import->categories->pluck('name'), 'success',
                '<i class="fa fa-folder-open"></i>'),
            'keywords' => formatArrayAsLabels($import->keywords, 'success', '<i class="fa fa-tag"></i>'),
            'imported_products_count' => $import->products->count(),
            'created_at' => format_date($import->created_at),
            'updated_at' => format_date($import->updated_at),
            'action' => $this->actions($import)
        ];

        return parent::transformResponse($transformedArray);
    }
}
