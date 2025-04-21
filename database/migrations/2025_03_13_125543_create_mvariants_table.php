<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMvariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mvariants', function (Blueprint $table) {
            $table->id('mvariant_id');
            $table->string('sku');
            $table->string('mvariant_image')->nullable();
            $table->float('price')->nullable();
            $table->float('compare_price')->nullable();
            $table->float('cost_price')->nullable();
            $table->tinyInteger('taxable')->default(0);
            $table->string('barcode')->nullable();
            $table->float('weight')->nullable();
            $table->enum('weightunit',['kg', 'g'])->default('kg');
            $table->tinyInteger('isvalidatedetails')->default(0);
            $table->foreignId('mproduct_id')->constrained('mproducts','mproduct_id')->onDelete('cascade')->nullable();
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
        Schema::dropIfExists('mvariants');
    }
}
