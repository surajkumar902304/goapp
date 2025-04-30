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
