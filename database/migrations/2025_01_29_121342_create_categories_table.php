<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('cat_id');
            $table->string('cat_title');
            $table->string('cat_slug')->unique();
            $table->longText('cat_desc')->nullable();
            $table->string('cat_image')->nullable();
            $table->enum('cat_type', ['manual', 'auto'])->default('manual');
            $table->foreignId('shop_id')->constrained('shops','shop_id')->onDelete('cascade');
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
        Schema::dropIfExists('categories');
    }
}
