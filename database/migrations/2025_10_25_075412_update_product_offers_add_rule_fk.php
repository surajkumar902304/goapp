<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductOffersAddRuleFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product__offers', function (Blueprint $table) {
            $table->dropColumn('product_offer');
            $table->enum('product_type', ['buy_x_get_y', 'volume_discount'])->nullable();
            $table->integer('buy_qty')->nullable(); 
            $table->integer('get_qty')->nullable(); 
            $table->integer('min_qty')->nullable(); 
            $table->decimal('discount_amount', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
