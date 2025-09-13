<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_invites', function (Blueprint $table) {
            $table->id('referral_invite_id');
            $table->unsignedBigInteger('sender_user_id');
            $table->string('name');                       
            $table->string('city');                       
            $table->string('email');                      
            $table->string('referral_code')->nullable();  
            $table->timestamps();

            $table->foreign('sender_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_invites');
    }
}
