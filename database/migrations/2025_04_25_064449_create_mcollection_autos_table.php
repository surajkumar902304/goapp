<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMcollectionAutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mcollection_autos', function (Blueprint $table) {
            $table->id('collection_auto_id');
            $table->foreignId('msubcat_id')->constrained('msubcategories','msubcat_id')->onDelete('cascade');
            $table->foreignId('field_id')->constrained('fields','field_id')->onDelete('cascade');
            $table->foreignId('query_id')->constrained('queries','query_id')->onDelete('cascade');
            $table->string('value');
            $table->enum('logical_operator',['all', 'any'])->default('all');
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
        Schema::dropIfExists('mcollection_autos');
    }
}
