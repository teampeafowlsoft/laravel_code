<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('group_id');
            $table->integer('lang_id');
            $table->string('name',250)->nullable();
            $table->text('description');
            $table->foreignUuid('category_id')->nullable();
            $table->integer('indicator');
            $table->string('sku',50)->nullable();
            $table->text('tags')->nullable();
            $table->integer('vendor')->nullable();
            $table->string('made_in',100)->nullable();
            $table->string('manufacturer',250)->nullable();
            $table->string('manufacturer_part_no',150)->nullable();
            $table->integer('brand_ids')->nullable();
            $table->integer('weight');
            $table->integer('length');
            $table->integer('width');
            $table->integer('height');
            $table->boolean('return_status')->default(0);
            $table->boolean('promo_status')->default(0);
            $table->boolean('cancelable_status')->default(0);
            $table->string('till_status',25)->nullable();
            $table->text('image');
            $table->text('other_images')->nullable();
            $table->text('videoURL')->nullable();
            $table->text('brochure')->nullable();
            $table->text('seoPageNm')->nullable();
            $table->text('sMetaTitle')->nullable();
            $table->text('sMetaKeywords')->nullable();
            $table->text('sMetaDescription')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
