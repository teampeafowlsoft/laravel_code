<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_master', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('group_id');
            $table->string('attribute_name')->unique();
            $table->string('display_name')->unique();
            $table->integer('attribute_field_type_id');
            $table->integer('lang_id');
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
        Schema::dropIfExists('attribute_master');
    }
}
