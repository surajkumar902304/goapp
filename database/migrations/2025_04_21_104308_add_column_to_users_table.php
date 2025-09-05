<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 15)->after('password');
            $table->string('company_name')->after('mobile');
            $table->string('address1')->after('company_name');
            $table->string('address2')->nullable()->after('address1');
            $table->string('city')->after('address2');
            $table->string('country')->after('city');
            $table->string('postcode')->after('country');
            $table->string('rep_code')->nullable()->after('postcode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //            $table->enum('saleschannel',['Online Store', 'Other'])->default('Online Store')->after('mtags');

        });
    }
}
