<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_products', function (Blueprint $table) {
            $table->id('new_product_id');
            $table->unsignedBigInteger('mvariant_id')->nullable();
            $table->foreign('mvariant_id')
                ->references('mvariant_id')
                ->on('mvariants')
                ->onDelete('cascade');
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
        Schema::dropIfExists('new_products');
    }
}
