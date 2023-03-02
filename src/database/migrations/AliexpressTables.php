<?php

namespace Corals\Modules\Aliexpress\database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AliexpressTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aliexpress_imports', function (Blueprint $table) {
            $table->string('title');
            $table->increments('id');
            $table->text('keywords');
            $table->integer('image_count');
            $table->integer('max_result_pages');
            $table->unsignedInteger('store_id')->nullable();

            $table->enum('status',
                ['canceled', 'pending', 'in_progress', 'completed', 'failed'])->default('pending')->nullable();
            $table->text('notes')->nullable();

            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('aliexpress_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('integration_id');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedInteger('parent_id')->nullable();

            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('aliexpress_import_product', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('import_id');
            $table->foreign('import_id')
                ->references('id')
                ->on('aliexpress_imports')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique(['product_id', 'import_id'], 'alix_pid_imid_unique');
        });

        Schema::create('aliexpress_category_import', function (Blueprint $table) {
            $table->unsignedInteger('aliexpress_category_id');
            $table->foreign('aliexpress_category_id')
                ->references('id')
                ->on('aliexpress_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedInteger('import_id');

            $table->foreign('import_id')
                ->references('id')
                ->on('aliexpress_imports')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique(['import_id', 'aliexpress_category_id'], 'alix_imid_catid_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aliexpress_category_import');
        Schema::dropIfExists('aliexpress_import_product');
        Schema::dropIfExists('aliexpress_imports');
        Schema::dropIfExists('aliexpress_categories');
    }
}
