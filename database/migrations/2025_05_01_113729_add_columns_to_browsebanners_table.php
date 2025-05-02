<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToBrowsebannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('browsebanners', function (Blueprint $table) {
            $table->unsignedBigInteger('mcat_id')->nullable()->after('browsebanner_position');

            $table->foreign('mcat_id')
                ->references('mcat_id')
                ->on('mcategories')
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
