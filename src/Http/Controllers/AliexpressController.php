<?php

namespace Corals\Modules\Aliexpress\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Aliexpress\database\seeds\AliexpressCategoriesTableSeeder;
use Corals\Modules\Marketplace\Models\Store;
use Illuminate\Http\Request;

class AliexpressController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function settings(Request $request)
    {
        $this->setViewSharedData(['title_singular' => trans('Aliexpress::labels.settings.aliexpress_settings')]);

        $config_setting = config('aliexpress.settings');
        $settings = [];
        foreach ($config_setting as $key => $setting) {
            $settings['aliexpress_' . $key] = [
                'name' => trans('Aliexpress::labels.settings.' . $key),
                'settings' => $setting,
            ];
        }

        return view('Aliexpress::aliexpress.settings')->with(compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        try {
            $settings = $request->except('_token');

            if ($request->route('store')) {
                $store = Store::findByHash($request->route('store'));
                foreach ($settings as $key => $value) {
                    $store->updateSetting($key, $value);
                }
            } else {
                foreach ($settings as $key => $value) {
                    \Settings::set($key, $value, 'Aliexpress');
                }
            }

            flash(trans(
                'Corals::messages.success.saved',
                ['item' => trans('Aliexpress::labels.settings.aliexpress_settings')]
            ))->success();
        } catch (\Exception $exception) {
            log_exception($exception, 'AliexpressSettings', 'savedSettings');
        }

        return redirectTo(url()->previous());
    }

    public function loadCategories(Request $request)
    {
        try {
            $dbSeeder = new AliexpressCategoriesTableSeeder();

            $dbSeeder->run();

            return response()->json([
                'level' => 'success',
                'message' => trans('Aliexpress::labels.load_categories_success'),
            ]);
        } catch (\Exception $exception) {
            return response()->json(['level' => 'error', 'message' => $exception->getMessage()]);
        }
    }
}
