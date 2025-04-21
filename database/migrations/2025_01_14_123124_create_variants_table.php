<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->bigIncrements('variant_id')->index();
            $table->string('sku');
            $table->bigInteger('qty')->default(10);
            $table->float('price');
            $table->foreignId('product_id')->constrained('products','product_id')->onDelete('cascade');
            $table->foreignId('product_type_id')->constrained('product_types','product_type_id')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('brands','brand_id')->onDelete('cascade');
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
        Schema::dropIfExists('variants');
    }
}
