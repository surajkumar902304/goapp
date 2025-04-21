<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMvariantDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mvariant_details', function (Blueprint $table) {
            $table->id('mvariant_detail_id');
            $table->json('options')->nullable();
            $table->json('option_value')->nullable();
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
        Schema::dropIfExists('mvariant_details');
    }
}
