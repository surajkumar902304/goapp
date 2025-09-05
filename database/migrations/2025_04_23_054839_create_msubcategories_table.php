<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('msubcategories', function (Blueprint $table) {
            $table->id('msubcat_id');
            $table->foreignId('mcat_id')->constrained('mcategories','mcat_id')->onDelete('cascade')->nullable();
            $table->string('msubcat_name');
            $table->string('msubcat_slug')->unique();
            $table->string('msubcat_tag')->nullable();
            $table->string('msubcat_image');
            $table->json('msubcat_publish')->nullable();
            $table->enum('msubcat_type', ['manual', 'smart'])->default('manual');
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
        Schema::dropIfExists('msubcategories');
    }
}
