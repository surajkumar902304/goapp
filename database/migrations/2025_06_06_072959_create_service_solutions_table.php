<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceSolutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_solutions', function (Blueprint $table) {
            $table->id('service_solution_id');
            $table->string('service_solution_title');
            $table->string('service_solution_sub_title');
            $table->string('service_solution_image');
            $table->string('service_solution_desc');
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
        Schema::dropIfExists('service_solutions');
    }
}
