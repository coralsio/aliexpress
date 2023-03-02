<?php

namespace Corals\Modules\Aliexpress\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\Aliexpress\Models\Import;
use Corals\Modules\Aliexpress\Transformers\ImportTransformer;
use Yajra\DataTables\EloquentDataTable;

class ImportsDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('aliexpress.models.import.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new ImportTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param Import $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(Import $model)
    {
        if (\Modules::isModuleActive('corals-marketplace')) {
            if (\Store::isStoreAdmin()) {
                return $model->newQuery();
            } else {
                return user()->aliexpress_imports()->select('aliexpress_imports.*')->newQuery();
            }
        } else {
            return $model->newQuery();
        }
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['visible' => false],
            'title' => ['title' => trans('Aliexpress::attributes.import.title')],
            'status' => ['title' => trans('Corals::attributes.status')],
            'keywords' => ['title' => trans('Aliexpress::attributes.import.keywords')],
            'imported_products_count' => ['title' => trans('Aliexpress::attributes.import.imported_products_count')],
            'categories' => ['title' => trans('Aliexpress::attributes.import.categories')],
            'created_at' => ['title' => trans('Corals::attributes.created_at')],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at')],
        ];
    }
}
