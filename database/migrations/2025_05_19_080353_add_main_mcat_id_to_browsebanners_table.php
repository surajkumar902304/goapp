<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMainMcatIdToBrowsebannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('browsebanners', function (Blueprint $table) {
            $table->unsignedBigInteger('main_mcat_id')->nullable()->after('browsebanner_position');
            
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
        Schema::table('browsebanners', function (Blueprint $table) {
            //
        });
    }
}
