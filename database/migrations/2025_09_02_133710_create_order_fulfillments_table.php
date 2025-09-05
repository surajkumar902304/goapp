<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFulfillmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_fulfillments', function (Blueprint $table) {
            $table->bigIncrements('order_fulfillment_id');
            $table->unsignedBigInteger('order_id');
            $table->string('tracking_id')->nullable();
            $table->string('shipping_courier')->nullable();
            $table->timestamp('fulfilled_at')->nullable(); 
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_fulfillments');
    }
}
