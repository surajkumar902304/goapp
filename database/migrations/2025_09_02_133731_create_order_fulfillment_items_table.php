<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFulfillmentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_fulfillment_items', function (Blueprint $table) {
            $table->bigIncrements('order_fulfillment_item_id');
            $table->unsignedBigInteger('order_fulfillment_id');
            $table->unsignedBigInteger('order_item_id');
            $table->unsignedInteger('quantity');
            $table->timestamps();

            $table->foreign('order_fulfillment_id')->references('order_fulfillment_id')->on('order_fulfillments')->onDelete('cascade');
            $table->foreign('order_item_id')->references('order_item_id')->on('order_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_fulfillment_items');
    }
}
