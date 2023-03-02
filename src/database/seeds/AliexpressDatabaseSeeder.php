<?php

namespace Corals\Modules\Aliexpress\database\seeds;

use Corals\Menu\Models\Menu;
use Corals\Settings\Models\Setting;
use Corals\User\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AliexpressDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AliexpressPermissionsDatabaseSeeder::class);
        $this->call(AliexpressMenuDatabaseSeeder::class);
        $this->call(AliexpressSettingsDatabaseSeeder::class);
    }

    public function rollback()
    {
        Permission::where('name', 'like', 'Aliexpress::%')->delete();

        Menu::where('key', 'aliexpress')
            ->orWhere('active_menu_url', 'like', 'aliexpress%')
            ->orWhere('url', 'like', 'aliexpress%')
            ->delete();

        Setting::where('category', 'Aliexpress')->delete();

        Media::whereIn('collection_name', ['aliexpress-media-collection'])->delete();
    }
}
