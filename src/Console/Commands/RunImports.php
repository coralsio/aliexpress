<?php

namespace Corals\Modules\Aliexpress\Console\Commands;

use Corals\Modules\Aliexpress\Models\Import;
use Corals\Modules\Aliexpress\TopSdk\TopSdkManager;
use Corals\Modules\Marketplace\Models\Store;
use Corals\Settings\Facades\Settings;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RunImports extends Command
{
    protected $signature = 'import:alix';
    protected $description = 'Execute Pending Imports to import aliexpress products';

    protected $categories;

    public function handle()
    {
        return $this->processImports();
    }

    public function processImports()
    {
        $running_import = Import::where('status', 'in_progress')->first();

        if ($running_import) {
            $this->info("There is already running import process ");

            return false;
        }

        $import = Import::pending()->orderBy('created_at', 'asc')->first();

        if (! $import) {
            $this->info("There is no Pending imports");

            return true;
        }

        try {
            $this->info("Running Import: " . $import->title);

            $this->categories = \ImportCategory::whereNotNull('external_id')->pluck('id', 'external_id')->toArray();

            $import->status = 'in_progress';
            $import->notes = '';
            $import->save();

            if ($import->keywords) {
                $keywords = implode(',', $import->keywords);
            } else {
                $keywords = "";
            }

            if ($import->store_id) {
                $store = Store::find($import->store_id);

                $appKey = $store->getSettingValue('aliexpress_api_appKey');
                $appSecret = $store->getSettingValue('aliexpress_api_secretKey');
                $shipToCountry = $store->getSettingValue('aliexpress_api_country');
                $targetCurrency = $store->getSettingValue('aliexpress_api_currency');
                $targetLanguage = $store->getSettingValue('aliexpress_api_language');
            } else {
                $appKey = Settings::get('aliexpress_api_appKey');
                $appSecret = Settings::get('aliexpress_api_secretKey');
                $shipToCountry = Settings::get('aliexpress_api_country');
                $targetCurrency = Settings::get('aliexpress_api_currency');
                $targetLanguage = Settings::get('aliexpress_api_language');
            }

            $topSdkManager = new TopSdkManager($appKey, $appSecret);

            $scan_pages = $import->max_result_pages ?? 1000;

            $categoryIds = implode(',', $import->categories->pluck('integration_id')->toArray());

            $productsQueryParameters = compact('shipToCountry', 'targetCurrency', 'targetLanguage', 'categoryIds');

            for ($i = 1; $i <= $scan_pages; $i++) {
                try {
                    $response = $topSdkManager->productQueryRequest($keywords, $productsQueryParameters, $i);

                    if (empty($response)) {
                        break;
                    }

                    $products = data_get($response, 'products.product', []);

                    if (empty($products)) {
                        break;
                    }

                    $this->parseResponse($import, $products);
                } catch (\Exception $exception) {
                    report($exception);
                    $this->error($exception->getMessage());
                }
            }

            $this->info("Finishing Import: " . $import->title);

            $import->status = 'completed';
            $import->save();
        } catch (\Exception $exception) {
            $errors = [];

            if (! empty($errors)) {
                $error = implode("\n", $errors);
            } else {
                $error = $exception->getMessage();
            }

            $this->error("Error while importing : " . $error);
            $import->notes = $exception->getMessage();
            $import->status = 'failed';
            $import->save();
            log_exception($exception, Import::class, 'import');
        }
    }

    public function parseResponse($import, $products)
    {
        $productService = new \ImportProductService();

        foreach ($products as $product) {
            $productCategories = $this->handleProductCategories($product);

            if ($sku = \ImportSKU::query()->where('code', $product['product_id'])->first()) {
                $productModel = $sku->product;
            }

            $description = '';

            $video = data_get($product, 'product_video_url');

            if (! empty($video)) {
                $description .= '<p><video controls="controls" src="' . $video . '">
                            Your browser does not support the HTML5 Video element.</video></p>';
            }

            if (isset($productModel)) {
                $properties = $productModel->properties;
            } else {
                $properties = [];
            }

            $properties['external_product_dump'] = $product;

            $productData = [
                'name' => $product['product_title'],
                'caption' => $product['product_title'],
                'type' => 'simple',
                'code' => $product['product_id'],
                'regular_price' => $product['target_original_price'],
                'sale_price' => $product['target_sale_price'] ?? 0,
                'allowed_quantity' => '0',
                'inventory' => 'infinite',
                'status' => 'active',
                'categories' => $productCategories,
                'description' => $description,
                'external_url' => $product['promotion_link'],
                'properties' => $properties,
            ];

            if ($import->store_id) {
                $productData['store_id'] = $import->store_id;
            }

            $productRequest = new \ImportProductRequest();

            $productRequest->replace($productData);

            if (isset($productModel)) {
                $productModel->clearMediaCollection($productModel->galleryMediaCollection);
                $productModel = $productService->update($productRequest, $productModel);
            } else {
                $productModel = $productService->store($productRequest, get_class(new \ImportProduct()));
            }

            if ($import->image_count) {
                $this->handleProductImages($import, $product, $productModel);
            }

            $import->products()->syncWithoutDetaching($productModel);

            unset($productModel);
        }
    }

    protected function handleProductImages($import, $product, $productModel)
    {
        try {
            foreach (range(1, $import->image_count) as $i) {
                if ($i == 1) {
                    $productModel->addMediaFromUrl($product['product_main_image_url'])->withCustomProperties([
                        'root' => 'media_import',
                        'featured' => true,
                    ])->toMediaCollection($productModel->galleryMediaCollection);

                    continue;
                }
                $imgURL = data_get($product, 'product_small_image_urls.string.' . ($i - 2));

                if (! $imgURL) {
                    break;
                }
                $imgURL = trim($imgURL, '//');

                $imgURL = 'http://' . $imgURL;

                $productModel->addMediaFromUrl($imgURL)->withCustomProperties([
                    'root' => 'media_import',
                ])->toMediaCollection($productModel->galleryMediaCollection);
            }
        } catch (\Exception $exception) {
            report($exception);
            $this->error($exception->getMessage());
        }
    }

    protected function handleProductCategories($product)
    {
        $firstLevelCategory = [
            'name' => $product['first_level_category_name'],
            'integration_id' => $product['first_level_category_id'],
        ];

        $secondLevelCategory = [
            'name' => $product['second_level_category_name'],
            'integration_id' => $product['second_level_category_id'],
        ];

        if (isset($this->categories[$firstLevelCategory['integration_id']])) {
            $parentCategoryId = $this->categories[$firstLevelCategory['integration_id']];
        } else {
            $parentCategory = new \ImportCategory();
            $parentCategory->external_id = $firstLevelCategory['integration_id'];
            $parentCategory->name = $firstLevelCategory['name'];
            $parentCategory->is_featured = false;
            $parentCategory->slug = Str::slug($firstLevelCategory['name']);
            $parentCategory->save();
            $parentCategoryId = $parentCategory->id;

            $this->categories[$parentCategory->external_id] = $parentCategoryId;
        }

        if (isset($this->categories[$secondLevelCategory['integration_id']])) {
            $category = (object)[
                'id' => $this->categories[$secondLevelCategory['integration_id']],
            ];
        } else {
            $category = new \ImportCategory();

            $category->external_id = $secondLevelCategory['integration_id'];
            $category->name = $secondLevelCategory['name'];
            $category->parent_id = $parentCategoryId;
            $category->is_featured = false;
            $category->slug = Str::slug($secondLevelCategory['name']);
            $category->save();

            $this->categories[$category->external_id] = $category->id;
        }

        return [$parentCategoryId, $category->id];
    }
}
