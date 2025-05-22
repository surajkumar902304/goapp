<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeExploreDealBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_explore_deal_banners', function (Blueprint $table) {
            $table->id('home_explore_deal_banner_id');
            $table->string('home_explore_deal_banner_name');
            $table->string('home_explore_deal_banner_image');
            $table->integer('home_explore_deal_banner_position')->default(0);
            $table->unsignedBigInteger('main_mcat_id')->nullable();
            $table->unsignedBigInteger('mcat_id')->nullable();
            $table->unsignedBigInteger('msubcat_id')->nullable();
            $table->unsignedBigInteger('mproduct_id')->nullable();
            
            $table->foreign('main_mcat_id')
                ->references('main_mcat_id')
                ->on('main_categories')
                ->onDelete('cascade');
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
        Schema::dropIfExists('home_explore_deal_banners');
    }
}
