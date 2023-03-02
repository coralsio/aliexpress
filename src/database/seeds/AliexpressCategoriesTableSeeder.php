<?php

namespace Corals\Modules\Aliexpress\database\seeds;

use Corals\Modules\Aliexpress\Models\AliexpressCategory;
use Corals\Modules\Aliexpress\TopSdk\TopSdkManager;
use Corals\Modules\Marketplace\Models\Store;
use Corals\Settings\Facades\Settings;
use Illuminate\Database\Seeder;

class AliexpressCategoriesTableSeeder extends Seeder
{
    public const PRIMARY_CATEGORY = 0;
    public const PRIMARY_CATEGORY_ID = 1;
    public const SUB_CATEGORY = 2;
    public const SUB_CATEGORY_ID = 3;

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $categoriesToImport = [];

        if ($storeId = request()->route('store')) {
            $store = Store::findByHash($storeId);

            $appKey = $store->getSettingValue('aliexpress_api_appKey');
            $appSecret = $store->getSettingValue('aliexpress_api_secretKey');
        } else {
            $appKey = Settings::get('aliexpress_api_appKey');
            $appSecret = Settings::get('aliexpress_api_secretKey');
        }

        if (empty($appKey) || empty($appSecret)) {
            throw new \Exception(trans('Aliexpress::exceptions.app_keys_required'));
        }

        try {
            $topSdkManager = new TopSdkManager($appKey, $appSecret);

            $result = $topSdkManager->categoryGetRequest();

            foreach (data_get($result, 'categories.category', []) as $category) {
                if (empty($category['parent_category_id']) && ! isset($categoriesToImport[$category['category_id']])) {
                    $categoriesToImport[$category['category_id']] = [
                        'name' => $category['category_name'],
                        'integration_id' => $category['category_id'],
                        'sub_categories' => [],
                    ];
                } elseif (! empty($category['parent_category_id'])) {
                    $categoriesToImport[$category['parent_category_id']]['sub_categories'][] = [
                        'name' => $category['category_name'],
                        'integration_id' => $category['category_id'],
                    ];
                }
            }
        } catch (\Exception $exception) {
            report($exception);
        }

        //fallback load from file
        if (empty($categoriesToImport)) {
            $categories = fopen(__DIR__ . '/categories.csv', 'r');

            if ($categories !== false) {
                $row = 0;

                while (($category = fgetcsv($categories)) !== false) {
                    if ($row === 0) {
                        $row++;

                        continue;
                    }

                    if (! isset($categoriesToImport[$category[self::PRIMARY_CATEGORY_ID]])) {
                        $categoriesToImport[$category[self::PRIMARY_CATEGORY_ID]] = [
                            'name' => $category[self::PRIMARY_CATEGORY],
                            'integration_id' => $category[self::PRIMARY_CATEGORY_ID],
                            'sub_categories' => [],
                        ];
                    } elseif (! empty($category[self::SUB_CATEGORY])) {
                        $categoriesToImport[$category[self::PRIMARY_CATEGORY_ID]]['sub_categories'][] = [
                            'name' => $category[self::SUB_CATEGORY],
                            'integration_id' => $category[self::SUB_CATEGORY_ID],
                        ];
                    }

                    $row++;
                }

                fclose($categories);
            }
        }

        foreach ($categoriesToImport as $category) {
            $parentCategory = AliexpressCategory::query()->updateOrCreate([
                'integration_id' => $category['integration_id'],
            ], [
                'name' => $category['name'],
            ]);

            array_map(function ($item) use ($parentCategory) {
                $item['parent_id'] = $parentCategory->id;
                $item['created_at'] = $item['updated_at'] = now();

                AliexpressCategory::query()->updateOrCreate(['integration_id' => $item['integration_id']], $item);

                return $item;
            }, $category['sub_categories']);
        }
    }
}
