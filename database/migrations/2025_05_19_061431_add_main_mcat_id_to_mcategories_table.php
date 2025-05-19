<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMainMcatIdToMcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mcategories', function (Blueprint $table) {
            $table->unsignedBigInteger('main_mcat_id')->nullable()->after('mcat_name');
            
            $table->foreign('main_mcat_id')
                ->references('main_mcat_id')
                ->on('main_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mcategories', function (Blueprint $table) {
            //
        });
    }
}
