<?php

namespace Corals\Modules\Aliexpress\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Aliexpress\DataTables\ImportsDataTable;
use Corals\Modules\Aliexpress\Http\Requests\ImportRequest;
use Corals\Modules\Aliexpress\Models\Import;

class ImportsController extends BaseController
{
    protected $excludedRequestParams = ['categories'];

    public function __construct()
    {
        $this->resource_url = config('aliexpress.models.import.resource_url');

        $this->resource_model = new Import();

        $this->title = 'Aliexpress::module.import.title';
        $this->title_singular = 'Aliexpress::module.import.title_singular';

        parent::__construct();
    }

    /**
     * @param ImportRequest $request
     * @param ImportsDataTable $dataTable
     * @return mixed
     */
    public function index(ImportRequest $request, ImportsDataTable $dataTable)
    {
        return $dataTable->render('Aliexpress::imports.index');
    }

    /**
     * @param ImportRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(ImportRequest $request)
    {
        $import = new Import();

        $this->setViewSharedData([
            'title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular]),
        ]);

        return view('Aliexpress::imports.create_edit')->with(compact('import'));
    }

    /**
     * @param ImportRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(ImportRequest $request)
    {
        try {
            $data = $request->except($this->excludedRequestParams);

            if (\Modules::isModuleActive('corals-marketplace')) {
                $data = \Store::setStoreData($data);
            }

            $import = Import::create($data);

            $import->categories()->sync($request->get('categories', []));

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Import::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param ImportRequest $request
     * @param Import $import
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(ImportRequest $request, Import $import)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.show_title', ['title' => $import->title])]);

//        $this->setViewSharedData(['edit_url' => $this->resource_url . '/' . $import->hashed_id . '/edit']);

        return view('Aliexpress::imports.show')->with(compact('import'));
    }

    /**
     * @param ImportRequest $request
     * @param Import $import
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ImportRequest $request, Import $import)
    {
        $this->setViewSharedData([
            'title_singular' => trans('Corals::labels.update_title', ['title' => $import->title]),
        ]);

        return view('Aliexpress::imports.create_edit')->with(compact('import'));
    }

    /**
     * @param ImportRequest $request
     * @param Import $import
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|mixed
     */
    public function update(ImportRequest $request, Import $import)
    {
        try {
            $data = $request->except($this->excludedRequestParams);

            if (\Modules::isModuleActive('corals-marketplace')) {
                $data = \Store::setStoreData($data);
            }

            $import->update($data);

            $import->categories()->sync($request->get('categories', []));

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Import::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param ImportRequest $request
     * @param Import $import
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ImportRequest $request, Import $import)
    {
        try {
            $import->delete();

            $message = [
                'level' => 'success',
                'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular]),
            ];
        } catch (\Exception $exception) {
            log_exception($exception, Import::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }
}
