<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMainMcatIdToCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('coupons', function (Blueprint $table) {
        $table->unsignedBigInteger('main_mcat_id')->nullable()->after('coupon_id');

        $table->foreign('main_mcat_id')
              ->references('main_mcat_id')
              ->on('main_categories')
              ->onDelete('set null')
              ->onUpdate('cascade');
    });
}

public function down()
{
    Schema::table('coupons', function (Blueprint $table) {
        $table->dropForeign(['main_mcat_id']);
        $table->dropColumn('main_mcat_id');
    });
}

}
