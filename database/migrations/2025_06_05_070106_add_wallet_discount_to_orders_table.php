<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWalletDiscountToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('wallet_discount', 10, 2)->default(0.00)->after('total_amount');
            $table->decimal('coupon_discount', 10, 2)->default(0.00)->after('wallet_discount');
            $table->enum('fulfillment_status', ['fulfilled', 'unfulfilled'])
                  ->default('unfulfilled')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
