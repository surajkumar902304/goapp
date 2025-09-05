<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'paid', 'shipped', 'cancelled'])
                  ->default('pending');
            $table->unsignedBigInteger('user_company_address_id');
            $table->unsignedBigInteger('delivery_method_id');
            $table->decimal('vat', 10, 2)->default(0.00);
            $table->decimal('product_total_amount', 10, 2)->default(0.00);
            $table->string('delivery_instructions')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('user_company_address_id')
                ->references('user_company_address_id')
                ->on('user_company_addresses')
                ->onDelete('cascade');
            $table->foreign('delivery_method_id')
                ->references('delivery_method_id')
                ->on('delivery_methods')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
