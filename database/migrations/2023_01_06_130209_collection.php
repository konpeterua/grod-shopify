<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Collection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collects', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('collection_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('position')->nullable();
            $table->text('handle')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('template_suffix')->nullable();
            $table->string('sort_value',16)->nullable();
        });
        Schema::create('collections', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->text('handle')->nullable();
            $table->text('title')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->text('body_html')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('sort_order')->nullable();
            $table->text('template_suffix')->nullable();
            $table->integer('products_count')->nullable();
            $table->string('collection_type',128)->nullable();
            $table->string('published_scope',128)->nullable();
            $table->text('admin_graphql_api_id')->nullable();
        });
        Schema::create('collection_products', function (Blueprint $table) {
            $table->unsignedBigInteger('collection_id');
            $table->unsignedBigInteger('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collects');
        Schema::dropIfExists('collections');
    }
}
