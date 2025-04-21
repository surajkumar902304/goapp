<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mproducts', function (Blueprint $table) {
            $table->id('mproduct_id');
            $table->string('mproduct_title');
            $table->string('mproduct_image')->nullable();
            $table->string('mproduct_slug');
            $table->string('mproduct_desc')->nullable();
            $table->enum('status',['Draft', 'Active'])->default('Draft');
            $table->foreignId('mproduct_type_id')->nullable()->constrained('mproduct_types','mproduct_type_id')->onDelete('cascade');
            $table->foreignId('mbrand_id')->nullable()->constrained('mbrands','mbrand_id')->onDelete('cascade');
            $table->json('mtags')->nullable();
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
        Schema::dropIfExists('mproducts');
    }
}
