<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_commissions', function (Blueprint $table) {
            $table->id('order_commission_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('rep_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('product_total', 10, 2);
            $table->decimal('commission_percent', 5, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('rep_id')->references('rep_id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_commissions');
    }
}
