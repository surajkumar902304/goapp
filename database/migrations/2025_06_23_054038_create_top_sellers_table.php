<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_sellers', function (Blueprint $table) {
            $table->id('top_seller_id');
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
        Schema::dropIfExists('top_sellers');
    }
}
