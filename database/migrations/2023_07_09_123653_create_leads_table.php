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
            $table->text('name');
            $table->text('linkedin_profile')->nullable();
            $table->text('title')->nullable();
            $table->text('company');
            $table->text('company_website')->nullable();
            $table->text('location')->nullable();
            $table->text('email');
            $table->integer('leadlist_id')->nullable();
            $table->integer('campaign_id')->nullable();
            $table->longText('website_content')->nullable();
            $table->longText('personalized_line')->nullable();
            $table->text('verified')->nullable();
            $table->boolean('subscribe')->default(1);
            $table->boolean('sent')->default(0);
            $table->boolean('opened')->default(0);
            $table->boolean('replied')->default(0);
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
