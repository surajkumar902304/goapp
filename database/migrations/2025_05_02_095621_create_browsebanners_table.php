<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrowsebannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('browsebanners', function (Blueprint $table) {
            $table->id('browsebanner_id');
            $table->string('browsebanner_name');
            $table->string('browsebanner_image');
            $table->integer('browsebanner_position')->default(0);
            $table->unsignedBigInteger('mcat_id')->nullable();
            $table->unsignedBigInteger('msubcat_id')->nullable();
            $table->unsignedBigInteger('mproduct_id')->nullable();

            $table->foreign('mcat_id')
                ->references('mcat_id')
                ->on('mcategories')
                ->onDelete('cascade');
                $table->foreign('msubcat_id')
                ->references('msubcat_id')
                ->on('msubcategories')
                ->onDelete('cascade');
                $table->foreign('mproduct_id')
                ->references('mproduct_id')
                ->on('mproducts')
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
        Schema::dropIfExists('browsebanners');
    }
}
