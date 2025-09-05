<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogicalOperatorToMsubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msubcategories', function (Blueprint $table) {
            $table->enum('logical_operator',['all', 'any'])->default('all')->after('msubcat_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('msubcategories', function (Blueprint $table) {
            //
        });
    }
}
