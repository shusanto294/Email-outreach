<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('apollo_url')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->string('title')->nullable();
            $table->string('company');
            $table->string('company_website')->nullable();
            $table->string('location')->nullable();
            $table->string('email');
            $table->boolean('campaign_id')->default(0);
            $table->boolean('sent')->default(0);
            $table->boolean('opened')->default(0);
            $table->boolean('unsubscribe')->default(0);
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
        Schema::dropIfExists('leads');
    }
};
