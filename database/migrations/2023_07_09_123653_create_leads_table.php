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
            $table->boolean('leadlist_id')->default(0);
            $table->boolean('campaign_id')->default(0);
            $table->text('website_status')->nullable();
            $table->text('website_content')->default("");
            $table->text('personalized_line')->default("");
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
