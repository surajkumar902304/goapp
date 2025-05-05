<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToMsubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msubcategories', function (Blueprint $table) {
            $table->string('offer_name')->nullable()->after('msubcat_publish');
            $table->dateTime('start_time')->nullable()->after('offer_name');
            $table->dateTime('end_time')->nullable()->after('start_time');
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
