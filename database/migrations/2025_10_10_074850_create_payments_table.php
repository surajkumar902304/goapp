<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('payment_id');
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            $table->string('provider')->default('stripe');       
            $table->string('payment_intent_id')->unique();        
            $table->string('payment_method_id')->nullable();      
            $table->string('customer_id')->nullable();            
            $table->string('currency', 10);
            $table->bigInteger('amount')->comment('Amount in smallest unit (e.g. pence)');

            $table->string('status')->index();                    
            $table->string('receipt_email')->nullable();
            $table->string('description')->nullable();

            $table->json('metadata')->nullable();                 
            $table->json('raw_payload')->nullable();              

            $table->timestamps();

            $table->foreign('order_id')
                ->references('order_id')
                ->on('orders')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('payments');
    }
}
