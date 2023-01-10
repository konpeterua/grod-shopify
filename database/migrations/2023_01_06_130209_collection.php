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

        Schema::create('collections', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();
            $table->text('handle')->nullable();
            $table->text('title')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->text('body_html')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('sort_order')->nullable();
            $table->text('template_suffix')->nullable();
            $table->integer('products_count')->nullable();
            $table->boolean('disjunctive')->nullable();
            $table->string('collection_type',128)->nullable();
            $table->string('published_scope',128)->nullable();
            $table->text('admin_graphql_api_id')->nullable();
        });
        Schema::create('collection_image', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->timestamp('created_at')->nullable();
            $table->text('alt')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->text('src')->nullable();
        });
        Schema::create('collection_rules', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('column',128)->nullable();
            $table->string('relation',128)->nullable();
            $table->text('condition')->nullable();
            $table->unique(['id', 'column','relation','condition']);
        });
        Schema::create('collection_products', function (Blueprint $table) {
            $table->unsignedBigInteger('collection_id');
            $table->unsignedBigInteger('product_id');
            $table->unique(['collection_id', 'product_id']);
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collections');
        Schema::dropIfExists('collection_image');
        Schema::dropIfExists('collection_rules');
        Schema::dropIfExists('collection_products');
    }
}
