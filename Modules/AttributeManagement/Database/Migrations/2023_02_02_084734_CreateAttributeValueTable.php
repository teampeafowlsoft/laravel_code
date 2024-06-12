<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributevalues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('group_id');
            $table->integer('lang_id');
            $table->foreignUuid('attribute_id');
            $table->string('attribute_value');
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('attributevalues');
    }
}
