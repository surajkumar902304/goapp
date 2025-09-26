<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTagPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tag_prices', function (Blueprint $table) {
            $table->id('user_tag_price_id');
            $table->unsignedBigInteger('user_tag_id');
            $table->unsignedBigInteger('mvariant_id');
            $table->float('tag_price');
            $table->timestamps();

            $table->foreign('user_tag_id')
                ->references('user_tag_id')
                ->on('user_tags')
                ->onDelete('cascade');
            $table->foreign('mvariant_id')
                ->references('mvariant_id')
                ->on('mvariants')
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
        Schema::dropIfExists('user_tag_prices');
    }
}
