<?php

namespace Corals\Modules\Aliexpress\database\seeds;

use Illuminate\Database\Seeder;

class AliexpressMenuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $aliexpress_menu_id = \DB::table('menus')->insertGetId([
            'parent_id' => 1,// admin
            'key' => 'aliexpress',
            'url' => null,
            'active_menu_url' => 'aliexpress*',
            'name' => 'Aliexpress',
            'description' => 'Aliexpress Menu Item',
            'icon' => 'fa fa-shopping-cart',
            'target' => null,
            'roles' => '["1","2"]',
            'order' => 0
        ]);

        // seed subscriptions children menu
        \DB::table('menus')->insert([
                [
                    'parent_id' => $aliexpress_menu_id,
                    'key' => null,
                    'url' => config('aliexpress.models.import.resource_url'),
                    'active_menu_url' => config('aliexpress.models.import.resource_url') . '*',
                    'name' => 'Imports',
                    'description' => 'Imports List Menu Item',
                    'icon' => 'fa fa-upload',
                    'target' => null,
                    'roles' => '["1"]',
                    'order' => 0
                ],
            ]
        );
        // seed users children menu
        \DB::table('menus')->insert([
                [
                    'parent_id' => $aliexpress_menu_id,
                    'key' => null,
                    'url' => 'aliexpress/settings',
                    'active_menu_url' => 'aliexpress/settings*',
                    'name' => 'Aliexpress Settings',
                    'description' => 'Aliexpress Settings Menu Item',
                    'icon' => 'fa fa-cog fa-fw',
                    'target' => null,
                    'roles' => '["1"]',
                    'order' => 0
                ],
            ]
        );
    }
}
