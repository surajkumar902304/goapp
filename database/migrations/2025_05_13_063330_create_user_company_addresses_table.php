<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCompanyAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_company_addresses', function (Blueprint $table) {
            $table->id('user_company_address_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_company_name');
            $table->string('company_address1');
            $table->string('company_address2')->nullable();
            $table->string('company_city');
            $table->string('company_country');
            $table->string('company_postcode');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
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
        Schema::dropIfExists('user_company_addresses');
    }
}
