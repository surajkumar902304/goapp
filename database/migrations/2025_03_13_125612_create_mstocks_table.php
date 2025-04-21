<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mstocks', function (Blueprint $table) {
            $table->id('mstock_id');
            $table->bigInteger('quantity')->nullable();
            $table->foreignId('mlocation_id')->constrained('mlocations','mlocation_id')->onDelete('cascade');
            $table->foreignId('mvariant_id')->constrained('mvariants','mvariant_id')->onDelete('cascade');
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
        Schema::dropIfExists('mstocks');
    }
}
