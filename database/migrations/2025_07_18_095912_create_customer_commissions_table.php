<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_commissions', function (Blueprint $table) {
            $table->id('customer_commission_id');
            $table->unsignedBigInteger('rep_id');
            $table->decimal('total_commission', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('rep_id')->references('rep_id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_commissions');
    }
}
