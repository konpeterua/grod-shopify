<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Products extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('title',256)->nullable();
            $table->text('body_html')->nullable();
            $table->string('vendor',128)->nullable();
            $table->string('product_type',128)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('handle',128)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('template_suffix',128)->nullable();
            $table->string('status',16)->nullable();
            $table->string('published_scope',16)->nullable();
            $table->text('tags')->nullable();
            $table->string('admin_graphql_api_id',256)->nullable();

        });
        Schema::create('product_variants', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('title',256)->nullable();
            $table->float('price',10,2)->nullable();
            $table->string('sku',128)->nullable();
            $table->integer('position')->unsigned()->nullable();
            $table->string('inventory_policy',64)->nullable();
            $table->float('compare_at_price',10,2)->nullable();
            $table->string('fulfillment_service',128)->nullable();
            $table->string('inventory_management',16)->nullable();
            $table->string('option1',256)->nullable();
            $table->string('option2',256)->nullable();
            $table->string('option3',256)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('taxable')->nullable();
            $table->string('barcode',128)->nullable();
            $table->integer('grams')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->float('weight',10,3)->nullable();
            $table->string('weight_unit',16)->nullable();
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            $table->integer('inventory_quantity')->nullable();
            $table->integer('old_inventory_quantity')->nullable();
            $table->boolean('requires_shipping')->nullable();
            $table->string('admin_graphql_api_id',256)->nullable();
        });    
        Schema::create('product_options', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('product_id');
            $table->string('name',256)->nullable();
            $table->integer('position')->unsigned()->nullable();
        });   
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('product_id');
            $table->string('value',256);
        });   
        Schema::create('product_images', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('position')->unsigned()->nullable();

            $table->text('alt')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('src',256)->nullable();
            $table->string('admin_graphql_api_id',256)->nullable();
        });   
        Schema::create('product_images_variant', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('image_id');
        });   
        Schema::create('product_image', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('position')->unsigned()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->text('alt')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('src',256)->nullable();
            $table->string('admin_graphql_api_id',256)->nullable();
        });   
        Schema::create('product_image_variant', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('image_id');
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
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_images_variant');
        Schema::dropIfExists('product_image');
        Schema::dropIfExists('product_image_variant');
    }
}
