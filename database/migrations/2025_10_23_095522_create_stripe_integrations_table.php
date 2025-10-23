<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_integrations', function (Blueprint $table) {
            $table->id('stripe_integration_id');
            $table->string('provider');
            $table->string('publishable_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->boolean('test_mode')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('note')->nullable();
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
        Schema::dropIfExists('stripe_integrations');
    }
}
